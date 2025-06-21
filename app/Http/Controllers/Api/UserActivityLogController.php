<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivityLogController extends Controller
{
    public function getAddedUsersBySuperAdmin()
    {
        // Middleware already ensures super_admin role
        $superAdminId = Auth::id();
        if (!$superAdminId) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $allAddedUsersLogs = UserActivityLog::where('action', 'added')
            ->with('superAdmin:id,name,email', 'user:id,name,email') // Eager load superAdmin and user details
            ->get();

        $groupedBySuperAdmin = $allAddedUsersLogs->groupBy('super_admin_id');

        $result = $groupedBySuperAdmin->map(function ($logs, $superAdminId) {
            $superAdmin = $logs->first()->superAdmin;
            $addedUsers = $logs->map(function ($log) {
                // Load the user's current roles to get the current role name
                $user = User::find($log->user_id);
                $currentRole = $user && $user->roles->first() ? $user->roles->first()->name : null;

                return [
                    'user_id' => $log->user->id,
                    'user_name' => $log->user->name,
                    'user_email' => $log->user->email,
                    'current_role' => $currentRole,
                    'added_at' => $log->created_at->toDateTimeString(),
                ];
            });

            return [
                'super_admin_id' => $superAdmin->id,
                'super_admin_name' => $superAdmin->name,
                'super_admin_email' => $superAdmin->email,
                'added_users' => $addedUsers,
            ];
        })->values(); // Reset keys to be a simple array

        return response()->json($result);
    }

    public function getUserRoleHistory(User $user)
    {
        // Middleware already ensures super_admin role
        $superAdminId = Auth::id();
        if (!$superAdminId) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $roleHistory = UserActivityLog::where('user_id', $user->id)
            ->whereIn('action', ['added', 'role_changed'])
            ->with('superAdmin:id,name') // Load super admin who performed the action
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($log) {
                $entry = [
                    'action' => $log->action,
                    'date' => $log->created_at->toDateTimeString(),
                    'performed_by_super_admin_id' => $log->super_admin_id,
                    'performed_by_super_admin_name' => $log->superAdmin ? $log->superAdmin->name : 'N/A',
                ];

                if ($log->action === 'added') {
                    $entry['role'] = $log->new_role;
                } elseif ($log->action === 'role_changed') {
                    $entry['old_role'] = $log->old_role;
                    $entry['new_role'] = $log->new_role;
                }
                return $entry;
            });

        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'history' => $roleHistory,
        ]);
    }

    /**
     * Display all activity logs for a specific super_admin.
     */
    public function getSuperAdminActivityLog(User $superAdmin)
    {
        // Ensure the authenticated user is a super_admin and has access to view logs
        // The 'role:super_admin' middleware on the route will handle the primary authorization.
        // We also check if the provided $superAdmin user actually has the super_admin role.
        if (!$superAdmin->hasRole('super_admin')) {
            return response()->json(['message' => 'The specified user is not a super admin.'], 404);
        }

        $activityLogs = UserActivityLog::where('super_admin_id', $superAdmin->id)
            ->with('user:id,name,email') // Eager load the user who was affected by the action
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $entry = [
                    'log_id' => $log->id,
                    'action' => $log->action,
                    'date' => $log->created_at->toDateTimeString(),
                    'affected_user_id' => $log->user_id,
                    'affected_user_name' => $log->user ? $log->user->name : 'N/A',
                    'affected_user_email' => $log->user ? $log->user->email : 'N/A',
                    'affected_user_previous_value' => null, // Initialize with null
                ];

                if ($log->action === 'added') {
                    $entry['new_role'] = $log->new_role;
                } elseif ($log->action === 'role_changed') {
                    $entry['old_role'] = $log->old_role;
                    $entry['new_role'] = $log->new_role;
                } elseif ($log->action === 'deleted') {
                    $entry['old_role'] = $log->old_role; // The role the user had before deletion
                } elseif ($log->action === 'updated_details') {
                    $changes = json_decode($log->details, true); // Decode the JSON string
                    $entry['changes'] = $changes;

                    // Add previous value for name, email, or password if available
                    $previousValue = null;
                    $fieldsToCheck = ['name', 'email', 'password'];

                    foreach ($fieldsToCheck as $field) {
                        if (isset($changes[$field]['old'])) {
                            $previousValue = ($field === 'password') ? 'hashed' : $changes[$field]['old'];
                            break; // Found the first relevant old value, exit loop
                        }
                    }
                    $entry['affected_user_previous_value'] = $previousValue;
                }
                return $entry;
            });

        return response()->json([
            'super_admin_id' => $superAdmin->id,
            'super_admin_name' => $superAdmin->name,
            'super_admin_email' => $superAdmin->email,
            'activity_log' => $activityLogs,
        ]);
    }
}
