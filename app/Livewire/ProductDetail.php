<?php

namespace App\Livewire;

use App\Actions\EvaluateProductWarnings;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Scan;
use App\Services\OpenFoodFactsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductDetail extends Component
{
    public string $barcode;

    public ?Product $product = null;

    public bool $notFound = false;

    /** @var array<int, array> */
    public array $warnings = [];

    public bool $isFavorited = false;

    public bool $justScanned = false;

    public function mount(string $barcode, OpenFoodFactsService $service, EvaluateProductWarnings $evaluate): void
    {
        $this->barcode = $barcode;

        $service->findByBarcode($barcode);

        $product = Product::where('barcode', $barcode)->first();

        if (! $product) {
            $this->notFound = true;

            return;
        }

        $this->product = $product;
        $this->warnings = $evaluate(Auth::user(), $product);
        $this->isFavorited = Auth::user()->favoriteProducts()->where('product_id', $product->id)->exists();
        $this->justScanned = $this->alreadyScannedToday();
    }

    public function markScanned(): void
    {
        if (! $this->product || $this->justScanned) {
            return;
        }

        $this->authorize('create', Scan::class);

        if ($this->alreadyScannedToday()) {
            $this->justScanned = true;

            return;
        }

        Scan::create([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
            'scanned_at' => now(),
            'flags' => $this->warnings,
        ]);

        $this->justScanned = true;
    }

    private function alreadyScannedToday(): bool
    {
        return Scan::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->whereDate('scanned_at', now()->toDateString())
            ->exists();
    }

    public function toggleFavorite(): void
    {
        if (! $this->product) {
            return;
        }

        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)->where('product_id', $this->product->id)->first();

        if ($favorite) {
            $this->authorize('delete', $favorite);

            $user->favoriteProducts()->detach($this->product->id);
            $this->isFavorited = false;

            return;
        }

        $this->authorize('create', Favorite::class);

        $user->favoriteProducts()->attach($this->product->id, ['created_at' => now()]);
        $this->isFavorited = true;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.product-detail');
    }
}
