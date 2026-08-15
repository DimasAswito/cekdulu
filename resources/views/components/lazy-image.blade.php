@props(['src' => null, 'alt' => ''])

<div class="relative w-full h-full">
    @if ($src)
        <div
            x-data="{ loaded: false }"
            x-init="$refs.img.complete && (loaded = true)"
            class="w-full h-full"
        >
            <div x-show="!loaded" x-cloak class="absolute inset-0 animate-pulse bg-gray-200"></div>

            <img
                x-ref="img"
                src="{{ $src }}"
                alt="{{ $alt }}"
                loading="lazy"
                decoding="async"
                x-on:load="loaded = true"
                x-on:error="loaded = true"
                :class="loaded ? 'opacity-100' : 'opacity-0'"
                {{ $attributes->merge(['class' => 'object-contain w-full h-full transition-opacity duration-300']) }}
            >
        </div>
    @else
        <div class="w-full h-full flex items-center justify-center">
            <span class="text-gray-300 text-xs">Tidak ada gambar</span>
        </div>
    @endif
</div>
