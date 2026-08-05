<?php

use App\Livewire\ProfileSetup;
use App\Models\Allergen;
use App\Models\Condition;
use App\Models\User;
use Livewire\Livewire;

test('users without a completed health profile are redirected from dashboard to onboarding', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect(route('onboarding'));
});

test('users with a completed health profile can access the dashboard directly', function () {
    $user = User::factory()->create();
    $user->healthProfile()->create([]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
});

test('profile setup saves conditions, allergens, and health profile details', function () {
    $user = User::factory()->create();
    $diabetes = Condition::create(['name' => 'Diabetes', 'slug' => 'diabetes']);
    $peanut = Allergen::create(['name' => 'Kacang', 'slug' => 'kacang']);

    Livewire::actingAs($user)
        ->test(ProfileSetup::class)
        ->set('conditions', [$diabetes->id])
        ->set('allergens', [$peanut->id])
        ->set('diet_goal', 'menurunkan berat badan')
        ->set('daily_calorie_target', 1800)
        ->call('save')
        ->assertRedirect(route('dashboard'));

    $user->refresh();

    expect($user->healthProfile)->not->toBeNull()
        ->and($user->healthProfile->diet_goal)->toBe('menurunkan berat badan')
        ->and($user->healthProfile->daily_calorie_target)->toBe(1800)
        ->and($user->conditions->pluck('id')->all())->toBe([$diabetes->id])
        ->and($user->allergens->pluck('id')->all())->toBe([$peanut->id]);
});
