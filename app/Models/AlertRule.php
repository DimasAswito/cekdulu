<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertRule extends Model
{
    protected $fillable = [
        'condition_id',
        'allergen_id',
        'nutrient_key',
        'operator',
        'threshold',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'threshold' => 'decimal:2',
        ];
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    public function allergen(): BelongsTo
    {
        return $this->belongsTo(Allergen::class);
    }
}
