<?php

namespace Database\Seeders;

use App\Models\Allergen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AllergenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allergens = [
            'Kacang',
            'Laktosa',
            'Gluten',
            'Telur',
            'Seafood',
            'Kedelai',
        ];

        foreach ($allergens as $name) {
            Allergen::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
