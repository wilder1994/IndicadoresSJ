<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Inventarios programados" />
        <x-text-input type="number" step="0.01" wire:model.live="form.inventarios_programados" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
    </div>
    <div>
        <x-input-label value="Inventarios realizados" />
        <x-text-input type="number" step="0.01" wire:model.live="form.inventarios_realizados" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
    </div>
</div>

