<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label value="A) Total servicios" />
            <x-text-input type="number" step="0.01" wire:model.live="form.total_servicios" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
        <div>
            <x-input-label value="A) Total siniestros" />
            <x-text-input type="number" step="0.01" wire:model.live="form.total_siniestros" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
    </div>

    <div class="rounded-md border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-medium">Clasificación por tipo</h4>
            <button type="button" wire:click="addTypeRow" class="rounded-md border border-indigo-300 px-3 py-1 text-xs text-indigo-700" @disabled($isPeriodClosed)>Agregar fila</button>
        </div>
        <div class="space-y-2">
            @foreach ($form['clasificacion_por_tipo'] as $index => $row)
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-7">
                        <x-text-input type="text" wire:model.live="form.clasificacion_por_tipo.{{ $index }}.tipo" placeholder="Tipo" class="block w-full" :disabled="$isPeriodClosed" />
                    </div>
                    <div class="col-span-4">
                        <x-text-input type="number" step="0.01" wire:model.live="form.clasificacion_por_tipo.{{ $index }}.cantidad" placeholder="Cantidad" class="block w-full" :disabled="$isPeriodClosed" />
                    </div>
                    <div class="col-span-1">
                        <button type="button" wire:click="removeTypeRow({{ $index }})" class="text-red-600 text-xs" @disabled($isPeriodClosed)>X</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label value="B) Facturación mensual" />
            <x-text-input type="number" step="0.01" wire:model.live="form.facturacion_mensual" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
        <div>
            <x-input-label value="B) Valor pagado siniestros" />
            <x-text-input type="number" step="0.01" wire:model.live="form.valor_pagado_siniestros" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
    </div>
</div>


