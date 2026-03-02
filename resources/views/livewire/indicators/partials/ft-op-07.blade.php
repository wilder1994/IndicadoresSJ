<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Análisis programados" />
        <x-text-input type="number" step="0.01" wire:model.live="form.analisis_programados" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
    <div>
        <x-input-label value="Análisis realizados" />
        <x-text-input type="number" step="0.01" wire:model.live="form.analisis_realizados" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
</div>
