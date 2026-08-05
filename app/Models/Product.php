<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'name',
        'brand',
        'image_url',
        'nutriscore_grade',
        'nova_group',
        'categories',
        'ingredients_text',
        'nutriments',
        'raw_payload',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'nutriments' => 'array',
            'raw_payload' => 'array',
            'fetched_at' => 'datetime',
        ];
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
