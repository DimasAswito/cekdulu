# CLAUDE.md — Project: CekDulu

Panduan ini dibaca otomatis oleh Claude Code setiap kali bekerja di repo ini. Ikuti aturan
di bawah secara konsisten untuk semua perubahan kode.

## 1. Ringkasan Proyek

**CekDulu** adalah aplikasi web food-label scanner personal. User mencari/scan produk makanan
(data diambil dari **OpenFoodFacts API**), lalu sistem memberi peringatan personal berdasarkan
profil kesehatan user (kondisi medis, alergi, target diet) yang sudah mereka atur.

Tujuan proyek: portfolio developer yang menunjukkan integrasi API eksternal, arsitektur Laravel
yang rapi (service layer, action classes, policy), dan UI yang usable.

## 2. Tech Stack

- **Backend:** Laravel 11 (PHP 8.3+)
- **Frontend:** Livewire 3 + Alpine.js + TailwindCSS
- **Auth:** Laravel Breeze (stack Livewire)
- **Database:** SQLite untuk development, kompatibel MySQL untuk production
- **External API:** OpenFoodFacts (tanpa API key, wajib kirim custom User-Agent)
- **Testing:** Pest PHP
- **Code style:** Laravel Pint (PSR-12)

## 3. Perintah Penting

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run dev
php artisan serve
php artisan test          # jalankan test Pest
./vendor/bin/pint          # format kode
```

## 4. Arsitektur & Konvensi Wajib

- **Service layer** untuk semua panggilan API eksternal: `app/Services/OpenFoodFactsService.php`.
  Jangan panggil `Http::get()` langsung dari Controller/Livewire component.
- **Action classes** untuk business logic non-trivial, contoh: `app/Actions/EvaluateProductWarnings.php`.
- **Livewire components** untuk semua bagian interaktif (search, form profil, dsb), bukan
  Controller + Blade biasa, kecuali halaman statis (landing page).
- **Policy** untuk setiap resource milik user (`ScanPolicy`, `FavoritePolicy`) — user hanya boleh
  akses data miliknya sendiri.
- **Caching** wajib untuk semua response OpenFoodFacts: gunakan `Cache::remember()` dengan key
  berbasis barcode/query, TTL 6 jam. Simpan juga hasil fetch ke tabel `products` sebagai cache lokal
  permanen (lihat `database-schema.md`).
- Semua nilai gizi (nutriments) disimpan sebagai JSON, jangan bikin kolom terpisah per nutrisi.
- Gunakan Form Request class untuk validasi input, jangan validasi inline di controller.
- Named routes untuk semua route, prefix sesuai fitur: `products.*`, `scans.*`, `favorites.*`, `profile.*`.

## 5. Struktur Folder yang Diharapkan

```
app/
  Actions/
    EvaluateProductWarnings.php
  Livewire/
    ProductSearch.php
    ProductDetail.php
    ScanHistory.php
    FavoriteList.php
    ProfileSetup.php
    Dashboard.php
  Models/
    User.php
    HealthProfile.php
    Condition.php
    Allergen.php
    Product.php
    Scan.php
    Favorite.php
    AlertRule.php
  Policies/
    ScanPolicy.php
    FavoritePolicy.php
  Services/
    OpenFoodFactsService.php
database/
  migrations/
  seeders/
    ConditionSeeder.php
    AllergenSeeder.php
    AlertRuleSeeder.php
resources/views/livewire/
tests/Feature/
```

## 6. Skema Database

Lihat detail lengkap di `database-schema.md`. Ringkasan tabel:
`users`, `health_profiles`, `conditions`, `user_conditions` (pivot), `allergens`,
`user_allergens` (pivot), `products` (cache lokal OpenFoodFacts), `scans`, `favorites`, `alert_rules`.

## 7. Integrasi OpenFoodFacts API

- Base URL: `https://world.openfoodfacts.org/api/v2`
- Get produk by barcode: `GET /product/{barcode}.json?fields=product_name,brands,nutriscore_grade,nova_group,ingredients_text,nutriments,image_url`
- Search by nama produk: full-text search **tidak tersedia** di API v2, gunakan endpoint legacy:
  `GET https://world.openfoodfacts.org/cgi/search.pl?search_terms={query}&search_simple=1&action=process&json=1&page_size=20`
- **Wajib** kirim header `User-Agent: CekDulu/1.0 (contact: your-email@example.com)` di setiap request,
  ini diminta langsung oleh dokumentasi OpenFoodFacts.
- Tidak perlu API key untuk baca data.
- Tangani kasus produk tidak ditemukan (`status: 0` pada response) dengan graceful fallback di UI.

## 8. Desain UI

Gaya: modern, minimalis, clean. Palet warna hijau-putih (kesan sehat & terpercaya).
Komponen konsisten: navbar, card produk, badge warna skor kesehatan (Nutri-Score A-E),
alert/warning box untuk peringatan personal.

## 9. Definition of Done untuk Setiap Fitur

- Migration + model + relasi selesai dan ada test dasar.
- Livewire component dengan validasi input.
- Policy diterapkan di route/component yang relevan.
- View responsif (mobile-first, Tailwind).
- Tidak ada pemanggilan API eksternal tanpa cache.
