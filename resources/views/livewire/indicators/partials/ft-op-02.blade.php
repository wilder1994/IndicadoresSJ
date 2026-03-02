<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Total servicios" />
        <x-text-input type="number" step="0.01" wire:model.live="form.total_servicios" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
    <div>
        <x-input-label value="No conformes" />
        <x-text-input type="number" step="0.01" wire:model.live="form.no_conformes" class="mt-1 block w-full" @disabled($isPeriodClosed) />
    </div>
</div>
