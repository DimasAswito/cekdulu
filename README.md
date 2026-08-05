# CekDulu

Food-label scanner personal. Cari atau scan produk makanan (data dari [OpenFoodFacts](https://world.openfoodfacts.org)),
lalu dapatkan peringatan personal berdasarkan profil kesehatan kamu (kondisi medis, alergi, target diet).

## Tech Stack

- Laravel 13 (PHP 8.3+)
- Livewire 3 + Alpine.js + TailwindCSS 3
- Laravel Breeze (stack Livewire)
- SQLite (development)
- Pest PHP

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
```

Lalu buka aplikasi di URL yang sudah dikonfigurasi (`APP_URL` di `.env`). Kalau memakai Laravel Herd,
site otomatis tersedia di `http://cekdulu.test`. Kalau tidak, jalankan:

```bash
php artisan serve
```

Untuk development dengan hot-reload asset:

```bash
npm run dev
```

## Menjalankan Test

```bash
php artisan test
```

## Format Kode

```bash
./vendor/bin/pint
```

## Konfigurasi OpenFoodFacts

Diatur lewat `.env`:

```
OFF_BASE_URL=https://world.openfoodfacts.org
OFF_USER_AGENT="CekDulu/1.0 (contact: your-email@example.com)"
OFF_CACHE_TTL_HOURS=6
```

Response OpenFoodFacts di-cache (`Cache::remember`, TTL sesuai `OFF_CACHE_TTL_HOURS`) dan hasil
pencarian barcode juga disimpan permanen ke tabel `products` sebagai cache lokal.

## Alur Aplikasi

1. Registrasi/login (Laravel Breeze).
2. User baru diarahkan ke `/onboarding` untuk mengisi kondisi kesehatan, alergi, dan target diet.
3. Setelah profil lengkap, user bisa mengakses dashboard, cari produk (`/products`), lihat detail
   produk dengan peringatan personal (`/products/{barcode}`), riwayat scan (`/history`), dan
   favorit (`/favorites`).

## Akun Contoh (dari seeder)

- Email: `test@example.com`
- Password: `password`

Akun ini belum punya profil kesehatan, jadi akan diarahkan ke `/onboarding` saat pertama login.
