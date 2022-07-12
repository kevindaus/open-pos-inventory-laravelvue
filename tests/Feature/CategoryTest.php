<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

test('it can access category list', function () {
    $createdCategories = Category::factory(10)->create();
    $user = User::factory()->create();
    actingAs($user, 'api')
        ->get(route('category.list'))
        ->assertOk()
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'data' => $createdCategories->pluck('name', 'id')->toArray(),
            'message' => 'Category list'
        ]);
});

test('it can access category index', function () {
    $createdCategories = Category::factory(10)->create();
    $expectedResult = Category::latest()->paginate(10000);
    $expectedResult->setPath(route("category.index"));
    $user = User::factory()->create();
    actingAs($user, 'api')
        ->get(route('category.index'))
        ->assertOk()
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'data' => $expectedResult->jsonSerialize(),
            'message' => 'Category list'
        ]);
});

test('it can access store new category', function () {
    $user = User::factory()->create();
    actingAs($user, 'api')
        ->post(route('category.store'), [

        ])
        ->assertOk()
        ->assertSuccessful()
        ->assertJson([

        ]);
});
