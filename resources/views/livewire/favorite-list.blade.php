<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Produk Favorit</h1>
        <p class="mt-1 text-sm text-gray-600">Produk yang sudah kamu simpan.</p>
    </div>

    @if ($favorites->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <p class="text-sm text-gray-500">Kamu belum menyimpan produk favorit.</p>
            <a href="{{ route('products.search') }}" wire:navigate class="inline-block mt-4 text-brand-700 font-medium hover:underline">
                Cari produk
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($favorites as $product)
                <div wire:key="favorite-{{ $product->id }}" class="bg-white rounded-xl border border-gray-200 hover:border-brand-400 hover:shadow-md hover:-translate-y-0.5 transition p-3 flex flex-col">
                    <a href="{{ route('products.show', $product->barcode) }}" wire:navigate>
                        <div class="aspect-square w-full bg-gray-50 rounded-lg overflow-hidden mb-3 flex items-center justify-center">
                            <x-lazy-image :src="$product->image_url" :alt="$product->name" />
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $product->name ?? 'Tanpa nama' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $product->brand ?? '-' }}</p>
                            </div>
                            <x-nutriscore-badge :grade="$product->nutriscore_grade" />
                        </div>
                    </a>
                    <button
                        wire:click="removeFavorite({{ $product->id }})"
                        wire:confirm="Hapus dari favorit?"
                        class="mt-3 text-xs font-medium text-red-600 hover:text-red-700 hover:underline self-start"
                    >
                        Hapus dari favorit
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>
