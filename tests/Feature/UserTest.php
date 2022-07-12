<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use function Pest\Laravel\assertDatabaseHas;

uses(DatabaseMigrations::class);

test('admin can list users', function () {
    /*TODO  : after creating admin user , assign role to */
    $user = User::factory()->create();

    $this->actingAs($user, 'api')
        ->get(route('user.index'))
        ->assertOk()
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'data' => true,
        ]);
});

test("admin can list user with standard user type", function () {
    /* TODO : create and login as admin */
    $adminUser = User::factory()->create([
        'type' => 'admin'
    ]);

    $this->actingAs($adminUser, 'api')
        ->get('/api/user')
        ->assertOk()
        ->assertSuccessful();

});

test('admin can store user', function () {
    /* @TODO create user and assign role */
    $adminUser = User::factory()->create([
        'type' => 'admin'
    ]);
    $newEmail = fake()->unique()->email();
    $newName = fake()->name();
    $this->actingAs($adminUser)
        ->post(route('user.store'), [
            'name' => $newName,
            'email' => $newEmail,
            'password' => \Hash::make("123456"),
            'type' => 'user',
        ])
        ->assertOk()
        ->assertSuccessful();
    assertDatabaseHas('users', [
        'name' => $newName,
        'email' => $newEmail,
        'password' => \Hash::make("123456"),
        'type' => 'user',
    ]);
    /* TODO  - validate the returned json as well*/
});

test('storing incomplete data returns validation error', function () {
    $adminUser = User::factory()->create([
        'type' => 'admin'
    ]);
    $this->actingAs($adminUser)
        ->post(route('user.store'), [
            'name' => fake()->name,
            'email' => fake()->unique()->email(),
            'password' => \Hash::make("123456"),
            'type' => 'user',
        ])
        ->assertStatus(400);
});

test('it can update user information', function () {
    $adminUser = User::factory()->create([
        'type' => 'admin'
    ]);
    $userTobeUpdated = User::factory()->create([
        'type' => 'user'
    ]);
    $newEmail = fake()->email();
    $this->actingAs($adminUser)
        ->put(route('user.update', $userTobeUpdated), [
            'type' => 'admin',
            'name' => fake()->name(),
            'email' => $newEmail,
        ])
        ->assertOk()
        ->assertSuccessful();
    assertDatabaseHas('users', ['email' => $newEmail]);
});

test('it shows validation error when sending incomplete data', function () {
    $adminUser = User::factory()->create([
        'type' => 'admin'
    ]);
    $userTobeUpdated = User::factory()->create([
        'type' => 'user'
    ]);
    $newEmail = fake()->email();
    $this->actingAs($adminUser)
        ->patch(route('user.update', ['user' => $userTobeUpdated]), [
            'email' => $newEmail,
        ])
        ->assertStatus(ResponseCode::HTTP_BAD_REQUEST);
});
test('it can delete user record', function () {
    $adminUser = User::factory()->create([
        'type' => 'admin'
    ]);
    $userTobeDeleted = User::factory()->create([
        'type' => 'user'
    ]);
    $this->actingAs($adminUser, 'api')
        ->delete(route('user.destroy', ['user' => $userTobeDeleted]))
        ->assertOk()
        ->assertSuccessful();
});

test('it doesnt allow user delete for non admin', function () {
    $normalUser = User::factory()->create([
        'type' => 'user'
    ]);
    $userTobeDeleted = User::factory()->create([
        'type' => 'user'
    ]);
    $this->actingAs($normalUser, 'api')
        ->delete(route('user.destroy', ['user' => $userTobeDeleted]))
        ->assertStatus(ResponseCode::HTTP_FORBIDDEN);
});
