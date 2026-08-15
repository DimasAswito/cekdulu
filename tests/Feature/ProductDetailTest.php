<?php

use App\Livewire\ProductDetail;
use App\Models\Product;
use App\Models\Scan;
use App\Models\User;
use Livewire\Livewire;

function makeScannableProduct(): Product
{
    return Product::create([
        'barcode' => '8992388101010',
        'name' => 'Indomie Goreng',
        'brand' => 'Indomie',
        'nutriscore_grade' => 'd',
        'nova_group' => 4,
        'ingredients_text' => 'Mie, bumbu',
        'nutriments' => ['sugars_100g' => 5],
        'fetched_at' => now(),
    ]);
}

test('marking a product as scanned records one scan', function () {
    $user = User::factory()->create();
    $product = makeScannableProduct();

    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['barcode' => $product->barcode])
        ->call('markScanned')
        ->assertSet('justScanned', true);

    expect(Scan::where('user_id', $user->id)->where('product_id', $product->id)->count())->toBe(1);
});

test('clicking mark as scanned again the same day does not create a duplicate history entry', function () {
    $user = User::factory()->create();
    $product = makeScannableProduct();

    $component = Livewire::actingAs($user)
        ->test(ProductDetail::class, ['barcode' => $product->barcode]);

    $component->call('markScanned');
    $component->call('markScanned');
    $component->call('markScanned');

    expect(Scan::where('user_id', $user->id)->where('product_id', $product->id)->count())->toBe(1);
});

test('revisiting the product page the same day shows it as already scanned without duplicating', function () {
    $user = User::factory()->create();
    $product = makeScannableProduct();

    Scan::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'scanned_at' => now(),
        'flags' => [],
    ]);

    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['barcode' => $product->barcode])
        ->assertSet('justScanned', true)
        ->call('markScanned');

    expect(Scan::where('user_id', $user->id)->where('product_id', $product->id)->count())->toBe(1);
});

test('a non-numeric barcode in the url is rejected before reaching the component', function () {
    $user = User::factory()->create();
    $user->healthProfile()->create([]);

    $response = $this->actingAs($user)->get('/products/not-a-barcode');

    $response->assertNotFound();
});

test('scanning the same product again on a later day adds a new history entry', function () {
    $user = User::factory()->create();
    $product = makeScannableProduct();

    Scan::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'scanned_at' => now()->subDay(),
        'flags' => [],
    ]);

    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['barcode' => $product->barcode])
        ->assertSet('justScanned', false)
        ->call('markScanned')
        ->assertSet('justScanned', true);

    expect(Scan::where('user_id', $user->id)->where('product_id', $product->id)->count())->toBe(2);
});
