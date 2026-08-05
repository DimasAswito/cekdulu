<?php

namespace App\Livewire;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class FavoriteList extends Component
{
    public function removeFavorite(int $productId): void
    {
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (! $favorite) {
            return;
        }

        $this->authorize('delete', $favorite);

        $user->favoriteProducts()->detach($productId);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.favorite-list', [
            'favorites' => Auth::user()->favoriteProducts()->get(),
        ]);
    }
}
