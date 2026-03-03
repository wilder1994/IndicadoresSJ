<div class="space-y-6">
    <div class="sticky top-0 z-20 bg-white border border-gray-200 rounded-md p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-input-label value="Año" />
                <select wire:model.live="selectedYear" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Mes" />
                <select wire:model.live="selectedMonth" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach ($months as $monthNumber => $monthName)
                        <option value="{{ $monthNumber }}">{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Zona" />
                <select wire:model.live="selectedZoneId" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach ($zones as $zone)
                        <option value="{{ $zone['id'] }}">{{ $zone['code'] }} - {{ $zone['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <div class="w-full rounded-md border px-3 py-2 text-sm {{ $isPeriodClosed ? 'border-red-300 bg-red-50 text-red-700' : 'border-emerald-300 bg-emerald-50 text-emerald-700' }}">
                    {{ $isPeriodClosed ? 'Periodo cerrado' : 'Periodo abierto' }}
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-md border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-md p-6 space-y-4">
        <h3 class="font-semibold text-lg">{{ $indicator->code }} - {{ $indicator->name }}</h3>

        @include($fieldsView)

        <div>
            <x-input-label value="Análisis de resultados (editable)" />
            <textarea wire:model.live="analysisText" rows="5" class="mt-1 block w-full rounded-md border-gray-300" @disabled($isPeriodClosed)></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-md border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Resultado %</p>
                <p class="font-semibold">{{ number_format($resultPercentage, 2) }}%</p>
            </div>
            <div class="rounded-md border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Semáforo</p>
                <p class="font-semibold {{ $complies ? 'text-emerald-600' : 'text-red-600' }}">{{ $semaforo }}</p>
            </div>
            <div class="rounded-md border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Cumple</p>
                <p class="font-semibold">{{ $complies ? 'SI' : 'NO' }}</p>
            </div>
            <div class="rounded-md border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Mejora</p>
                <p class="font-semibold">
                    @if (! $complies)
                        <button type="button" wire:click="openImprovementModal" class="text-indigo-600 underline">SI</button>
                    @else
                        NO
                    @endif
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button" wire:click="save" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:bg-gray-700 focus:outline-none disabled:opacity-25" @disabled($isPeriodClosed)>
                Guardar mes
            </button>
            <button type="button" wire:click="generateSuggestion" class="rounded-md border border-indigo-300 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50" @disabled($isPeriodClosed)>
                Generar sugerencia
            </button>
            <a href="{{ route('exports.zone.excel', ['indicator' => $indicator->code, 'year' => $selectedYear, 'month' => $selectedMonth, 'zone_id' => $selectedZoneId]) }}"
               class="rounded-md border border-emerald-300 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                Exportar Excel (Zona)
            </a>
            <a href="{{ route('exports.zone.pdf', ['indicator' => $indicator->code, 'year' => $selectedYear, 'month' => $selectedMonth, 'zone_id' => $selectedZoneId]) }}"
               class="rounded-md border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">
                Exportar PDF (Zona)
            </a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-md p-6">
        <h4 class="font-semibold mb-3">Tendencia últimos 3 meses (solo lectura)</h4>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Periodo</th>
                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Resultado</th>
                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Semáforo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($trendRows as $row)
                    <tr>
                        <td class="px-3 py-2 text-sm">{{ $row['year'] }}-{{ str_pad((string) $row['month'], 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-3 py-2 text-sm">{{ $row['result'] !== null ? number_format((float) $row['result'], 2).'%' : '-' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $row['semaforo'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($showImprovementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-4xl rounded-md bg-white p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg">Mejora global obligatoria</h3>
                    <button type="button" wire:click="closeImprovementModal" class="text-gray-500">Cerrar</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label value="Análisis" />
                        <textarea wire:model.live="improvementAnalysis" rows="5" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div>
                        <x-input-label value="Acción tomada" />
                        <textarea wire:model.live="improvementActionTaken" rows="5" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div>
                        <x-input-label value="Acción definida" />
                        <textarea wire:model.live="improvementActionDefined" rows="5" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeImprovementModal" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Cancelar</button>
                    <button
                        type="button"
                        wire:click="saveImprovement"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:bg-gray-700 focus:outline-none disabled:opacity-25"
                        @disabled(trim($improvementAnalysis) === '' || trim($improvementActionTaken) === '' || trim($improvementActionDefined) === '')
                    >
                        Guardar mejora
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
