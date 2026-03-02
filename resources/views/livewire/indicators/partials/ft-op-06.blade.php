<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Total clientes cadena" />
        <x-text-input type="number" step="0.01" wire:model.live="form.total_clientes_cadena" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
    <div>
        <x-input-label value="Eventos indeseables" />
        <x-text-input type="number" step="0.01" wire:model.live="form.eventos_indeseables" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
</div>
