<?php

namespace App\Livewire;

use App\Services\OpenFoodFactsService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductSearch extends Component
{
    public string $mode = 'name';

    #[Url(as: 'q')]
    public string $query = '';

    /** @var array<int, array> */
    public array $results = [];

    public bool $searched = false;

    public ?string $errorMessage = null;

    public function mount(OpenFoodFactsService $service): void
    {
        if (trim($this->query) !== '') {
            $this->searchByName($service);
        }
    }

    public function updatedMode(): void
    {
        $this->reset(['results', 'searched', 'errorMessage']);
    }

    public function updatedQuery(OpenFoodFactsService $service): void
    {
        if ($this->mode === 'name') {
            $this->searchByName($service);
        }
    }

    public function searchByName(OpenFoodFactsService $service): void
    {
        $this->errorMessage = null;
        $term = trim($this->query);

        if ($term === '') {
            $this->results = [];
            $this->searched = false;

            return;
        }

        $this->results = $service->searchByName($term);
        $this->searched = true;
    }

    public function searchByBarcode(OpenFoodFactsService $service): void
    {
        $this->errorMessage = null;
        $barcode = trim($this->query);

        if ($barcode === '') {
            return;
        }

        if (! preg_match('/^[0-9]{6,20}$/', $barcode)) {
            $this->errorMessage = 'Barcode harus berupa angka (6-20 digit).';

            return;
        }

        $product = $service->findByBarcode($barcode);

        if (! $product) {
            $this->errorMessage = 'Produk dengan barcode tersebut tidak ditemukan.';

            return;
        }

        $this->redirect(route('products.show', $product['barcode']), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.product-search');
    }
}
