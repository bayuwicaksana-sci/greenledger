<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class AcceptInvitationController extends Controller
{
    /**
     * Show invitation acceptance page.
     */
    public function show(string $token): Response
    {
        $invitation = UserInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired()) {
            return Inertia::render('auth/invitation-expired', [
                'email' => $invitation->email,
            ]);
        }

        if ($invitation->isAccepted()) {
            return Inertia::render('auth/invitation-already-accepted');
        }

        return Inertia::render('auth/accept-invitation', [
            'invitation' => [
                'email' => $invitation->email,
                'full_name' => $invitation->full_name,
                'token' => $invitation->token,
            ],
        ]);
    }

    /**
     * Accept invitation and create user account.
     */
    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = UserInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired() || $invitation->isAccepted()) {
            abort(422, 'Invalid or expired invitation');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create user
        $user = User::create([
            'name' => $invitation->full_name,
            'full_name' => $invitation->full_name,
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
            'primary_site_id' => $invitation->primary_site_id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign roles
        if ($invitation->role_ids) {
            $user->syncRoles(Role::whereIn('id', $invitation->role_ids)->pluck('name'));
        }

        // Assign sites
        $siteIds = array_merge(
            [$invitation->primary_site_id],
            $invitation->additional_site_ids ?? []
        );

        $syncData = [];
        foreach (array_unique($siteIds) as $siteId) {
            $syncData[$siteId] = [
                'granted_at' => now(),
                'granted_by' => $invitation->invited_by,
            ];
        }

        $user->sites()->sync($syncData);

        // Mark invitation as accepted
        $invitation->accepted_at = now();
        $invitation->save();

        // Log activity
        activity()
            ->causedBy($user)
            ->withProperties(['invitation_id' => $invitation->id])
            ->log('User account created via invitation');

        // Log in the user
        Auth::login($user);

        return redirect()->route('dashboard', ['site' => $user->primarySite->site_code]);
    }
}
