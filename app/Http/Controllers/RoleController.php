<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\SyncPermissionsRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles with metadata.
     */
    public function index(): JsonResponse
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $roles,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        $role->load('permissions');
        $role->loadCount(['permissions', 'users']);

        return response()->json([
            'data' => $role,
            'message' => 'Role created successfully',
        ], 201);
    }

    /**
     * Display the specified role with permissions.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');

        return response()->json([
            'data' => $role,
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'data' => $role,
            'message' => 'Role updated successfully',
        ]);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        // Check if role has users
        $usersCount = $role->users()->count();

        if ($usersCount > 0) {
            return response()->json([
                'message' => "Cannot delete role. {$usersCount} user(s) are assigned to this role.",
                'users_count' => $usersCount,
            ], 422);
        }

        // Check if role is system-critical
        $systemRoles = ['Manager', 'AVP', 'Farm Admin'];

        if (in_array($role->name, $systemRoles)) {
            return response()->json([
                'message' => 'Cannot delete system-critical role.',
            ], 403);
        }

        $roleName = $role->name;
        $role->delete();

        return response()->json([
            'message' => "Role '{$roleName}' deleted successfully",
        ]);
    }

    /**
     * Sync permissions for the specified role.
     */
    public function syncPermissions(SyncPermissionsRequest $request, Role $role): JsonResponse
    {
        // Sync permissions
        $role->syncPermissions($request->permissions);

        // Reload permissions
        $role->load('permissions');

        return response()->json([
            'data' => $role,
            'message' => 'Permissions updated successfully',
        ]);
    }

    /**
     * Get all available permissions.
     */
    public function allPermissions(): JsonResponse
    {
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'data' => $permissions,
        ]);
    }
}
