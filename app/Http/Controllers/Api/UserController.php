<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * Update a user's role.
     */
    public function updateUserRole(Request $request, User $user)
    {
        // Ensure only super_admin can perform this action
        if (!auth()->user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::find($request->role_id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $user->roles()->sync([$role->id]);

        return response()->json(['message' => 'User role updated successfully', 'user' => $user->load('roles')]);
    }
}
