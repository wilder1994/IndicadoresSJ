<div class="space-y-4">
    <div>
        <div style="display:flex; gap:0.75rem; align-items:flex-end; flex-wrap:nowrap;">
        <div style="flex:1 1 0;">
            <x-input-label value="Facturacion mensual" />
            <x-text-input type="number" step="0.01" wire:model.live="form.facturacion_mensual" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
        <div style="flex:1 1 0;">
            <x-input-label value="Valor pagado siniestros" />
            <x-text-input type="number" step="0.01" wire:model.live="form.valor_pagado_siniestros" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
        <div style="flex:0 0 95px;">
            <x-input-label value="Total servicios" />
            <x-text-input type="number" step="0.01" wire:model.live="form.total_servicios" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
        <div style="flex:0 0 95px;">
            <x-input-label value="Total siniestros" />
            <x-text-input type="number" step="0.01" wire:model.live="form.total_siniestros" class="mt-1 block w-full" :disabled="$isPeriodClosed" />
        </div>
        <div style="flex:0 0 140px; display:flex; align-items:flex-end;">
            <button type="button" wire:click="openClassificationModal" class="w-full h-[42px] inline-flex items-center justify-center rounded-md border border-sky-300 px-4 text-sm font-semibold text-sky-700 hover:bg-sky-50" @disabled($isPeriodClosed)>
                Clasificar siniestros
            </button>
        </div>
        <div style="flex:0 0 140px; display:flex; align-items:flex-end;">
            <button type="button" wire:click="openImprovementModal" class="w-full h-[42px] inline-flex items-center justify-center rounded-md border border-indigo-300 px-4 text-sm font-semibold text-indigo-700 hover:bg-indigo-50" @disabled($isPeriodClosed)>
                Analisis
            </button>
        </div>
        </div>
    </div>
</div>
