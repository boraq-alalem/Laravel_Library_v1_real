<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /**
     * List roles for a specific user.
     */
    public function index(User $user)
    {
        return response()->json($user->roles);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role);
            return response()->json(['message' => 'Role assigned successfully.'], 200);
        }

        return response()->json(['message' => 'User already has this role.'], 409);
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(User $user, Role $role)
    {
        if ($user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->detach($role);
            return response()->json(['message' => 'Role removed successfully.'], 200);
        }

        return response()->json(['message' => 'User does not have this role.'], 404);
    }
}
