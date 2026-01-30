<?php

use App\Models\Site;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
    $this->roRole = Role::firstOrCreate(['name' => 'RO', 'guard_name' => 'web']);
    $this->site = Site::factory()->create();

    $this->manager = User::factory()->create();
    $this->manager->assignRole($this->managerRole);
});

it('invitation expires after 7 days', function () {
    $invitation = UserInvitation::factory()->create([
        'expires_at' => now()->subDays(8),
    ]);

    expect($invitation->isExpired())->toBeTrue();
});

it('invitation is not expired within 7 days', function () {
    $invitation = UserInvitation::factory()->create([
        'expires_at' => now()->addDays(5),
    ]);

    expect($invitation->isExpired())->toBeFalse();
});

it('can resend invitation and it extends expiry', function () {
    Mail::fake();

    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(1),
    ]);

    $oldExpiry = $invitation->expires_at;

    actingAs($this->manager)
        ->postJson("/api/users/invitations/{$invitation->id}/resend")
        ->assertSuccessful();

    $invitation->refresh();
    expect($invitation->expires_at->greaterThan($oldExpiry))->toBeTrue();

    Mail::assertSent(\App\Mail\UserInvitationMail::class);
});

it('can cancel pending invitation', function () {
    $invitation = UserInvitation::factory()->create([
        'accepted_at' => null,
    ]);

    actingAs($this->manager)
        ->deleteJson("/api/users/invitations/{$invitation->id}")
        ->assertSuccessful();

    expect(UserInvitation::find($invitation->id))->toBeNull();
});

it('accepting invitation creates user with correct roles and sites', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'full_name' => 'New User',
        'primary_site_id' => $this->site->id,
        'role_ids' => [$this->roRole->id],
        'additional_site_ids' => [],
        'token' => Str::random(64),
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->postJson("/invitation/{$invitation->token}", [
        'password' => 'NewPassword123',
        'password_confirmation' => 'NewPassword123',
    ]);

    $response->assertRedirect();

    assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'full_name' => 'New User',
        'primary_site_id' => $this->site->id,
    ]);

    $user = User::where('email', 'newuser@example.com')->first();
    expect($user->hasRole($this->roRole))->toBeTrue();
    expect($user->sites->pluck('id')->toArray())->toContain($this->site->id);

    $invitation->refresh();
    expect($invitation->accepted_at)->not->toBeNull();
});

it('expired invitation cannot be accepted', function () {
    $invitation = UserInvitation::factory()->create([
        'token' => Str::random(64),
        'expires_at' => now()->subDays(1),
    ]);

    $this->get("/invitation/{$invitation->token}")
        ->assertInertia(fn ($page) => $page->component('auth/invitation-expired'));
});

it('already accepted invitation shows appropriate page', function () {
    $invitation = UserInvitation::factory()->create([
        'token' => Str::random(64),
        'accepted_at' => now(),
    ]);

    $this->get("/invitation/{$invitation->token}")
        ->assertInertia(fn ($page) => $page->component('auth/invitation-already-accepted'));
});
