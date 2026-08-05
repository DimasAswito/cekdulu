<?php

use App\Actions\EvaluateProductWarnings;
use App\Models\AlertRule;
use App\Models\Allergen;
use App\Models\Condition;
use App\Models\Product;
use App\Models\User;

test('flags a nutrient warning when the threshold is exceeded for a user condition', function () {
    $user = User::factory()->create();
    $diabetes = Condition::create(['name' => 'Diabetes', 'slug' => 'diabetes']);
    $user->conditions()->attach($diabetes);

    AlertRule::create([
        'condition_id' => $diabetes->id,
        'nutrient_key' => 'sugars_100g',
        'operator' => '>',
        'threshold' => 22.5,
        'message' => 'Kandungan gula tinggi',
    ]);

    $product = Product::create([
        'barcode' => '1111111111111',
        'name' => 'Minuman Manis',
        'nutriments' => ['sugars_100g' => 30],
    ]);

    $warnings = (new EvaluateProductWarnings)($user, $product);

    expect($warnings)->toHaveCount(1)
        ->and($warnings[0]['type'])->toBe('condition')
        ->and($warnings[0]['label'])->toBe('Diabetes')
        ->and($warnings[0]['severity'])->toBe('warning');
});

test('does not flag a nutrient warning when the value is under the threshold', function () {
    $user = User::factory()->create();
    $diabetes = Condition::create(['name' => 'Diabetes', 'slug' => 'diabetes']);
    $user->conditions()->attach($diabetes);

    AlertRule::create([
        'condition_id' => $diabetes->id,
        'nutrient_key' => 'sugars_100g',
        'operator' => '>',
        'threshold' => 22.5,
        'message' => 'Kandungan gula tinggi',
    ]);

    $product = Product::create([
        'barcode' => '2222222222222',
        'nutriments' => ['sugars_100g' => 5],
    ]);

    $warnings = (new EvaluateProductWarnings)($user, $product);

    expect($warnings)->toBeEmpty();
});

test('flags an allergen warning when the allergen name appears in the ingredients text', function () {
    $user = User::factory()->create();
    $peanut = Allergen::create(['name' => 'Kacang', 'slug' => 'kacang']);
    $user->allergens()->attach($peanut);

    AlertRule::create([
        'allergen_id' => $peanut->id,
        'message' => 'Mengandung kacang, waspada alergi',
    ]);

    $product = Product::create([
        'barcode' => '3333333333333',
        'ingredients_text' => 'Gula, minyak kacang tanah, garam',
    ]);

    $warnings = (new EvaluateProductWarnings)($user, $product);

    expect($warnings)->toHaveCount(1)
        ->and($warnings[0]['type'])->toBe('allergen')
        ->and($warnings[0]['label'])->toBe('Kacang')
        ->and($warnings[0]['severity'])->toBe('danger');
});

test('does not flag an allergen warning when the allergen is not present', function () {
    $user = User::factory()->create();
    $peanut = Allergen::create(['name' => 'Kacang', 'slug' => 'kacang']);
    $user->allergens()->attach($peanut);

    AlertRule::create([
        'allergen_id' => $peanut->id,
        'message' => 'Mengandung kacang, waspada alergi',
    ]);

    $product = Product::create([
        'barcode' => '4444444444444',
        'ingredients_text' => 'Gula, tepung terigu, garam',
    ]);

    $warnings = (new EvaluateProductWarnings)($user, $product);

    expect($warnings)->toBeEmpty();
});

test('returns no warnings when the user has no conditions or allergens', function () {
    $user = User::factory()->create();

    $product = Product::create([
        'barcode' => '5555555555555',
        'nutriments' => ['sugars_100g' => 100],
    ]);

    $warnings = (new EvaluateProductWarnings)($user, $product);

    expect($warnings)->toBeEmpty();
});

test('can return multiple warnings for a user with both a condition and an allergen match', function () {
    $user = User::factory()->create();

    $diabetes = Condition::create(['name' => 'Diabetes', 'slug' => 'diabetes']);
    $peanut = Allergen::create(['name' => 'Kacang', 'slug' => 'kacang']);
    $user->conditions()->attach($diabetes);
    $user->allergens()->attach($peanut);

    AlertRule::create([
        'condition_id' => $diabetes->id,
        'nutrient_key' => 'sugars_100g',
        'operator' => '>',
        'threshold' => 22.5,
        'message' => 'Kandungan gula tinggi',
    ]);

    AlertRule::create([
        'allergen_id' => $peanut->id,
        'message' => 'Mengandung kacang, waspada alergi',
    ]);

    $product = Product::create([
        'barcode' => '6666666666666',
        'nutriments' => ['sugars_100g' => 40],
        'ingredients_text' => 'Gula, kacang tanah, garam',
    ]);

    $warnings = (new EvaluateProductWarnings)($user, $product);

    expect($warnings)->toHaveCount(2);
});
