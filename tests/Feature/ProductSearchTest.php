<?php

use App\Livewire\ProductSearch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('can search products by name using a mocked openfoodfacts response', function () {
    Http::fake([
        'world.openfoodfacts.org/cgi/search.pl*' => Http::response([
            'products' => [
                [
                    'code' => '1234567890123',
                    'product_name' => 'Teh Botol Sosro',
                    'brands' => 'Sosro',
                    'nutriscore_grade' => 'c',
                    'image_url' => 'https://example.com/image.jpg',
                ],
            ],
        ], 200),
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProductSearch::class)
        ->set('query', 'teh botol')
        ->assertSet('searched', true)
        ->assertSee('Teh Botol Sosro');

    Http::assertSent(fn ($request) => str_contains($request->url(), 'cgi/search.pl'));
});

test('search by barcode redirects to product detail when found', function () {
    Http::fake([
        'world.openfoodfacts.org/api/v2/product/*' => Http::response([
            'status' => 1,
            'product' => [
                'product_name' => 'Indomie Goreng',
                'brands' => 'Indomie',
                'nutriscore_grade' => 'd',
                'nova_group' => 4,
                'ingredients_text' => 'Mie, bumbu',
                'nutriments' => ['sugars_100g' => 5],
                'image_url' => 'https://example.com/indomie.jpg',
            ],
        ], 200),
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProductSearch::class)
        ->set('mode', 'barcode')
        ->set('query', '8992388101010')
        ->call('searchByBarcode')
        ->assertRedirect(route('products.show', '8992388101010'));

    expect(Product::where('barcode', '8992388101010')->exists())->toBeTrue();
});

test('search by barcode shows an error when product is not found', function () {
    Http::fake([
        'world.openfoodfacts.org/api/v2/product/*' => Http::response(['status' => 0], 200),
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProductSearch::class)
        ->set('mode', 'barcode')
        ->set('query', '0000000000000')
        ->call('searchByBarcode')
        ->assertNoRedirect()
        ->assertSet('errorMessage', 'Produk dengan barcode tersebut tidak ditemukan.');
});

test('search by barcode rejects non-numeric input without calling the openfoodfacts api', function () {
    Http::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProductSearch::class)
        ->set('mode', 'barcode')
        ->set('query', '123"; DROP TABLE users; --')
        ->call('searchByBarcode')
        ->assertNoRedirect()
        ->assertSet('errorMessage', 'Barcode harus berupa angka (6-20 digit).');

    Http::assertNothingSent();
});

test('search by name never hits a real openfoodfacts request when http is not faked for an unmatched url', function () {
    Http::fake([
        'world.openfoodfacts.org/*' => Http::response(['products' => []], 200),
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProductSearch::class)
        ->set('query', 'apapun');

    Http::assertSentCount(1);
});
