<div class="space-y-6 ftop01-sheet">
    <style>
        .ftop01-sheet .t-title { font-size: 32px; font-weight: 700; line-height: 1.1; }
        .ftop01-sheet .t-head { font-size: 13px; font-weight: 700; line-height: 1.1; }
        .ftop01-sheet .t-body { font-size: 13px; font-weight: 400; line-height: 1.1; }
    </style>
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

    <div class="bg-white border border-gray-200 rounded-md p-6 space-y-4">
        <h3 class="font-semibold text-lg">{{ $indicator->code }} - {{ $indicator->name }}</h3>
        @if ($indicator->code !== 'FT-OP-03')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-2">
                    @include($fieldsView)
                </div>
                <div class="flex items-end">
                    <button type="button" wire:click="openImprovementModal" class="w-full h-[42px] inline-flex items-center justify-center rounded-md border border-indigo-300 px-4 text-sm font-semibold text-indigo-700 hover:bg-indigo-50" @disabled($isPeriodClosed)>
                        Abrir modal de analisis
                    </button>
                </div>
            </div>
        @else
            @include($fieldsView)
            <div class="flex justify-end">
                <button type="button" wire:click="openImprovementModal" class="h-[42px] inline-flex items-center justify-center rounded-md border border-indigo-300 px-4 text-sm font-semibold text-indigo-700 hover:bg-indigo-50" @disabled($isPeriodClosed)>
                    Abrir modal de analisis
                </button>
            </div>
        @endif
        <p class="text-xs text-gray-500">Campos obligatorios: datos del indicador y analisis en modal.</p>
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
                    <button type="button" wire:click="openImprovementModal" class="text-indigo-600 underline" @disabled($isPeriodClosed)>
                        {{ $improvementId ? 'SI' : 'NO' }}
                    </button>
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('exports.zone.excel', ['indicator' => $indicator->code, 'year' => $selectedYear, 'month' => $selectedMonth, 'zone_id' => $selectedZoneId]) }}" class="rounded-md border border-emerald-300 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                Exportar Excel (Zona)
            </a>
            <a href="{{ route('exports.zone.pdf', ['indicator' => $indicator->code, 'year' => $selectedYear, 'month' => $selectedMonth, 'zone_id' => $selectedZoneId]) }}" class="rounded-md border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">
                Exportar PDF (Zona)
            </a>
        </div>
    </div>

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
                <td colspan="8" class="border border-gray-600 px-2 t-body">Medir el grado de cumplimiento del indicador.</td>
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
                <td colspan="2" class="border border-gray-600 t-body">{{ ucfirst((string) ($indicator->unit ?? 'Porcentaje')) }}</td>
                <td class="border border-gray-600 t-body">{{ number_format((float) $indicator->target_value, 0) }}%</td>
                <td colspan="3" class="border border-gray-600 t-body">{{ ucfirst($indicator->frequency ?? 'Mensual') }}</td>
                <td colspan="2" class="border border-gray-600 t-body">{{ ($indicator->target_operator ?? '>=') === '<=' ? 'Decreciente' : 'Creciente' }}</td>
                <td colspan="4" class="border border-gray-600 t-body">Base de datos del indicador</td>
            </tr>
            <tr style="height:26px;" class="bg-gray-100">
                <td colspan="12" class="border border-gray-600 text-center t-head">FORMULA</td>
            </tr>
            <tr style="height:26px;">
                <td colspan="12" class="border border-gray-600 text-center t-body">({{ $indicator->formula_description ?? 'N/A' }})</td>
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
                <td colspan="12" class="border border-gray-600 t-head">{{ $sheetDenominatorLabel }}</td>
            </tr>
            <tr style="height:26px;" class="text-center">
                @foreach ($sheetRows as $row)
                    <td class="border border-gray-600 t-body">{{ rtrim(rtrim(number_format($row['denominator'], 2, '.', ''), '0'), '.') }}</td>
                @endforeach
            </tr>
            <tr style="height:26px;" class="bg-blue-100 text-center">
                <td colspan="12" class="border border-gray-600 t-head">{{ $sheetNumeratorLabel }}</td>
            </tr>
            <tr style="height:26px;" class="text-center">
                @foreach ($sheetRows as $row)
                    <td class="border border-gray-600 t-body">{{ rtrim(rtrim(number_format($row['numerator'], 2, '.', ''), '0'), '.') }}</td>
                @endforeach
            </tr>
            <tr style="height:26px;" class="bg-blue-100 text-center">
                <td colspan="12" class="border border-gray-600 t-head">NIVEL DE CUMPLIMIENTO {{ strtoupper($indicator->name) }}</td>
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
                    <td class="border border-gray-600 t-head">{{ $indicator->target_operator === '<=' ? '<=' : '>=' }} {{ number_format((float) $indicator->target_value, 0) }}%</td>
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
                    <td class="border border-gray-600 text-center t-head">{{ $row['has_capture'] ? ($row['complies'] ? 'SI' : 'NO') : '' }}</td>
                    <td class="border border-gray-600 text-center t-head">{{ $row['has_capture'] ? ($row['improvement'] ? 'SI' : 'NO') : '' }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    @if ($showImprovementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-4xl rounded-md bg-white p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg">Analisis de resultados (obligatorio)</h3>
                    <button type="button" wire:click="closeImprovementModal" class="text-gray-500">Cerrar</button>
                </div>
                <div class="grid grid-cols-1 {{ $complies ? 'md:grid-cols-3' : 'md:grid-cols-4' }} gap-4">
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
                    @if (! $complies)
                        <div>
                            <x-input-label value="Debe agregar mejora" />
                            <textarea wire:model.defer="improvementRequired" rows="5" class="mt-1 block w-full rounded-md border-amber-300 bg-amber-50 text-amber-900" placeholder="Describe la mejora requerida porque no se cumplio la meta..."></textarea>
                        </div>
                    @endif
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeImprovementModal" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Cancelar</button>
                    <button type="button" wire:click="save" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:bg-gray-700 focus:outline-none disabled:opacity-25">
                        Guardar mes
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
            const denominator = payload.denominator || [];
            const numerator = payload.numerator || [];
            const result = payload.result_percentage || [];
            const meta = payload.meta || [];
            const year = payload.year || '';
            const denominatorLabel = payload.denominator_label || 'Total base';
            const numeratorLabel = payload.numerator_label || 'Total cumplido';
            const chartTitle = payload.title || ('Nivel de cumplimiento ' + year);

            return {
                title: {
                    text: chartTitle,
                    left: 'center',
                    top: 10,
                    textStyle: { fontSize: 24, fontWeight: 'bold' }
                },
                tooltip: { trigger: 'axis' },
                grid: { left: 55, right: 30, top: 65, bottom: 35 },
                legend: {
                    bottom: 0,
                    data: [denominatorLabel, numeratorLabel, '% Cumplimiento', 'Meta']
                },
                xAxis: [{
                    type: 'category',
                    data: months,
                    axisLabel: { fontWeight: 'bold' }
                }],
                yAxis: [
                    { type: 'value', name: 'Valor' },
                    { type: 'value', name: '%', min: 0, max: 100, splitLine: { show: false } }
                ],
                series: [
                    ...cylinderBar(denominatorLabel, denominator, ['#90b8ff', '#2f6fd9'], '#2a4f86'),
                    ...cylinderBar(numeratorLabel, numerator, ['#d8f3a5', '#78b63f'], '#3e7f23'),
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
