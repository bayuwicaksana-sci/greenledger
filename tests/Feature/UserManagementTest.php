<?php

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    // Create permissions
    $permissions = [
        'users.view.all',
        'users.create',
        'users.update',
        'users.deactivate',
        'users.assign-roles',
        'users.assign-sites',
        'users.reset-password',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    // Create roles
    $this->managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
    $this->roRole = Role::firstOrCreate(['name' => 'RO', 'guard_name' => 'web']);

    // Give Manager all user management permissions
    $this->managerRole->givePermissionTo($permissions);

    // Create sites
    $this->site1 = Site::factory()->create(['site_name' => 'Jakarta', 'site_code' => 'JKT']);
    $this->site2 = Site::factory()->create(['site_name' => 'Bandung', 'site_code' => 'BDG']);

    // Create Manager user (UserFactory already attaches primary_site)
    $this->manager = User::factory()->create([
        'primary_site_id' => $this->site1->id,
        'is_active' => true,
    ]);
    $this->manager->assignRole($this->managerRole);
});

it('manager can invite user', function () {
    Mail::fake();

    $response = actingAs($this->manager)
        ->postJson('/api/users/invite', [
            'email' => 'newuser@example.com',
            'full_name' => 'New User',
            'primary_site_id' => $this->site1->id,
            'role_ids' => [$this->roRole->id],
        ]);

    $response->assertCreated();

    assertDatabaseHas('user_invitations', [
        'email' => 'newuser@example.com',
        'full_name' => 'New User',
        'primary_site_id' => $this->site1->id,
    ]);

    Mail::assertSent(\App\Mail\UserInvitationMail::class);
});

it('cannot invite with existing email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    actingAs($this->manager)
        ->postJson('/api/users/invite', [
            'email' => 'existing@example.com',
            'full_name' => 'Duplicate User',
            'primary_site_id' => $this->site1->id,
            'role_ids' => [$this->roRole->id],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('manager can update user details', function () {
    $user = User::factory()->create(['name' => 'old_name']);

    actingAs($this->manager)
        ->putJson("/api/users/{$user->id}", [
            'name' => 'new_name',
            'email' => $user->email,
            'full_name' => 'New Full Name',
            'primary_site_id' => $this->site1->id,
            'is_active' => true,
        ])
        ->assertSuccessful();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'new_name',
        'full_name' => 'New Full Name',
    ]);
});

it('manager can toggle user status', function () {
    $user = User::factory()->create(['is_active' => true]);

    actingAs($this->manager)
        ->postJson("/api/users/{$user->id}/toggle-status")
        ->assertSuccessful();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => false,
    ]);
});

it('cannot deactivate self', function () {
    actingAs($this->manager)
        ->postJson("/api/users/{$this->manager->id}/toggle-status")
        ->assertForbidden();
});

it('manager can assign roles to user', function () {
    $user = User::factory()->create();

    actingAs($this->manager)
        ->putJson("/api/users/{$user->id}/roles", [
            'role_ids' => [$this->roRole->id],
        ])
        ->assertSuccessful();

    expect($user->fresh()->roles->pluck('id')->toArray())
        ->toContain($this->roRole->id);
});

it('manager can assign sites to user', function () {
    $user = User::factory()->create();

    actingAs($this->manager)
        ->putJson("/api/users/{$user->id}/sites", [
            'site_ids' => [$this->site1->id, $this->site2->id],
            'primary_site_id' => $this->site1->id,
        ])
        ->assertSuccessful();

    expect($user->fresh()->sites->pluck('id')->toArray())
        ->toContain($this->site1->id, $this->site2->id);
});

it('primary site must be in assigned sites', function () {
    $user = User::factory()->create();

    actingAs($this->manager)
        ->putJson("/api/users/{$user->id}/sites", [
            'site_ids' => [$this->site1->id],
            'primary_site_id' => $this->site2->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('primary_site_id');
});

it('manager can reset user password', function () {
    Mail::fake();

    $user = User::factory()->create();

    actingAs($this->manager)
        ->postJson("/api/users/{$user->id}/reset-password", [
            'must_change_password' => true,
        ])
        ->assertSuccessful();

    Mail::assertSent(\App\Mail\PasswordResetMail::class);
});

it('cannot reset own password via admin function', function () {
    actingAs($this->manager)
        ->postJson("/api/users/{$this->manager->id}/reset-password")
        ->assertForbidden();
});
