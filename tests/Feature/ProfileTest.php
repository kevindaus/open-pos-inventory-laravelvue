<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('it can access profile of logged in user', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'api')->get(route('profile.who-am-i'))->assertSuccessful()->assertOk();
});

test("it can't access api profile if not logged in", function () {
    $this->get(route('profile.who-am-i'))->assertStatus(401);
});


test('it can update profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'api')->put(route('profile.update'), ['email' => $this->faker->email, 'name' => $this->faker->name])->assertOk()->assertSuccessful();
});

test("it can't update profile", function () {
    $this->put(route('profile.update'), ['email' => $this->faker->email, 'name' => $this->faker->name])->assertOk()->assertSuccessful();
});
test('it can return validation error when updating profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'api')
        ->put(route('profile.update'), ['name' => fake()->name])
        ->assertStatus(422);
});


test('it can change password', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'api')
        ->post(route('profile.change-password'), [
            'current_password' => 123456,
            'new_password' => 123456789,
            'confirm_password' => 123456789
        ])
        ->assertOk()
        ->assertSuccessful()
        ->assertJson(['success' => true, 'message' => true]);
});
test('it returns validation error when changing password using invalid data', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'api')
        ->postJson('/api/change-password', [
            'current_password' => fake()->password
        ])
        ->assertStatus(422);
});
