<?php

namespace Database\Seeders;

use App\Models\AlertRule;
use App\Models\Allergen;
use App\Models\Condition;
use Illuminate\Database\Seeder;

class AlertRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditionRules = [
            [
                'condition' => 'Diabetes',
                'nutrient_key' => 'sugars_100g',
                'operator' => '>',
                'threshold' => 22.5,
                'message' => 'Kandungan gula tinggi, kurang cocok untuk kondisi diabetes kamu',
            ],
            [
                'condition' => 'Hipertensi',
                'nutrient_key' => 'sodium_100g',
                'operator' => '>',
                'threshold' => 0.6,
                'message' => 'Kandungan sodium tinggi, perlu diwaspadai untuk hipertensi',
            ],
            [
                'condition' => 'Kolesterol Tinggi',
                'nutrient_key' => 'saturated-fat_100g',
                'operator' => '>',
                'threshold' => 5,
                'message' => 'Lemak jenuh tinggi, sebaiknya dibatasi',
            ],
            [
                'condition' => 'Obesitas',
                'nutrient_key' => 'energy-kcal_100g',
                'operator' => '>',
                'threshold' => 400,
                'message' => 'Kalori tinggi, perlu diperhatikan untuk kontrol berat badan',
            ],
        ];

        foreach ($conditionRules as $rule) {
            $condition = Condition::where('name', $rule['condition'])->first();

            if (! $condition) {
                continue;
            }

            AlertRule::updateOrCreate(
                [
                    'condition_id' => $condition->id,
                    'nutrient_key' => $rule['nutrient_key'],
                ],
                [
                    'operator' => $rule['operator'],
                    'threshold' => $rule['threshold'],
                    'message' => $rule['message'],
                ]
            );
        }

        $allergenMessages = [
            'Kacang' => 'Produk ini mengandung kacang, waspada karena kamu memiliki alergi kacang',
            'Laktosa' => 'Produk ini mengandung laktosa, waspada karena kamu memiliki alergi/intoleransi laktosa',
            'Gluten' => 'Produk ini mengandung gluten, waspada karena kamu memiliki alergi gluten',
            'Telur' => 'Produk ini mengandung telur, waspada karena kamu memiliki alergi telur',
            'Seafood' => 'Produk ini mengandung seafood, waspada karena kamu memiliki alergi seafood',
            'Kedelai' => 'Produk ini mengandung kedelai, waspada karena kamu memiliki alergi kedelai',
        ];

        foreach ($allergenMessages as $allergenName => $message) {
            $allergen = Allergen::where('name', $allergenName)->first();

            if (! $allergen) {
                continue;
            }

            AlertRule::updateOrCreate(
                ['allergen_id' => $allergen->id],
                ['message' => $message]
            );
        }
    }
}
