# BUILD_PROMPT — Paste ini ke Claude Code (dalam 1 pesan)

> Cara pakai: taruh file ini bersama `CLAUDE.md`, `database-schema.md`, dan `.env.example` di root
> folder project (folder boleh kosong/baru). Jalankan Claude Code di folder itu, lalu paste seluruh
> isi blok di bawah sebagai prompt pertama.

---

Buatkan saya aplikasi web Laravel bernama **CekDulu** secara lengkap dan langsung bisa dijalankan.
Baca dulu `CLAUDE.md` dan `database-schema.md` di root folder ini sebagai acuan arsitektur dan skema
database sebelum mulai coding — ikuti semua konvensi yang tertulis di sana.

## Konteks Proyek

CekDulu adalah aplikasi food-label scanner personal. User mencari produk makanan (data dari
OpenFoodFacts API), lalu mendapat peringatan personal berdasarkan profil kesehatan mereka
(kondisi medis, alergi, target diet).

## Yang Perlu Dibangun

### 1. Setup Awal
- Inisialisasi project Laravel 11 baru di folder ini.
- Install & setup Laravel Breeze dengan stack **Livewire**.
- Install TailwindCSS (biasanya sudah include lewat Breeze).
- Setup `.env` dari `.env.example` yang sudah ada, gunakan SQLite untuk development.

### 2. Database
- Buat semua migration sesuai `database-schema.md` (tabel: health_profiles, conditions,
  user_conditions, allergens, user_allergens, products, scans, favorites, alert_rules).
- Buat semua Model Eloquent dengan relasi sesuai bagian "Relasi Eloquent Ringkas" di
  `database-schema.md`.
- Buat seeder untuk `conditions` (minimal: Diabetes, Hipertensi, Kolesterol Tinggi, Obesitas),
  `allergens` (minimal: Kacang, Laktosa, Gluten, Telur, Seafood, Kedelai), dan `alert_rules`
  (pakai contoh data di bagian akhir `database-schema.md`, boleh ditambah).

### 3. Service Layer — Integrasi OpenFoodFacts
Buat `app/Services/OpenFoodFactsService.php` dengan method:
- `findByBarcode(string $barcode): ?array` — panggil
  `GET {OFF_BASE_URL}/api/v2/product/{barcode}.json` dengan field yang relevan
  (product_name, brands, nutriscore_grade, nova_group, ingredients_text, nutriments,
  image_url, allergens_tags). Cache hasil pakai `Cache::remember()` dengan TTL dari
  `config`/`.env` (`OFF_CACHE_TTL_HOURS`), dan simpan/update ke tabel `products` sebagai
  cache lokal permanen.
- `searchByName(string $query, int $page = 1): array` — panggil endpoint legacy
  `GET {OFF_BASE_URL}/cgi/search.pl?search_terms={query}&search_simple=1&action=process&json=1&page_size=20&page={page}`
  (full-text search tidak tersedia di v2, jadi wajib pakai endpoint ini). Cache juga.
- Semua request wajib pakai header `User-Agent` dari `.env` (`OFF_USER_AGENT`).
- Tangani error/timeout dengan graceful fallback (return null/array kosong + log error),
  jangan sampai error API bikin halaman crash.

### 4. Business Logic — Evaluasi Peringatan
Buat `app/Actions/EvaluateProductWarnings.php`:
- Input: `User $user`, `Product $product`.
- Ambil semua `alert_rules` yang cocok dengan `conditions` dan `allergens` milik user.
- Untuk rule berbasis nutrient (nutrient_key/operator/threshold): bandingkan nilai di
  `product->nutriments` (per 100g) dengan threshold.
- Untuk rule berbasis alergen: cek apakah nama alergen ada di `ingredients_text` atau
  `allergens_tags` produk (case-insensitive).
- Return array warning berisi `type`, `label`, `message`, `severity` (warning/danger).
- Hasil evaluasi ini yang disimpan ke kolom `scans.flags` saat user melakukan scan.

### 5. Livewire Components & Halaman

Buat semua komponen berikut lengkap dengan Blade view, styling Tailwind (tema hijau-putih,
modern, minimalis), dan responsif mobile-first:

1. **Landing page** (`/`) — hero section, penjelasan manfaat, CTA daftar/masuk. Statis, tidak
   perlu Livewire.
2. **ProfileSetup** (Livewire, halaman `/onboarding` setelah register) — form multi-step atau
   satu halaman: pilih kondisi kesehatan (checkbox multi), alergi (checkbox multi), target diet
   (text/select), simpan ke `health_profiles` + pivot tables.
3. **ProductSearch** (Livewire, halaman `/products`) — search bar dengan debounce, toggle
   cari-by-nama vs cari-by-barcode, hasil pencarian dalam grid card (gambar, nama, brand,
   badge Nutri-Score berwarna).
4. **ProductDetail** (Livewire, halaman `/products/{barcode}`) — tampilkan badge Nutri-Score
   (A=hijau tua, B=hijau muda, C=kuning, D=oranye, E=merah), Nova Group, ingredients list,
   info gizi lengkap, **warning box** yang menonjol (kuning untuk warning, merah untuk danger)
   berisi hasil `EvaluateProductWarnings`, tombol "Tandai sudah dicek" (buat record `scans`),
   tombol simpan/hapus favorit.
5. **Dashboard** (Livewire, halaman `/dashboard`, jadi landing setelah login) — ringkasan scan
   5 terakhir, jumlah total scan, quick search bar, shortcut ke favorit.
6. **ScanHistory** (Livewire, halaman `/history`) — list riwayat scan dengan paginasi, tampilkan
   status warning per item (badge).
7. **FavoriteList** (Livewire, halaman `/favorites`) — grid produk favorit, tombol hapus dari
   favorit langsung dari grid.

### 6. Auth & Otorisasi
- Pakai auth bawaan Breeze (register, login, logout, forgot password).
- Setelah register pertama kali → redirect ke `/onboarding` (ProfileSetup) sebelum ke dashboard.
- Buat `ScanPolicy` dan `FavoritePolicy`: user hanya boleh lihat/hapus scan & favorit miliknya
  sendiri. Terapkan di setiap Livewire component/route yang relevan.

### 7. Routing
- Semua route pakai `Route::middleware('auth')` kecuali landing page dan auth routes.
- Named routes: `dashboard`, `onboarding`, `products.search`, `products.show`, `history.index`,
  `favorites.index`.

### 8. Testing
- Buat minimal Pest feature test untuk: registrasi + redirect ke onboarding, pencarian produk
  (mock HTTP response OpenFoodFacts, jangan hit API asli saat testing), evaluasi warning
  (`EvaluateProductWarnings`) dengan beberapa skenario kondisi/alergi, dan otorisasi
  (`ScanPolicy`/`FavoritePolicy` mencegah akses data user lain).

### 9. Terakhir
- Pastikan `php artisan migrate --seed` jalan tanpa error.
- Pastikan `php artisan test` semua hijau.
- Buat `README.md` singkat berisi cara instalasi dan menjalankan project.
- Rangkum di akhir: file apa saja yang dibuat/diubah, dan langkah manual apa (jika ada) yang
  masih perlu saya lakukan sendiri (misal generate APP_KEY, dsb).

Kerjakan secara bertahap dan runtut (setup → migration/model → seeder → service → action →
livewire components → routing → testing), commit-kan pemikiranmu secara singkat di tiap tahap
besar, tapi tidak perlu tanya konfirmasi ke saya di tengah jalan kecuali menemukan blocker nyata.
