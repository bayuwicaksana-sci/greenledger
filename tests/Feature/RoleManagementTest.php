<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create necessary permissions for testing
    Permission::firstOrCreate(['name' => 'users.assign-roles']);
    Permission::firstOrCreate(['name' => 'programs.view.all']);
    Permission::firstOrCreate(['name' => 'programs.create.site']);
    Permission::firstOrCreate(['name' => 'programs.update.site']);
});

// Authorization Tests
test('guests cannot access role endpoints', function () {
    $this->getJson('/api/roles')->assertUnauthorized();
});

test('users without permission cannot access role endpoints', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/roles')
        ->assertForbidden();
});

test('authorized users can list all permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $response = $this->actingAs($user)
        ->getJson('/api/permissions')
        ->assertOk();

    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThan(0);

    // Check structure of first permission
    $firstPermission = $response->json('data.0');
    expect($firstPermission)->toHaveKeys(['id', 'name']);
});

// List Roles Tests
test('authorized users can list all roles', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role1 = Role::create(['name' => 'Test Role 1']);
    $role2 = Role::create(['name' => 'Test Role 2']);

    $response = $this->actingAs($user)
        ->getJson('/api/roles')
        ->assertOk();

    expect($response->json('data'))->toBeArray();
    expect(collect($response->json('data'))->pluck('name')->toArray())
        ->toContain('Test Role 1', 'Test Role 2');
});

test('role list includes counts', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Test Role']);
    $role->givePermissionTo('programs.view.all');

    $roleUser = User::factory()->create();
    $roleUser->assignRole($role);

    $response = $this->actingAs($user)
        ->getJson('/api/roles')
        ->assertOk();

    $roleData = collect($response->json('data'))->firstWhere('name', 'Test Role');
    expect($roleData['permissions_count'])->toBe(1);
    expect($roleData['users_count'])->toBe(1);
});

// Create Role Tests
test('authorized users can create a new role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $response = $this->actingAs($user)
        ->postJson('/api/roles', [
            'name' => 'New Role',
        ])
        ->assertCreated();

    expect($response->json('data.name'))->toBe('New Role');
    expect(Role::where('name', 'New Role')->exists())->toBeTrue();
});

test('can create role with initial permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $response = $this->actingAs($user)
        ->postJson('/api/roles', [
            'name' => 'Role With Permissions',
            'permissions' => ['programs.view.all', 'programs.create.site'],
        ])
        ->assertCreated();

    $role = Role::findByName('Role With Permissions');
    expect($role->permissions->pluck('name')->toArray())
        ->toContain('programs.view.all', 'programs.create.site');
});

test('role name is required when creating', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $this->actingAs($user)
        ->postJson('/api/roles', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('role name must be unique when creating', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    Role::create(['name' => 'Existing Role']);

    $this->actingAs($user)
        ->postJson('/api/roles', ['name' => 'Existing Role'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('permissions must exist when creating role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $this->actingAs($user)
        ->postJson('/api/roles', [
            'name' => 'New Role',
            'permissions' => ['nonexistent.permission'],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions.0']);
});

// Show Role Tests
test('authorized users can view a role with permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Test Role']);
    $role->givePermissionTo(['programs.view.all', 'programs.create.site']);

    $response = $this->actingAs($user)
        ->getJson("/api/roles/{$role->id}")
        ->assertOk();

    expect($response->json('data.name'))->toBe('Test Role');
    expect($response->json('data.permissions'))->toBeArray();
    expect(collect($response->json('data.permissions'))->pluck('name')->toArray())
        ->toContain('programs.view.all', 'programs.create.site');
});

// Update Role Tests
test('authorized users can rename a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Old Name']);

    $response = $this->actingAs($user)
        ->putJson("/api/roles/{$role->id}", [
            'name' => 'New Name',
        ])
        ->assertOk();

    expect($response->json('data.name'))->toBe('New Name');
    expect(Role::where('name', 'New Name')->exists())->toBeTrue();
    expect(Role::where('name', 'Old Name')->exists())->toBeFalse();
});

test('cannot rename system critical roles', function ($roleName) {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::firstOrCreate(['name' => $roleName]);

    $this->actingAs($user)
        ->putJson("/api/roles/{$role->id}", [
            'name' => 'New Name',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);

    expect(Role::where('name', $roleName)->exists())->toBeTrue();
})->with(['Manager', 'AVP', 'Farm Admin']);

test('role name must be unique when updating', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role1 = Role::create(['name' => 'Role 1']);
    $role2 = Role::create(['name' => 'Role 2']);

    $this->actingAs($user)
        ->putJson("/api/roles/{$role1->id}", ['name' => 'Role 2'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

// Delete Role Tests
test('authorized users can delete a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Deletable Role']);

    $this->actingAs($user)
        ->deleteJson("/api/roles/{$role->id}")
        ->assertOk();

    expect(Role::where('name', 'Deletable Role')->exists())->toBeFalse();
});

test('cannot delete system critical roles', function ($roleName) {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::firstOrCreate(['name' => $roleName]);

    $this->actingAs($user)
        ->deleteJson("/api/roles/{$role->id}")
        ->assertForbidden();

    expect(Role::where('name', $roleName)->exists())->toBeTrue();
})->with(['Manager', 'AVP', 'Farm Admin']);

test('cannot delete role with assigned users', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Role With Users']);
    $roleUser = User::factory()->create();
    $roleUser->assignRole($role);

    $this->actingAs($user)
        ->deleteJson("/api/roles/{$role->id}")
        ->assertUnprocessable();

    expect(Role::where('name', 'Role With Users')->exists())->toBeTrue();
});

// Sync Permissions Tests
test('authorized users can sync role permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Test Role']);
    $role->givePermissionTo('programs.view.all');

    $response = $this->actingAs($user)
        ->putJson("/api/roles/{$role->id}/permissions", [
            'permissions' => ['programs.create.site', 'programs.update.site'],
        ])
        ->assertOk();

    $role->refresh();
    expect($role->permissions->pluck('name')->toArray())
        ->toContain('programs.create.site', 'programs.update.site')
        ->not->toContain('programs.view.all');
});

test('can remove all permissions from a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Test Role']);
    $role->givePermissionTo(['programs.view.all', 'programs.create.site']);

    $this->actingAs($user)
        ->putJson("/api/roles/{$role->id}/permissions", [
            'permissions' => [],
        ])
        ->assertOk();

    $role->refresh();
    expect($role->permissions)->toBeEmpty();
});

test('permissions field is required when syncing', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Test Role']);

    // Test without the permissions field at all (should fail)
    $this->actingAs($user)
        ->putJson("/api/roles/{$role->id}/permissions", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions']);
});

test('permissions must exist when syncing', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('users.assign-roles');

    $role = Role::create(['name' => 'Test Role']);

    $this->actingAs($user)
        ->putJson("/api/roles/{$role->id}/permissions", [
            'permissions' => ['nonexistent.permission'],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions.0']);
});
