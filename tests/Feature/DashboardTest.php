<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard', ['site' => 'test']))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard', ['site' => $user->primarySite->site_code]))
        ->assertOk();
});
