<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Cari Produk</h1>
        <p class="mt-1 text-sm text-gray-600">Cari berdasarkan nama produk atau scan/ketik barcode.</p>
    </div>

    <div class="bg-white rounded-xl border border-brand-100 shadow-sm p-4 sm:p-6">
        <div class="flex gap-2 mb-4">
            <button
                type="button"
                wire:click="$set('mode', 'name')"
                class="px-4 py-2 text-sm font-medium rounded-lg {{ $mode === 'name' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                Cari Nama
            </button>
            <button
                type="button"
                wire:click="$set('mode', 'barcode')"
                class="px-4 py-2 text-sm font-medium rounded-lg {{ $mode === 'barcode' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                Cari Barcode
            </button>
        </div>

        @if ($mode === 'name')
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.500ms="query"
                    placeholder="Ketik nama produk, mis. teh botol..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                >
                <div wire:loading wire:target="query" class="absolute right-3 top-2.5 text-xs text-gray-400">Mencari...</div>
            </div>
        @else
            <div x-data="barcodeScanner">
                <form wire:submit="searchByBarcode" class="flex gap-2">
                    <input
                        type="text"
                        wire:model="query"
                        placeholder="Masukkan angka barcode..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                    >
                    <x-primary-button type="submit">Cari</x-primary-button>
                    <button
                        type="button"
                        x-on:click="start"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-semibold text-brand-900 bg-accent-300 hover:bg-accent-200 transition whitespace-nowrap"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V6a2 2 0 0 1 2-2h2M4 16v2a2 2 0 0 0 2 2h2M20 8V6a2 2 0 0 0-2-2h-2M20 16v2a2 2 0 0 1-2 2h-2" />
                            <path stroke-linecap="round" d="M7 12h10" />
                        </svg>
                        Scan Kamera
                    </button>
                </form>

                <div
                    x-show="open"
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-brand-950/80 p-4"
                >
                    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900">Scan Barcode</h3>
                            <button type="button" x-on:click="stop" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                        </div>

                        <div wire:ignore>
                            <div id="barcode-reader" class="rounded-lg overflow-hidden bg-black min-h-[220px]"></div>
                        </div>

                        <p x-show="error" x-text="error" class="mt-3 text-sm text-red-600"></p>
                        <p x-show="!error" class="mt-3 text-xs text-gray-500">Arahkan kamera ke barcode produk.</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <p class="mt-3 text-sm text-red-600">{{ $errorMessage }}</p>
        @endif
    </div>

    @if ($mode === 'name')
        <div class="mt-6" wire:loading.remove wire:target="query">
            @if ($searched && empty($results))
                <p class="text-center text-gray-500 py-12">Tidak ada produk ditemukan untuk "{{ $query }}".</p>
            @elseif (! empty($results))
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($results as $item)
                        <a
                            wire:key="result-{{ $item['code'] ?? $loop->index }}"
                            href="{{ isset($item['code']) ? route('products.show', $item['code']) : '#' }}"
                            wire:navigate
                            class="bg-white rounded-xl border border-gray-200 hover:border-brand-400 hover:shadow-md transition p-3 flex flex-col"
                        >
                            <div class="aspect-square w-full bg-gray-50 rounded-lg overflow-hidden mb-3 flex items-center justify-center">
                                <x-lazy-image
                                    :src="$item['image_front_small_url'] ?? $item['image_url'] ?? null"
                                    :alt="$item['product_name'] ?? 'Produk'"
                                />
                            </div>
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $item['product_name'] ?? 'Tanpa nama' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $item['brands'] ?? '-' }}</p>
                                </div>
                                <x-nutriscore-badge :grade="$item['nutriscore_grade'] ?? null" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
