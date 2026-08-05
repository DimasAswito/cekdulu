<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $user = Auth::user();

        $recentScans = $user->scans()
            ->with('product')
            ->latest('scanned_at')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'recentScans' => $recentScans,
            'totalScans' => $user->scans()->count(),
            'totalFavorites' => $user->favoriteProducts()->count(),
        ]);
    }
}
