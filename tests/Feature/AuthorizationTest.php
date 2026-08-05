<?php

use App\Livewire\FavoriteList;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Scan;
use App\Models\User;
use Livewire\Livewire;

test('a user cannot view another users scan', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $product = Product::create(['barcode' => '7777777777777']);

    $scan = Scan::create([
        'user_id' => $owner->id,
        'product_id' => $product->id,
        'scanned_at' => now(),
    ]);

    expect($owner->can('view', $scan))->toBeTrue()
        ->and($intruder->can('view', $scan))->toBeFalse();
});

test('a user cannot delete another users scan', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $product = Product::create(['barcode' => '8888888888888']);

    $scan = Scan::create([
        'user_id' => $owner->id,
        'product_id' => $product->id,
        'scanned_at' => now(),
    ]);

    expect($intruder->can('delete', $scan))->toBeFalse();
});

test('a user cannot delete another users favorite', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $product = Product::create(['barcode' => '9999999999999']);
    $owner->favoriteProducts()->attach($product->id, ['created_at' => now()]);

    $favorite = Favorite::where('user_id', $owner->id)->where('product_id', $product->id)->first();

    expect($owner->can('delete', $favorite))->toBeTrue()
        ->and($intruder->can('delete', $favorite))->toBeFalse();
});

test('removing another users favorite through the component does not delete it', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $product = Product::create(['barcode' => '1010101010101']);
    $owner->favoriteProducts()->attach($product->id, ['created_at' => now()]);

    Livewire::actingAs($intruder)
        ->test(FavoriteList::class)
        ->call('removeFavorite', $product->id);

    expect($owner->favoriteProducts()->where('product_id', $product->id)->exists())->toBeTrue();
});
