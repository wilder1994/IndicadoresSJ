<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Visitas programadas" />
        <x-text-input type="number" step="0.01" wire:model.live="form.visitas_programadas" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
    </div>
    <div>
        <x-input-label value="Visitas realizadas" />
        <x-text-input type="number" step="0.01" wire:model.live="form.visitas_realizadas" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
    </div>
</div>

