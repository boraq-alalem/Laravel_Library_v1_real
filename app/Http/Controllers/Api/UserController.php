<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Add this line

class UserController extends Controller
{
    /**
     * Display a listing of the users with their permissions.
     */
    public function index()
    {
        $users = User::with('roles.permissions')->get();

        return response()->json($users);
    }

    /**
     * Display a listing of the users, excluding super_admin.
     */
    public function indexWithoutSuperAdmin()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->with(['roles' => function ($query) {
            $query->select('roles.id', 'roles.name');
        }, 'roles.permissions'])->get();

        $formattedUsers = $users->map(function ($user) {
            $permissions = $user->roles->flatMap(function ($role) {
                return $role->permissions->map(function ($permission) {
                    return ['id' => $permission->id, 'name' => $permission->name];
                });
            })->unique()->values()->all();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'roles' => $user->roles->map(function ($role) {
                    return ['id' => $role->id, 'name' => $role->name];
                }),
                'permissions' => $permissions,
            ];
        });

        return response()->json($formattedUsers);
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Consider using Hash::make()
        ]);

        // You might want to return a token or user data here
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $role = Role::find($request->role_id);
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        $superAdminId = Auth::id();
        if ($superAdminId) { // Middleware already ensures super_admin role
            UserActivityLog::create([
                'super_admin_id' => $superAdminId,
                'user_id' => $user->id,
                'action' => 'added',
                'new_role' => $role ? $role->name : null,
            ]);
        }

        return response()->json(['message' => 'User created successfully', 'user' => $user->load('roles')], 201);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'role_id' => 'sometimes|required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalUser = $user->getOriginal(); // Get original user attributes before modifications

        $user->fill($request->only(['name', 'email']));

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $superAdminId = Auth::id();
        if ($superAdminId) { // Middleware already ensures super_admin role
            $changes = [];

            // Check for changes in name, email, and password
            if ($request->has('name')) {
                $changes['name'] = ['old' => $originalUser['name'], 'new' => $request->name];
            }
            if ($request->has('email')) {
                $changes['email'] = ['old' => $originalUser['email'], 'new' => $request->email];
            }
            if ($request->has('password')) { // Password is always hashed, so just check if it was provided
                $changes['password'] = ['old' => 'hashed', 'new' => 'hashed']; // Don't log actual passwords
            }

            // Always create a log entry if any of the fields were present in the request
            if ($request->has('name') || $request->has('email') || $request->has('password')) {
                UserActivityLog::create([
                    'super_admin_id' => $superAdminId,
                    'user_id' => $user->id,
                    'action' => 'updated_details',
                    'details' => json_encode($changes), // Store changes as JSON
                ]);
            }

            if ($request->has('role_id')) {
                // Ensure roles are loaded before accessing them
                $user->loadMissing('roles');
                $oldRole = $user->roles->first() ? $user->roles->first()->name : null;
                $newRole = Role::find($request->role_id);

                if ($newRole && $oldRole !== $newRole->name) {
                    UserActivityLog::create([
                        'super_admin_id' => $superAdminId,
                        'user_id' => $user->id,
                        'action' => 'role_changed',
                        'old_role' => $oldRole,
                        'new_role' => $newRole->name,
                    ]);
                }
            }
        }

        if ($request->has('role_id')) {
            $role = Role::find($request->role_id);
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user->load('roles')]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the super_admin user itself if it's the only one
        if ($user->hasRole('super_admin') && User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->count() === 1) {
            return response()->json(['message' => 'Cannot delete the last super admin user.'], 403);
        }

        $superAdminId = Auth::id();
        if ($superAdminId) { // Middleware already ensures super_admin role
            // Ensure roles are loaded before accessing them
            $user->loadMissing('roles');
            UserActivityLog::create([
                'super_admin_id' => $superAdminId,
                'user_id' => $user->id,
                'action' => 'deleted',
                'old_role' => $user->roles->first() ? $user->roles->first()->name : null,
            ]);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    /**
     * Authenticate a user and return an API token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        // Load roles and their permissions
        $user->load('roles.permissions');

        // Flatten permissions for easier frontend consumption
        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        })->unique()->values()->all();

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles,
                'permissions' => $permissions,
            ]
        ]);
    }

}
