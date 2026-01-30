<?php

namespace App\Http\Controllers;

use App\Data\UserData;
use App\Data\UserInvitationData;
use App\Http\Requests\User\AssignRolesRequest;
use App\Http\Requests\User\AssignSitesRequest;
use App\Http\Requests\User\BulkAssignRolesRequest;
use App\Http\Requests\User\BulkInviteUsersRequest;
use App\Http\Requests\User\BulkToggleStatusRequest;
use App\Http\Requests\User\InviteUserRequest;
use App\Http\Requests\User\ResetUserPasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Mail\PasswordResetMail;
use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a paginated list of users (scoped by permission).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Determine scope based on permissions
        $query = User::with(['primarySite', 'roles', 'sites'])
            ->withCount('roles');

        if ($user->can('users.view.all')) {
            // No filtering needed - see all users
        } elseif ($user->can('users.view.site')) {
            // Filter to user's accessible sites
            $siteIds = $user->sites->pluck('id')->toArray();
            $query->where(function ($q) use ($siteIds) {
                $q->whereIn('primary_site_id', $siteIds)
                    ->orWhereHas('sites', fn ($q) => $q->whereIn('sites.id', $siteIds));
            });
        } else {
            abort(403);
        }

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('role_id')) {
            $query->whereHas('roles', fn ($q) => $q->where('roles.id', $request->input('role_id')));
        }

        if ($request->has('site_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('primary_site_id', $request->input('site_id'))
                    ->orWhereHas('sites', fn ($q) => $q->where('sites.id', $request->input('site_id')));
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->input('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->map(fn ($user) => UserData::fromModel($user))->all(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Show a specific user.
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        $user->load(['primarySite', 'roles', 'sites']);

        return response()->json([
            'data' => UserData::fromModel($user),
        ]);
    }

    /**
     * Send invitation to create a new user.
     */
    public function invite(InviteUserRequest $request): JsonResponse
    {
        $invitation = UserInvitation::create([
            'email' => $request->email,
            'full_name' => $request->full_name,
            'token' => Str::random(64),
            'invited_by' => $request->user()->id,
            'primary_site_id' => $request->primary_site_id,
            'additional_site_ids' => $request->input('additional_site_ids', []),
            'role_ids' => $request->role_ids,
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        // Log activity
        activity()
            ->causedBy($request->user())
            ->withProperties([
                'email' => $invitation->email,
                'full_name' => $invitation->full_name,
                'primary_site_id' => $invitation->primary_site_id,
            ])
            ->log('User invitation sent');

        $invitation->load(['invitedBy', 'primarySite']);

        return response()->json([
            'message' => 'Invitation sent successfully',
            'data' => UserInvitationData::fromModel($invitation),
        ], 201);
    }

    /**
     * Update user details.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->withProperties($request->validated())
            ->log('User updated');

        return response()->json([
            'message' => 'User updated successfully',
            'data' => UserData::fromModel($user->fresh(['primarySite', 'roles', 'sites'])),
        ]);
    }

    /**
     * Toggle user active status (soft activation/deactivation).
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $this->authorize('deactivate', $user);

        $user->is_active = ! $user->is_active;
        $user->save();

        $action = $user->is_active ? 'activated' : 'deactivated';

        activity()
            ->causedBy(request()->user())
            ->performedOn($user)
            ->log("User {$action}");

        return response()->json([
            'message' => "User {$action} successfully",
            'data' => UserData::fromModel($user->fresh(['primarySite', 'roles', 'sites'])),
        ]);
    }

    /**
     * Assign roles to user.
     */
    public function assignRoles(AssignRolesRequest $request, User $user): JsonResponse
    {
        $user->syncRoles(Role::whereIn('id', $request->role_ids)->pluck('name'));

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->withProperties(['role_ids' => $request->role_ids])
            ->log('User roles updated');

        return response()->json([
            'message' => 'Roles assigned successfully',
            'data' => UserData::fromModel($user->fresh(['primarySite', 'roles', 'sites'])),
        ]);
    }

    /**
     * Assign sites to user.
     */
    public function assignSites(AssignSitesRequest $request, User $user): JsonResponse
    {
        // Update primary site
        $user->primary_site_id = $request->primary_site_id;
        $user->save();

        // Sync sites with granted_at and granted_by
        $syncData = [];
        foreach ($request->site_ids as $siteId) {
            $syncData[$siteId] = [
                'granted_at' => now(),
                'granted_by' => $request->user()->id,
            ];
        }

        $user->sites()->sync($syncData);

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->withProperties([
                'site_ids' => $request->site_ids,
                'primary_site_id' => $request->primary_site_id,
            ])
            ->log('User sites updated');

        return response()->json([
            'message' => 'Sites assigned successfully',
            'data' => UserData::fromModel($user->fresh(['primarySite', 'roles', 'sites'])),
        ]);
    }

    /**
     * Reset user password (generate token and send email).
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user): JsonResponse
    {
        $this->authorize('resetPassword', $user);

        // Generate password reset token
        $token = Password::createToken($user);

        // Optionally force password change on next login
        if ($request->boolean('must_change_password', true)) {
            $user->must_change_password = true;
            $user->save();
        }

        // Send password reset email
        Mail::to($user->email)->send(new PasswordResetMail($user, $token));

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Password reset initiated');

        return response()->json([
            'message' => 'Password reset email sent successfully',
        ]);
    }

    /**
     * Get pending invitations.
     */
    public function invitations(Request $request): JsonResponse
    {
        $invitations = UserInvitation::with(['invitedBy', 'primarySite'])
            ->whereNull('accepted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => $invitations->map(fn ($inv) => UserInvitationData::fromModel($inv))->all(),
            'meta' => [
                'current_page' => $invitations->currentPage(),
                'last_page' => $invitations->lastPage(),
                'per_page' => $invitations->perPage(),
                'total' => $invitations->total(),
            ],
        ]);
    }

    /**
     * Resend invitation.
     */
    public function resendInvitation(UserInvitation $invitation): JsonResponse
    {
        if ($invitation->isAccepted()) {
            return response()->json([
                'message' => 'Invitation has already been accepted',
            ], 422);
        }

        // Extend expiration
        $invitation->expires_at = now()->addDays(7);
        $invitation->save();

        // Resend email
        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        return response()->json([
            'message' => 'Invitation resent successfully',
        ]);
    }

    /**
     * Cancel/delete invitation.
     */
    public function cancelInvitation(UserInvitation $invitation): JsonResponse
    {
        if ($invitation->isAccepted()) {
            return response()->json([
                'message' => 'Cannot cancel accepted invitation',
            ], 422);
        }

        $invitation->delete();

        return response()->json([
            'message' => 'Invitation cancelled successfully',
        ]);
    }

    /**
     * Bulk invite users.
     */
    public function bulkInvite(BulkInviteUsersRequest $request): JsonResponse
    {
        $successCount = 0;
        $errors = [];

        foreach ($request->users as $index => $userData) {
            try {
                $invitation = UserInvitation::create([
                    'email' => $userData['email'],
                    'full_name' => $userData['full_name'],
                    'token' => Str::random(64),
                    'invited_by' => $request->user()->id,
                    'primary_site_id' => $userData['primary_site_id'],
                    'additional_site_ids' => $userData['additional_site_ids'] ?? [],
                    'role_ids' => $userData['role_ids'],
                    'expires_at' => now()->addDays(7),
                ]);

                Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $index + 1,
                    'email' => $userData['email'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        activity()
            ->causedBy($request->user())
            ->withProperties([
                'success_count' => $successCount,
                'error_count' => count($errors),
            ])
            ->log('Bulk user invitations sent');

        return response()->json([
            'message' => "Successfully invited {$successCount} user(s)",
            'success_count' => $successCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Bulk toggle status (activate/deactivate).
     */
    public function bulkToggleStatus(BulkToggleStatusRequest $request): JsonResponse
    {
        $updatedCount = User::whereIn('id', $request->user_ids)
            ->where('id', '!=', $request->user()->id) // Prevent self-deactivation
            ->update(['is_active' => $request->is_active]);

        $action = $request->is_active ? 'activated' : 'deactivated';

        activity()
            ->causedBy($request->user())
            ->withProperties([
                'user_ids' => $request->user_ids,
                'is_active' => $request->is_active,
                'updated_count' => $updatedCount,
            ])
            ->log("Bulk users {$action}");

        return response()->json([
            'message' => "Successfully {$action} {$updatedCount} user(s)",
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Bulk assign roles.
     */
    public function bulkAssignRoles(BulkAssignRolesRequest $request): JsonResponse
    {
        $roleNames = Role::whereIn('id', $request->role_ids)->pluck('name');
        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            $user->syncRoles($roleNames);
        }

        activity()
            ->causedBy($request->user())
            ->withProperties([
                'user_ids' => $request->user_ids,
                'role_ids' => $request->role_ids,
            ])
            ->log('Bulk roles assigned');

        return response()->json([
            'message' => "Successfully assigned roles to {$users->count()} user(s)",
            'updated_count' => $users->count(),
        ]);
    }
}
