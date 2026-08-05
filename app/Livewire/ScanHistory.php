<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ScanHistory extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    public function render()
    {
        $scans = Auth::user()->scans()
            ->with('product')
            ->latest('scanned_at')
            ->paginate(10);

        return view('livewire.scan-history', ['scans' => $scans]);
    }
}
