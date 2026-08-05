<?php

namespace App\Actions;

use App\Models\AlertRule;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class EvaluateProductWarnings
{
    /**
     * Evaluate a product against the user's health conditions and allergens.
     *
     * @return array<int, array{type: string, label: string, message: string, severity: string}>
     */
    public function __invoke(User $user, Product $product): array
    {
        $conditionIds = $user->conditions->pluck('id');
        $allergenIds = $user->allergens->pluck('id');

        if ($conditionIds->isEmpty() && $allergenIds->isEmpty()) {
            return [];
        }

        $rules = AlertRule::with(['condition', 'allergen'])
            ->where(function ($query) use ($conditionIds, $allergenIds) {
                $query->whereIn('condition_id', $conditionIds)
                    ->orWhereIn('allergen_id', $allergenIds);
            })
            ->get();

        $warnings = [];

        foreach ($rules as $rule) {
            $warning = match (true) {
                $rule->condition_id !== null => $this->evaluateNutrientRule($rule, $product),
                $rule->allergen_id !== null => $this->evaluateAllergenRule($rule, $product),
                default => null,
            };

            if ($warning !== null) {
                $warnings[] = $warning;
            }
        }

        return $warnings;
    }

    /**
     * @return array{type: string, label: string, message: string, severity: string}|null
     */
    protected function evaluateNutrientRule(AlertRule $rule, Product $product): ?array
    {
        if (! $rule->nutrient_key || ! $rule->operator || $rule->threshold === null) {
            return null;
        }

        $value = ($product->nutriments ?? [])[$rule->nutrient_key] ?? null;

        if ($value === null || ! is_numeric($value)) {
            return null;
        }

        $value = (float) $value;
        $threshold = (float) $rule->threshold;

        $matches = match ($rule->operator) {
            '>' => $value > $threshold,
            '>=' => $value >= $threshold,
            '<' => $value < $threshold,
            '<=' => $value <= $threshold,
            '==', '=' => $value === $threshold,
            default => false,
        };

        if (! $matches) {
            return null;
        }

        return [
            'type' => 'condition',
            'label' => $rule->condition?->name ?? 'Kondisi',
            'message' => $rule->message,
            'severity' => 'warning',
        ];
    }

    /**
     * @return array{type: string, label: string, message: string, severity: string}|null
     */
    protected function evaluateAllergenRule(AlertRule $rule, Product $product): ?array
    {
        $allergenName = $rule->allergen?->name;

        if (! $allergenName) {
            return null;
        }

        $allergensTags = $product->raw_payload['product']['allergens_tags'] ?? [];

        $haystacks = [
            $product->ingredients_text ?? '',
            implode(' ', is_array($allergensTags) ? $allergensTags : []),
        ];

        foreach ($haystacks as $haystack) {
            if ($haystack !== '' && Str::contains($haystack, $allergenName, ignoreCase: true)) {
                return [
                    'type' => 'allergen',
                    'label' => $allergenName,
                    'message' => $rule->message,
                    'severity' => 'danger',
                ];
            }
        }

        return null;
    }
}
