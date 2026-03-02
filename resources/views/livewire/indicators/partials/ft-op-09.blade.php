<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Armas programadas" />
        <x-text-input type="number" step="0.01" wire:model.live="form.armas_programadas" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
    <div>
        <x-input-label value="Armas inspeccionadas" />
        <x-text-input type="number" step="0.01" wire:model.live="form.armas_inspeccionadas" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
</div>
