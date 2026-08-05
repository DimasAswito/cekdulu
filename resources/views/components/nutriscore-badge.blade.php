@props(['grade'])

@php
    $grade = $grade ? strtoupper($grade) : null;

    $colors = [
        'A' => 'bg-green-700 text-white',
        'B' => 'bg-green-400 text-green-950',
        'C' => 'bg-yellow-400 text-yellow-950',
        'D' => 'bg-orange-500 text-white',
        'E' => 'bg-red-600 text-white',
    ];

    $classes = $colors[$grade] ?? 'bg-gray-300 text-gray-700';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold shrink-0 $classes"]) }}>
    {{ $grade ?? '?' }}
</span>
