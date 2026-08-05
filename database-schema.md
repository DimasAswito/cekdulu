# Database Schema — CekDulu

Referensi lengkap untuk migration. Gunakan ini sebagai acuan saat membuat file migration Laravel.

## users (bawaan Laravel Breeze, tidak perlu diubah)
- id, name, email, password, timestamps

## health_profiles
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigIncrements | |
| user_id | foreignId → users | unique, cascade on delete |
| daily_calorie_target | integer, nullable | |
| diet_goal | string, nullable | contoh: "menurunkan berat badan", "kontrol gula" |
| notes | text, nullable | |
| timestamps | | |

## conditions (master data)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigIncrements | |
| name | string | contoh: "Diabetes", "Hipertensi", "Kolesterol Tinggi" |
| slug | string, unique | |
| timestamps | | |

## user_conditions (pivot)
| Kolom | Tipe |
|---|---|
| user_id | foreignId → users, cascade |
| condition_id | foreignId → conditions, cascade |

primary key komposit (user_id, condition_id)

## allergens (master data)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigIncrements | |
| name | string | contoh: "Kacang", "Laktosa", "Gluten", "Telur" |
| slug | string, unique | |
| timestamps | | |

## user_allergens (pivot)
| Kolom | Tipe |
|---|---|
| user_id | foreignId → users, cascade |
| allergen_id | foreignId → allergens, cascade |

primary key komposit (user_id, allergen_id)

## products (cache lokal dari OpenFoodFacts)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigIncrements | |
| barcode | string, unique, indexed | |
| name | string, nullable | |
| brand | string, nullable | |
| image_url | string, nullable | |
| nutriscore_grade | char(1), nullable | a/b/c/d/e |
| nova_group | tinyInteger, nullable | 1-4 |
| categories | text, nullable | |
| ingredients_text | text, nullable | |
| nutriments | json, nullable | simpan raw nutriments object |
| raw_payload | json, nullable | simpan full response untuk debugging |
| fetched_at | timestamp, nullable | untuk cek staleness cache |
| timestamps | | |

## scans
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigIncrements | |
| user_id | foreignId → users, cascade |
| product_id | foreignId → products, cascade |
| scanned_at | timestamp | |
| flags | json, nullable | hasil evaluasi warning, contoh: `[{"type":"condition","label":"Diabetes","message":"Gula tinggi"}]` |
| timestamps | | |

## favorites
| Kolom | Tipe |
|---|---|
| user_id | foreignId → users, cascade |
| product_id | foreignId → products, cascade |
| created_at | timestamp |

primary key komposit (user_id, product_id)

## alert_rules (rule engine sederhana untuk evaluasi warning)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigIncrements | |
| condition_id | foreignId → conditions, nullable | rule berlaku untuk kondisi ini |
| allergen_id | foreignId → allergens, nullable | atau rule berlaku untuk alergen ini |
| nutrient_key | string, nullable | contoh: "sugars_100g", "sodium_100g", "saturated-fat_100g" |
| operator | string, nullable | contoh: ">", ">=", "<" |
| threshold | decimal(8,2), nullable | ambang batas nilai gizi per 100g |
| message | string | pesan warning yang ditampilkan ke user |
| timestamps | | |

> Catatan: rule untuk alergen biasanya tidak pakai nutrient_key/operator/threshold, cukup cek
> apakah nama alergen muncul di field `ingredients_text` atau `allergens_tags` produk.

## Relasi Eloquent Ringkas

```
User
  hasOne HealthProfile
  belongsToMany Condition (via user_conditions)
  belongsToMany Allergen (via user_allergens)
  hasMany Scan
  belongsToMany Product (favorites, via favorites, withTimestamps)

Product
  hasMany Scan

Scan
  belongsTo User
  belongsTo Product

AlertRule
  belongsTo Condition (nullable)
  belongsTo Allergen (nullable)
```

## Contoh Seed Data — alert_rules

| condition | nutrient_key | operator | threshold | message |
|---|---|---|---|---|
| Diabetes | sugars_100g | > | 22.5 | "Kandungan gula tinggi, kurang cocok untuk kondisi diabetes kamu" |
| Hipertensi | sodium_100g | > | 0.6 | "Kandungan sodium tinggi, perlu diwaspadai untuk hipertensi" |
| Kolesterol Tinggi | saturated-fat_100g | > | 5 | "Lemak jenuh tinggi, sebaiknya dibatasi" |
