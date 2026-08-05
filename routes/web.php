<?php

use App\Livewire\Dashboard;
use App\Livewire\FavoriteList;
use App\Livewire\ProductDetail;
use App\Livewire\ProductSearch;
use App\Livewire\ProfileSetup;
use App\Livewire\ScanHistory;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('onboarding', ProfileSetup::class)->name('onboarding');

    Route::view('profile', 'profile')->name('profile');

    Route::middleware('profile.complete')->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
        Route::get('products', ProductSearch::class)->name('products.search');
        Route::get('products/{barcode}', ProductDetail::class)->name('products.show');
        Route::get('history', ScanHistory::class)->name('history.index');
        Route::get('favorites', FavoriteList::class)->name('favorites.index');
    });
});

require __DIR__.'/auth.php';
