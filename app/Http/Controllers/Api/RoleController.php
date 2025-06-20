<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display the permissions for a specific role.
     */
    public function showPermissions(Role $role)
    {
        $role->load('permissions');

        return response()->json($role->permissions);
    }

    /**
     * Display a listing of roles with their permissions.
     */
    public function indexWithPermissions()
    {
        $roles = Role::with('permissions')->get();

        return response()->json($roles);
    }
}
