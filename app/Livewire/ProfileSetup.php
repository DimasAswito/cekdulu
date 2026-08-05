<?php

namespace App\Livewire;

use App\Models\Allergen;
use App\Models\Condition;
use App\Models\HealthProfile;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProfileSetup extends Component
{
    /** @var array<int, int> */
    public array $conditions = [];

    /** @var array<int, int> */
    public array $allergens = [];

    public ?string $diet_goal = null;

    public ?int $daily_calorie_target = null;

    public ?string $notes = null;

    public function mount(): void
    {
        $user = auth()->user();

        $this->conditions = $user->conditions()->pluck('conditions.id')->all();
        $this->allergens = $user->allergens()->pluck('allergens.id')->all();

        $profile = $user->healthProfile;

        if ($profile) {
            $this->diet_goal = $profile->diet_goal;
            $this->daily_calorie_target = $profile->daily_calorie_target;
            $this->notes = $profile->notes;
        }
    }

    protected function rules(): array
    {
        return [
            'conditions' => ['array'],
            'conditions.*' => ['integer', 'exists:conditions,id'],
            'allergens' => ['array'],
            'allergens.*' => ['integer', 'exists:allergens,id'],
            'diet_goal' => ['nullable', 'string', 'max:255'],
            'daily_calorie_target' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $user = auth()->user();

        HealthProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'diet_goal' => $validated['diet_goal'],
                'daily_calorie_target' => $validated['daily_calorie_target'],
                'notes' => $validated['notes'],
            ]
        );

        $user->conditions()->sync($validated['conditions']);
        $user->allergens()->sync($validated['allergens']);

        $this->redirect(route('dashboard'), navigate: true);
    }

    /**
     * @return Collection<int, Condition>
     */
    public function getAllConditionsProperty(): Collection
    {
        return Condition::orderBy('name')->get();
    }

    /**
     * @return Collection<int, Allergen>
     */
    public function getAllAllergensProperty(): Collection
    {
        return Allergen::orderBy('name')->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.profile-setup');
    }
}
