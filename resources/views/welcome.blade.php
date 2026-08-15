<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'CekDulu') }} — Cek Dulu Sebelum Makan</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-white text-gray-900">
        <header class="border-b border-brand-100">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2 text-brand-900 font-bold text-xl">
                    <x-application-logo class="w-8 h-8 text-brand-700" />
                    CekDulu
                </a>

                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-brand-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-brand-700">Masuk</a>
                        <a href="{{ route('register') }}" wire:navigate class="text-sm font-semibold bg-accent-400 text-brand-950 px-4 py-2 rounded-lg hover:bg-accent-300 transition">Daftar</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="max-w-6xl mx-auto px-6 py-16 sm:py-24 grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block text-xs font-semibold text-accent-300 bg-brand-900 px-3 py-1 rounded-full mb-4">
                        Food Label Scanner Personal
                    </span>
                    <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">
                        Cek Dulu sebelum kamu makan.
                    </h1>
                    <p class="mt-5 text-lg text-gray-600 leading-relaxed">
                        Cari atau scan produk makanan, dan dapatkan peringatan personal berdasarkan kondisi
                        kesehatan, alergi, dan target diet kamu — sebelum kamu menyesal makan sesuatu yang salah.
                    </p>
                    <div class="mt-8 flex gap-4">
                        <a href="{{ route('register') }}" wire:navigate class="bg-accent-400 text-brand-950 px-6 py-3 rounded-lg font-semibold hover:bg-accent-300 transition">
                            Mulai Gratis
                        </a>
                        <a href="{{ route('login') }}" wire:navigate class="px-6 py-3 rounded-lg font-medium text-gray-700 border border-gray-300 hover:bg-gray-50">
                            Masuk
                        </a>
                    </div>
                </div>

                <div class="bg-brand-50 border border-brand-100 rounded-2xl p-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm">E</span>
                            <div>
                                <p class="font-semibold text-sm text-gray-900">Minuman Bersoda XYZ</p>
                                <p class="text-xs text-gray-500">Nutri-Score E &middot; NOVA 4</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm p-3">
                            ⚠ Kandungan gula tinggi, kurang cocok untuk kondisi diabetes kamu
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-brand-50/60 border-y border-brand-100">
                <div class="max-w-6xl mx-auto px-6 py-16">
                    <h2 class="text-2xl font-bold text-gray-900 text-center">Kenapa pakai CekDulu?</h2>
                    <div class="mt-10 grid sm:grid-cols-3 gap-8">
                        <div class="bg-white rounded-xl p-6 border border-brand-100">
                            <div class="w-10 h-10 rounded-lg bg-brand-900 text-accent-300 flex items-center justify-center font-bold mb-4">1</div>
                            <h3 class="font-semibold text-gray-900">Cari atau Scan</h3>
                            <p class="mt-2 text-sm text-gray-600">Cari produk dari database OpenFoodFacts berdasarkan nama atau barcode.</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 border border-brand-100">
                            <div class="w-10 h-10 rounded-lg bg-brand-900 text-accent-300 flex items-center justify-center font-bold mb-4">2</div>
                            <h3 class="font-semibold text-gray-900">Profil Kesehatan</h3>
                            <p class="mt-2 text-sm text-gray-600">Atur kondisi medis, alergi, dan target diet kamu sekali saja.</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 border border-brand-100">
                            <div class="w-10 h-10 rounded-lg bg-brand-900 text-accent-300 flex items-center justify-center font-bold mb-4">3</div>
                            <h3 class="font-semibold text-gray-900">Peringatan Personal</h3>
                            <p class="mt-2 text-sm text-gray-600">Dapat peringatan otomatis kalau produk tidak cocok untuk kamu.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-6xl mx-auto px-6 py-16 text-center">
                <h2 class="text-2xl font-bold text-gray-900">Siap cek produk makananmu?</h2>
                <p class="mt-2 text-gray-600">Gratis, dan datanya di-cache biar cepat.</p>
                <a href="{{ route('register') }}" wire:navigate class="inline-block mt-6 bg-accent-400 text-brand-950 px-6 py-3 rounded-lg font-semibold hover:bg-accent-300 transition">
                    Daftar Sekarang
                </a>
            </section>
        </main>

        <footer class="border-t border-gray-100 py-8 text-center text-sm text-gray-500">
            CekDulu &middot; Portfolio project menggunakan data OpenFoodFacts
        </footer>
    </body>
</html>
