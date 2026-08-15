<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    @if ($notFound)
        <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
            <h1 class="text-xl font-semibold text-gray-900">Produk tidak ditemukan</h1>
            <p class="mt-2 text-sm text-gray-600">
                Barcode <span class="font-mono">{{ $barcode }}</span> tidak ditemukan di database OpenFoodFacts.
            </p>
            <a href="{{ route('products.search') }}" wire:navigate class="inline-block mt-6 text-brand-700 font-medium hover:underline">
                &larr; Kembali cari produk lain
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-brand-100 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row gap-6">
                <div class="w-32 h-32 shrink-0 bg-gray-50 rounded-lg overflow-hidden flex items-center justify-center mx-auto sm:mx-0">
                    <x-lazy-image :src="$product->image_url" :alt="$product->name" />
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ $product->name ?? 'Tanpa nama' }}</h1>
                            <p class="text-sm text-gray-500">{{ $product->brand ?? '-' }}</p>
                        </div>
                        <x-nutriscore-badge :grade="$product->nutriscore_grade" class="w-10 h-10 text-base" />
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        @if ($product->nova_group)
                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700">NOVA {{ $product->nova_group }}</span>
                        @endif
                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 font-mono">{{ $product->barcode }}</span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <button
                            wire:click="markScanned"
                            @disabled($justScanned)
                            class="px-4 py-2 rounded-lg text-sm font-medium {{ $justScanned ? 'bg-brand-100 text-brand-700 cursor-default' : 'bg-brand-600 text-white hover:bg-brand-700' }}"
                        >
                            {{ $justScanned ? 'Sudah dicek ✓' : 'Tandai sudah dicek' }}
                        </button>

                        <button
                            wire:click="toggleFavorite"
                            class="px-4 py-2 rounded-lg text-sm font-medium border {{ $isFavorited ? 'border-red-300 text-red-600 bg-red-50 hover:bg-red-100' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        >
                            {{ $isFavorited ? '♥ Hapus dari favorit' : '♡ Simpan ke favorit' }}
                        </button>
                    </div>
                </div>
            </div>

            @if (! empty($warnings))
                <div class="px-6 sm:px-8 pb-6 space-y-3">
                    @foreach ($warnings as $warning)
                        @php
                            $isDanger = ($warning['severity'] ?? 'warning') === 'danger';
                        @endphp
                        <div class="rounded-lg border p-4 {{ $isDanger ? 'bg-red-50 border-red-300 text-red-800' : 'bg-yellow-50 border-yellow-300 text-yellow-800' }}">
                            <p class="font-semibold text-sm">{{ $isDanger ? '⚠ Peringatan Penting' : '⚠ Perhatian' }} — {{ $warning['label'] }}</p>
                            <p class="text-sm mt-1">{{ $warning['message'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="border-t border-gray-100 px-6 sm:px-8 py-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-2">Komposisi</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $product->ingredients_text ?: 'Informasi komposisi tidak tersedia.' }}
                </p>
            </div>

            @php
                $nutriments = $product->nutriments ?? [];
                $nutrientLabels = [
                    'energy-kcal_100g' => 'Energi (kkal)',
                    'fat_100g' => 'Lemak (g)',
                    'saturated-fat_100g' => 'Lemak Jenuh (g)',
                    'carbohydrates_100g' => 'Karbohidrat (g)',
                    'sugars_100g' => 'Gula (g)',
                    'fiber_100g' => 'Serat (g)',
                    'proteins_100g' => 'Protein (g)',
                    'salt_100g' => 'Garam (g)',
                    'sodium_100g' => 'Sodium (g)',
                ];
            @endphp

            @if (! empty($nutriments))
                <div class="border-t border-gray-100 px-6 sm:px-8 py-6">
                    <h2 class="text-sm font-semibold text-gray-900 mb-3">Informasi Gizi (per 100g)</h2>
                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach ($nutrientLabels as $key => $label)
                            @if (isset($nutriments[$key]))
                                <div>
                                    <dt class="text-xs text-gray-500">{{ $label }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $nutriments[$key] }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>
            @endif
        </div>
    @endif
</div>
