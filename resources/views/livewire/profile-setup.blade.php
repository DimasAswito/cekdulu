<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Lengkapi Profil Kesehatan</h1>
        <p class="mt-1 text-sm text-gray-600">
            Informasi ini dipakai untuk memberi peringatan personal saat kamu scan produk makanan.
        </p>
    </div>

    <form wire:submit="save" class="space-y-8 bg-white shadow-sm rounded-xl border border-green-100 p-6 sm:p-8">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Kondisi Kesehatan</h2>
            <p class="text-sm text-gray-500 mb-3">Pilih kondisi yang kamu miliki (boleh lebih dari satu).</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($this->allConditions as $condition)
                    <label wire:key="condition-{{ $condition->id }}" class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 cursor-pointer hover:border-green-400 has-[:checked]:border-green-600 has-[:checked]:bg-green-50">
                        <input type="checkbox" wire:model="conditions" value="{{ $condition->id }}" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">{{ $condition->name }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('conditions')" class="mt-2" />
        </div>

        <div>
            <h2 class="text-base font-semibold text-gray-900">Alergi</h2>
            <p class="text-sm text-gray-500 mb-3">Pilih alergi yang kamu miliki (boleh lebih dari satu).</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($this->allAllergens as $allergen)
                    <label wire:key="allergen-{{ $allergen->id }}" class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 cursor-pointer hover:border-green-400 has-[:checked]:border-green-600 has-[:checked]:bg-green-50">
                        <input type="checkbox" wire:model="allergens" value="{{ $allergen->id }}" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">{{ $allergen->name }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('allergens')" class="mt-2" />
        </div>

        <div>
            <h2 class="text-base font-semibold text-gray-900">Target Diet</h2>
            <div class="mt-3 grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="diet_goal" value="Target (opsional)" />
                    <x-text-input wire:model="diet_goal" id="diet_goal" type="text" class="mt-1 block w-full" placeholder="mis. menurunkan berat badan" />
                    <x-input-error :messages="$errors->get('diet_goal')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="daily_calorie_target" value="Target kalori harian (opsional)" />
                    <x-text-input wire:model="daily_calorie_target" id="daily_calorie_target" type="number" min="0" class="mt-1 block w-full" placeholder="mis. 2000" />
                    <x-input-error :messages="$errors->get('daily_calorie_target')" class="mt-2" />
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="notes" value="Catatan tambahan (opsional)" />
            <textarea wire:model="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
        </div>

        <div class="flex justify-end">
            <x-primary-button>
                {{ __('Simpan & Lanjutkan') }}
            </x-primary-button>
        </div>
    </form>
</div>
