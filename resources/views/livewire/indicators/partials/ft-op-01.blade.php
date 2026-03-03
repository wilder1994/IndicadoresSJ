<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Total personal" />
        <x-text-input type="number" step="0.01" wire:model.live="form.total_personal" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
    </div>
    <div>
        <x-input-label value="Personal capacitado" />
        <x-text-input type="number" step="0.01" wire:model.live="form.personal_capacitado" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
    </div>
</div>

