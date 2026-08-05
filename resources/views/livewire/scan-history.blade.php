<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Scan</h1>
        <p class="mt-1 text-sm text-gray-600">Semua produk yang pernah kamu cek.</p>
    </div>

    <div class="bg-white rounded-xl border border-green-100 shadow-sm">
        @if ($scans->isEmpty())
            <p class="p-10 text-center text-sm text-gray-500">Belum ada riwayat scan.</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($scans as $scan)
                    <li wire:key="scan-{{ $scan->id }}">
                        <a href="{{ route('products.show', $scan->product->barcode) }}" wire:navigate class="flex items-center gap-4 p-4 hover:bg-green-50/50">
                            <x-nutriscore-badge :grade="$scan->product->nutriscore_grade" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $scan->product->name ?? 'Tanpa nama' }}</p>
                                <p class="text-xs text-gray-500">{{ $scan->product->brand ?? '-' }} &middot; {{ $scan->scanned_at->format('d M Y, H:i') }}</p>
                            </div>
                            @if (! empty($scan->flags))
                                @php
                                    $hasDanger = collect($scan->flags)->contains(fn ($f) => ($f['severity'] ?? null) === 'danger');
                                @endphp
                                <span class="text-xs px-2 py-1 rounded-full {{ $hasDanger ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ count($scan->flags) }} peringatan
                                </span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">Aman</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="p-4 border-t border-gray-100">
                {{ $scans->links() }}
            </div>
        @endif
    </div>
</div>
