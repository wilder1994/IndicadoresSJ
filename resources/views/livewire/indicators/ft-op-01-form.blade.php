<div class="space-y-6 ftop01-sheet">
    <style>
        .ftop01-sheet .t-title { font-size: 32px; font-weight: 700; line-height: 1.1; }
        .ftop01-sheet .t-head { font-size: 13px; font-weight: 700; line-height: 1.1; }
        .ftop01-sheet .t-body { font-size: 13px; font-weight: 400; line-height: 1.1; }
    </style>
    <div class="sticky top-0 z-20 bg-white border border-gray-200 rounded-md p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-input-label value="Ano" />
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

    @if (session('status'))
        <div class="rounded-md border border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white border border-gray-300 rounded-md p-4 overflow-x-auto">
        <table class="border-collapse table-fixed text-[13px] text-black" style="min-width: 888px;">
            <colgroup>
                @for ($c = 0; $c < 12; $c++)
                    <col style="width:74px;">
                @endfor
            </colgroup>

            <tr style="height:26px;">
                <td colspan="2" rowspan="4" class="border border-gray-600 text-center align-middle bg-gray-100">
                    <x-application-logo class="mx-auto h-20 w-20 text-sky-800" />
                </td>
                <td colspan="7" rowspan="4" class="border border-gray-600 text-center align-middle bg-gray-50 t-title">
                    FICHA DEL INDICADOR DE GESTION
                </td>
                <td colspan="3" class="border border-gray-600 px-2 t-body">{{ $indicator->code }}</td>
            </tr>
            <tr style="height:26px;">
                <td colspan="3" class="border border-gray-600 px-2 t-body">{{ ($months[$selectedMonth] ?? 'Mes').' de '.$selectedYear }}</td>
            </tr>
            <tr style="height:26px;">
                <td colspan="3" class="border border-gray-600 px-2 t-body">Version 02</td>
            </tr>
            <tr style="height:26px;">
                <td colspan="3" class="border border-gray-600 px-2 t-body">Pagina 1 de 1</td>
            </tr>

            <tr style="height:26px;" class="bg-gray-100">
                <td colspan="12" class="border border-gray-600 text-center t-head">NOMBRE DEL INDICADOR</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-50">
                <td colspan="12" class="border border-gray-600 text-center t-head">{{ $indicator->name }}</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-100">
                <td colspan="8" class="border border-gray-600 text-center t-head">OBJETIVO</td>
                <td colspan="4" class="border border-gray-600 text-center t-head">PROCESO</td>
            </tr>
            <tr style="height:26px;">
                <td colspan="8" class="border border-gray-600 px-2 t-body">Medir el grado de cumplimiento de las personas operativas capacitadas</td>
                <td colspan="4" class="border border-gray-600 text-center t-body">Gestion Operativa</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-100 text-center">
                <td colspan="2" class="border border-gray-600 t-head">UNIDAD MEDIDA</td>
                <td class="border border-gray-600 t-head">META</td>
                <td colspan="3" class="border border-gray-600 t-head">FRECUENCIA DE MEDICION</td>
                <td colspan="2" class="border border-gray-600 t-head">TENDENCIA</td>
                <td colspan="4" class="border border-gray-600 t-head">INSUMOS PARA LA MEDICION</td>
            </tr>
            <tr style="height:26px;" class="text-center">
                <td colspan="2" class="border border-gray-600 t-body">Porcentaje</td>
                <td class="border border-gray-600 t-body">{{ number_format((float) $indicator->target_value, 0) }}%</td>
                <td colspan="3" class="border border-gray-600 t-body">{{ ucfirst($indicator->frequency ?? 'Mensual') }}</td>
                <td colspan="2" class="border border-gray-600 t-body">Creciente</td>
                <td colspan="4" class="border border-gray-600 t-body">Base de datos personal operativo</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-100">
                <td colspan="12" class="border border-gray-600 text-center t-head">FORMULA</td>
            </tr>
            <tr style="height:26px;">
                <td colspan="12" class="border border-gray-600 text-center t-body">(N de personal capacitado / N Total de personal operativo)</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-100">
                <td colspan="12" class="border border-gray-600 text-center t-head">RESPONSABILIDADES</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-100 text-center">
                <td colspan="4" class="border border-gray-600 t-head">RESULTADOS Y MEDICION</td>
                <td colspan="4" class="border border-gray-600 t-head">RESULTADOS</td>
                <td colspan="4" class="border border-gray-600 t-head">MEDICION</td>
            </tr>
            <tr style="height:26px;" class="text-center">
                <td colspan="4" class="border border-gray-600 t-body">Lider de Gestion Operativa</td>
                <td colspan="4" class="border border-gray-600 t-body">N.A.</td>
                <td colspan="4" class="border border-gray-600 t-body">N.A.</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-100">
                <td colspan="12" class="border border-gray-600 text-center t-head">RESULTADOS</td>
            </tr>
            <tr style="height:26px;" class="bg-blue-100 text-center">
                @foreach ($sheetRows as $row)
                    <td class="border border-gray-600 t-head">{{ $row['month'] }}</td>
                @endforeach
            </tr>
            <tr style="height:26px;" class="bg-gray-50 text-center">
                <td colspan="12" class="border border-gray-600 t-head">{{ $selectedYear }}</td>
            </tr>
            <tr style="height:26px;" class="bg-blue-100 text-center">
                <td colspan="12" class="border border-gray-600 t-head">TOTAL PERSONAL OPERATIVO</td>
            </tr>
            <tr style="height:26px;" class="text-center">
                @foreach ($sheetRows as $row)
                    <td class="border border-gray-600 t-body">{{ rtrim(rtrim(number_format($row['total_personal'], 2, '.', ''), '0'), '.') }}</td>
                @endforeach
            </tr>
            <tr style="height:26px;" class="bg-blue-100 text-center">
                <td colspan="12" class="border border-gray-600 t-head">PERSONAL OPERATIVO CAPACITADO POR ZONA</td>
            </tr>
            <tr style="height:26px;" class="text-center">
                @foreach ($sheetRows as $row)
                    <td class="border border-gray-600 t-body">{{ rtrim(rtrim(number_format($row['personal_capacitado'], 2, '.', ''), '0'), '.') }}</td>
                @endforeach
            </tr>
            <tr style="height:26px;" class="bg-blue-100 text-center">
                <td colspan="12" class="border border-gray-600 t-head">NIVEL DE CUMPLIMIENTO PERSONAL OPERATIVO CAPACITADO</td>
            </tr>
            <tr style="height:26px;" class="text-center">
                @foreach ($sheetRows as $row)
                    <td class="border border-gray-600 t-head {{ $row['complies'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ number_format($row['result_percentage'], 2) }}%
                    </td>
                @endforeach
            </tr>
            <tr style="height:26px;" class="text-center">
                @for ($i = 0; $i < 12; $i++)
                    <td class="border border-gray-600 t-head">>= {{ number_format((float) $indicator->target_value, 0) }}%</td>
                @endfor
            </tr>
        </table>

        <div class="border border-gray-600 border-t-0" style="height:418px; width:888px; min-width:888px;">
            <div class="h-[38px] border-b border-gray-600 flex items-center justify-center t-head bg-gray-100">GRAFICOS</div>
            <div wire:ignore id="ft-op-01-chart" data-chart='@json($chartPayload)' class="w-full h-[380px]"></div>
        </div>

        <table class="border-collapse table-fixed text-[13px] text-black border border-gray-600 border-t-0" style="min-width: 888px;">
            <colgroup>
                <col style="width:74px;">
                <col style="width:74px;">
                <col style="width:502px;">
                <col style="width:74px;">
                <col style="width:74px;">
            </colgroup>
            <tr style="height:53px;" class="bg-gray-100 text-center">
                <td colspan="3" class="border border-gray-600 t-head">ANALISIS DE RESULTADOS</td>
                <td class="border border-gray-600 t-head">CUMPLE</td>
                <td class="border border-gray-600 t-head">MEJORA</td>
            </tr>
            @foreach ($sheetRows as $row)
                <tr style="height:53px;">
                    <td class="border border-gray-600 bg-gray-100 t-head text-center [writing-mode:vertical-rl] rotate-180">{{ $selectedYear }}</td>
                    <td class="border border-gray-600 bg-gray-100 t-head text-center">{{ $row['month'] }}</td>
                    <td class="border border-gray-600 px-2 align-top t-body">{{ $row['analysis'] }}</td>
                    <td class="border border-gray-600 text-center t-head">{{ $row['complies'] ? 'SI' : 'NO' }}</td>
                    <td class="border border-gray-600 text-center t-head">
                        @if ($row['complies'])
                            NO
                        @elseif ($selectedMonth === $row['month_number'])
                            <button type="button" wire:click="openImprovementModal" class="text-indigo-700 underline">SI</button>
                        @else
                            {{ $row['improvement'] ? 'SI' : 'NO' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="bg-white border border-gray-200 rounded-md p-6 space-y-4">
        <h3 class="font-semibold text-lg">{{ $indicator->code }} - {{ $indicator->name }}</h3>
        @include($fieldsView)
        <p class="text-xs text-gray-500">Campos obligatorios: datos del indicador y analisis de resultados.</p>
        <div>
            <x-input-label value="Analisis de resultados (editable)" />
            <textarea wire:model.live.debounce.300ms="analysisText" rows="5" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900" @disabled($isPeriodClosed)></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-md border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Resultado %</p>
                <p class="font-semibold">{{ number_format($resultPercentage, 2) }}%</p>
            </div>
            <div class="rounded-md border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Semaforo</p>
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
            <a href="{{ route('exports.zone.excel', ['indicator' => $indicator->code, 'year' => $selectedYear, 'month' => $selectedMonth, 'zone_id' => $selectedZoneId]) }}" class="rounded-md border border-emerald-300 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                Exportar Excel (Zona)
            </a>
            <a href="{{ route('exports.zone.pdf', ['indicator' => $indicator->code, 'year' => $selectedYear, 'month' => $selectedMonth, 'zone_id' => $selectedZoneId]) }}" class="rounded-md border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">
                Exportar PDF (Zona)
            </a>
        </div>
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
                        <x-input-label value="Analisis" />
                        <textarea wire:model.defer="improvementAnalysis" rows="5" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div>
                        <x-input-label value="Accion tomada" />
                        <textarea wire:model.defer="improvementActionTaken" rows="5" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div>
                        <x-input-label value="Accion definida" />
                        <textarea wire:model.defer="improvementActionDefined" rows="5" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeImprovementModal" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Cancelar</button>
                    <button type="button" wire:click="saveImprovement" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:bg-gray-700 focus:outline-none disabled:opacity-25">
                        Guardar mejora
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
@endassets

@script
<script>
    (function () {
        if (window.__ftop01ChartInit) return;
        window.__ftop01ChartInit = true;

        let chart = null;
        let lastPayload = null;

        function ensureEcharts(onReady) {
            if (window.echarts) {
                onReady();
                return;
            }

            const fallback = document.createElement('script');
            fallback.src = 'https://unpkg.com/echarts@5/dist/echarts.min.js';
            fallback.onload = onReady;
            document.head.appendChild(fallback);
        }

        function getPayloadFromDom() {
            const el = document.getElementById('ft-op-01-chart');
            if (!el || !el.dataset?.chart) return {};
            try {
                return JSON.parse(el.dataset.chart);
            } catch (e) {
                return {};
            }
        }

        function cylinderBar(name, data, colors, borderColor) {
            return [
                {
                    name,
                    type: 'bar',
                    barWidth: 16,
                    data,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: colors[0] },
                            { offset: 1, color: colors[1] }
                        ]),
                        borderColor,
                        borderWidth: 1,
                        shadowBlur: 6,
                        shadowColor: 'rgba(0,0,0,0.22)',
                    },
                },
                {
                    name: name + ' cap',
                    type: 'pictorialBar',
                    symbolSize: [16, 7],
                    symbolOffset: [0, -3],
                    symbolPosition: 'end',
                    z: 12,
                    tooltip: { show: false },
                    itemStyle: { color: colors[0], borderColor, borderWidth: 1 },
                    data,
                }
            ];
        }

        function buildOption(payload) {
            const months = payload.months || [];
            const total = payload.total_personal || [];
            const trained = payload.personal_capacitado || [];
            const result = payload.result_percentage || [];
            const meta = payload.meta || [];
            const year = payload.year || '';

            return {
                title: {
                    text: 'Nivel de cumplimiento personas operativas capacitadas ' + year,
                    left: 'center',
                    top: 10,
                    textStyle: { fontSize: 24, fontWeight: 'bold' }
                },
                tooltip: { trigger: 'axis' },
                grid: { left: 55, right: 30, top: 65, bottom: 35 },
                legend: {
                    bottom: 0,
                    data: ['Total personal', 'Personal capacitado', '% Cumplimiento', 'Meta']
                },
                xAxis: [{
                    type: 'category',
                    data: months,
                    axisLabel: { fontWeight: 'bold' }
                }],
                yAxis: [
                    { type: 'value', name: 'Personas' },
                    { type: 'value', name: '%', min: 0, max: 100, splitLine: { show: false } }
                ],
                series: [
                    ...cylinderBar('Total personal', total, ['#90b8ff', '#2f6fd9'], '#2a4f86'),
                    ...cylinderBar('Personal capacitado', trained, ['#d8f3a5', '#78b63f'], '#3e7f23'),
                    {
                        name: '% Cumplimiento',
                        type: 'line',
                        yAxisIndex: 1,
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 6,
                        lineStyle: { width: 3, color: '#d12f2f' },
                        itemStyle: { color: '#d12f2f' },
                        data: result,
                    },
                    {
                        name: 'Meta',
                        type: 'line',
                        yAxisIndex: 1,
                        smooth: false,
                        symbol: 'none',
                        lineStyle: { type: 'dashed', width: 2, color: '#444' },
                        data: meta,
                    }
                ],
            };
        }

        function render(payload) {
            const el = document.getElementById('ft-op-01-chart');
            if (!el || !window.echarts) return;
            lastPayload = payload;
            if (!chart) chart = echarts.init(el);
            chart.setOption(buildOption(payload), true);
        }

        function bootstrap() {
            const payload = getPayloadFromDom();
            ensureEcharts(function () {
                render(payload);
                if (chart) {
                    chart.resize();
                }
            });
        }
        window.setTimeout(bootstrap, 50);
        document.addEventListener('livewire:initialized', bootstrap);

        window.addEventListener('ft-op-01-chart-refresh', function (event) {
            const payload = event.detail.payload || {};
            render(payload);
        });

        document.addEventListener('livewire:navigated', function () {
            bootstrap();
        });

        window.addEventListener('resize', function () {
            if (chart) {
                chart.resize();
                if (lastPayload) {
                    chart.setOption(buildOption(lastPayload), false);
                }
            }
        });
    })();
</script>
@endscript
