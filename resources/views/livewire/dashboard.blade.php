<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }} 👋</h1>
        <p class="mt-1 text-sm text-gray-600">Ini ringkasan aktivitas cek produkmu.</p>
    </div>

    <form action="{{ route('products.search') }}" method="GET" class="bg-white rounded-xl border border-brand-100 shadow-sm p-4 sm:p-5 flex gap-2">
        <input
            type="text"
            name="q"
            placeholder="Cari produk cepat, mis. teh botol..."
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
        >
        <x-primary-button type="submit">Cari</x-primary-button>
    </form>

    <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-brand-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">Total Scan</p>
            <p class="text-3xl font-bold text-brand-700 mt-1">{{ $totalScans }}</p>
        </div>
        <a href="{{ route('favorites.index') }}" wire:navigate class="bg-white rounded-xl border border-brand-100 shadow-sm p-5 hover:border-brand-400 transition">
            <p class="text-sm text-gray-500">Produk Favorit</p>
            <p class="text-3xl font-bold text-brand-700 mt-1">{{ $totalFavorites }}</p>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-brand-100 shadow-sm">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Scan Terakhir</h2>
            <a href="{{ route('history.index') }}" wire:navigate class="text-sm text-brand-700 hover:underline">Lihat semua</a>
        </div>

        @if ($recentScans->isEmpty())
            <p class="p-8 text-center text-sm text-gray-500">Kamu belum pernah scan produk apa pun.</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($recentScans as $scan)
                    <li wire:key="recent-scan-{{ $scan->id }}">
                        <a href="{{ route('products.show', $scan->product->barcode) }}" wire:navigate class="flex items-center gap-4 p-4 hover:bg-brand-50/50">
                            <x-nutriscore-badge :grade="$scan->product->nutriscore_grade" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $scan->product->name ?? 'Tanpa nama' }}</p>
                                <p class="text-xs text-gray-500">{{ $scan->scanned_at->diffForHumans() }}</p>
                            </div>
                            @if (! empty($scan->flags))
                                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">{{ count($scan->flags) }} peringatan</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
