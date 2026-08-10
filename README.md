# 🥗 CekDulu

**Scan before you eat.** CekDulu is a personal food-label scanner: search or look up any packaged
food product, and get instant, personalized health warnings based on your own medical conditions,
allergies, and diet goals — powered by the [Open Food Facts](https://world.openfoodfacts.org)
database.

> Built as a portfolio project to demonstrate clean Laravel architecture (service layer, action
> classes, policies), a real external API integration with caching, and a usable, reactive UI —
> without a single line of JavaScript framework beyond Livewire/Alpine.

![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?logo=livewire&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-06B6D4?logo=tailwindcss&logoColor=white)
![Pest](https://img.shields.io/badge/Tests-Pest-EF4056?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

---

## ✨ Why CekDulu

Nutrition labels are hard to read, and generic scores like Nutri-Score don't know that *you*
have diabetes, a nut allergy, or a sodium-restriction diet. CekDulu bridges that gap:

1. You set up a health profile once — conditions, allergies, diet goals.
2. You search or scan any product barcode.
3. CekDulu fetches the product from Open Food Facts, evaluates it against your profile through a
   rule engine, and shows a clear warning box (⚠️ warning / ⛔ danger) with the reason — e.g.
   *"High sugar content, not great for your diabetes."*
4. Every check is saved to your scan history, and you can bookmark favorites for later.

## 🚀 Features

- 🔍 **Product search & barcode lookup** against the live Open Food Facts catalog
- 🧠 **Personalized warning engine** — rules matched against your medical conditions, allergies,
  and per-nutrient thresholds (sugar, sodium, saturated fat, …)
- 🩺 **Health profile onboarding** — conditions, allergens, and diet goals, editable anytime
- 🟢 **Nutri-Score & NOVA group badges** with an at-a-glance color scale (A–E)
- 📜 **Scan history** — everything you've checked, with the warnings that were shown at the time
- ⭐ **Favorites** — save products you check often
- 🔐 **Per-user authorization** — your scans and favorites are yours alone, enforced via Policies
- ⚡ **Fully reactive UI** with Livewire 3 + Alpine.js — no custom JS framework, no API layer to
  maintain separately

## 🏗️ Architecture highlights

This isn't a CRUD scaffold — it's structured the way a production Laravel app should be:

| Layer | Where | Purpose |
|---|---|---|
| **Service layer** | `app/Services/OpenFoodFactsService.php` | All external API calls live here — never called directly from a component |
| **Action classes** | `app/Actions/EvaluateProductWarnings.php` | Business logic (rule matching) isolated from controllers/components |
| **Livewire components** | `app/Livewire/*` | Every interactive page — search, product detail, onboarding, dashboard, history, favorites |
| **Policies** | `app/Policies/*` | `ScanPolicy`, `FavoritePolicy` — enforce that users only touch their own data |
| **Caching** | `Cache::remember()` + local `products` table | Every Open Food Facts response is cached (TTL-based) *and* persisted locally, so the app stays fast and API-friendly |

Nutrition values are stored as JSON rather than one column per nutrient, since the source data is
inherently variable per product.

## 🧪 Tech Stack

- **Backend:** Laravel 11 (PHP 8.3+)
- **Frontend:** Livewire 3 + Alpine.js + Tailwind CSS
- **Auth:** Laravel Breeze (Livewire stack)
- **Database:** SQLite (dev), MySQL-compatible (production)
- **External API:** [Open Food Facts](https://world.openfoodfacts.org) — no API key required
- **Testing:** Pest PHP
- **Code style:** Laravel Pint (PSR-12)

## 📦 Getting Started

### Requirements
- PHP 8.3+
- Composer
- Node.js + npm

### Installation

```bash
git clone https://github.com/DimasAswito/cekdulu.git
cd cekdulu

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

npm install && npm run build
php artisan serve
```

Then open the app at the URL shown by `php artisan serve` (or `http://cekdulu.test` if you're
using [Laravel Herd](https://herd.laravel.com)).

For frontend development with hot-reload:

```bash
npm run dev
```

### Demo account (from seeder)

| Email | Password |
|---|---|
| `test@example.com` | `password` |

This account has no health profile yet, so it'll be redirected to `/onboarding` on first login —
that's the intended flow for every new user.

## ⚙️ Configuring Open Food Facts

Set in `.env`:

```env
OFF_BASE_URL=https://world.openfoodfacts.org
OFF_USER_AGENT="CekDulu/1.0 (contact: your-email@example.com)"
OFF_CACHE_TTL_HOURS=6
```

Open Food Facts [asks every integrator to send a descriptive User-Agent](https://openfoodfacts.github.io/openfoodfacts-server/api/) —
`OpenFoodFactsService` does this on every request. Responses are cached with
`Cache::remember()` (TTL from `OFF_CACHE_TTL_HOURS`) and barcode lookups are also persisted
permanently to the local `products` table, so repeat lookups don't hit the API at all.

## 🗺️ App Flow

1. **Register / Login** (Laravel Breeze)
2. **Onboarding** (`/onboarding`) — set health conditions, allergies, and diet goal
3. **Dashboard** (`/dashboard`) — recent scans, quick search, shortcuts
4. **Search** (`/products`) — search by name or barcode
5. **Product detail** (`/products/{barcode}`) — Nutri-Score, NOVA group, ingredients, full
   nutrition facts, and your personalized warning box
6. **History** (`/history`) and **Favorites** (`/favorites`)

## ✅ Testing

```bash
php artisan test
```

Tests cover authentication/onboarding redirects, product search (Open Food Facts calls are
mocked — no real API hit during tests), the warning-evaluation rule engine across multiple
condition/allergy scenarios, and authorization (users can't reach each other's scans or
favorites).

## 🎨 Code style

```bash
./vendor/bin/pint
```

## 📄 License

[MIT](LICENSE)

---

Data provided by [Open Food Facts](https://world.openfoodfacts.org), a free, open, collaborative
database of food products from around the world.
