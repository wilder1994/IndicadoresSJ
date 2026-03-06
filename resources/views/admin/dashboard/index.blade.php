<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard General de Operaciones</h2>
            <a href="{{ route('admin.dashboard.pdf', ['year' => $year, 'month' => $month]) }}"
               class="rounded-md border border-rose-300 px-3 py-2 text-sm text-rose-700">
                Exportar PDF
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    <div>
                        <x-input-label value="Ano" />
                        <select name="year" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach ($years as $yearOption)
                                <option value="{{ $yearOption }}" @selected($year === (int) $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Mes" />
                        <x-text-input type="number" name="month" min="1" max="12" class="mt-1 block w-full" value="{{ $month }}" />
                    </div>
                    <div class="md:col-span-2">
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-white text-sm">Aplicar</button>
                    </div>
                </form>
                @if (session('status'))
                    <p class="mt-3 text-sm text-emerald-700">{{ session('status') }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Cumplimiento Global Ponderado</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-500">Score Global</p>
                        <p class="text-2xl font-semibold">{{ number_format($dashboard['global_score'], 2) }}%</p>
                    </div>
                    <div class="rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-500">Estado General</p>
                        <p class="text-2xl font-semibold
                            {{ $dashboard['global_state'] === 'ESTABLE' ? 'text-emerald-600' : ($dashboard['global_state'] === 'ATENCION' ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $dashboard['global_state'] }}
                        </p>
                    </div>
                    <div class="rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-500">Regla de estado</p>
                        <p class="text-sm text-gray-700">>=90 ESTABLE | 75-89 ATENCION | &lt;75 CRITICO</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-3">KPIs del mes (9 indicadores)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($dashboard['kpis'] as $kpi)
                        <a href="{{ $kpi['mother_url'] }}" class="bg-white shadow-sm sm:rounded-lg p-5 border border-gray-100 hover:border-indigo-200 transition">
                            <p class="text-xs text-gray-500">{{ $kpi['indicator']->code }}</p>
                            <p class="font-semibold text-gray-900">{{ $kpi['indicator']->name }}</p>
                            <p class="mt-2 text-sm text-gray-600">Resultado MADRE:
                                <strong>
                                    {{ $kpi['result'] !== null ? number_format((float) $kpi['result'], 2).'%' : '-' }}
                                </strong>
                            </p>
                            <p class="text-sm text-gray-600">Meta: <strong>{{ $kpi['meta'] }}</strong></p>
                            <p class="text-sm {{ $kpi['semaforo'] === 'VERDE' ? 'text-emerald-600' : 'text-red-600' }}">
                                Estado: <strong>{{ $kpi['semaforo'] }}</strong>
                            </p>
                            <p class="text-xs mt-2 {{ $kpi['has_improvements'] ? 'text-amber-700' : 'text-gray-400' }}">
                                {{ $kpi['has_improvements'] ? 'Tiene mejora(s) en zonas en rojo' : 'Sin mejoras registradas para rojos' }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                    <h3 class="font-semibold mb-3">Ranking de Zonas</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Posicion</th>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Zona</th>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Score</th>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Indicadores en rojo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($dashboard['zone_ranking'] as $index => $row)
                                <tr>
                                    <td class="px-3 py-2 text-sm">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 text-sm">
                                        <a href="{{ route('admin.dashboard.zone-summary', ['zone' => $row['zone']->id, 'year' => $year, 'month' => $month]) }}"
                                           class="text-indigo-700 hover:underline">
                                            {{ $row['zone']->name }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 text-sm">{{ number_format($row['score'], 2) }}%</td>
                                    <td class="px-3 py-2 text-sm">{{ $row['red_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                    <h3 class="font-semibold mb-3">Ranking de Indicadores Criticos</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Indicador</th>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Resultado</th>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Meta</th>
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Zonas rojo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($dashboard['critical_ranking'] as $row)
                                <tr>
                                    <td class="px-3 py-2 text-sm">
                                        <a href="{{ $row['mother_url'] }}" class="text-indigo-700 hover:underline">
                                            {{ $row['indicator']->code }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 text-sm">{{ $row['result'] !== null ? number_format((float) $row['result'], 2).'%' : '-' }}</td>
                                    <td class="px-3 py-2 text-sm">{{ $row['meta'] }}</td>
                                    <td class="px-3 py-2 text-sm">{{ $row['zones_red'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Tendencias (12 meses)</h3>
                <div id="trendChart" style="width: 100%; height: 420px;"></div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Resumen Ejecutivo</h3>
                <form method="POST" action="{{ route('admin.dashboard.save-summary') }}">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <textarea id="summaryText" name="summary_text" rows="8" class="w-full rounded-md border-gray-300">{{ old('summary_text', $summary?->summary_text) }}</textarea>
                    @error('summary_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-3">
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Guardar resumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
    <script>
        (function () {
            const chartEl = document.getElementById('trendChart');
            if (!chartEl) return;
            const payload = @json($dashboard['trends']);
            const chart = echarts.init(chartEl);

            chart.setOption({
                tooltip: { trigger: 'axis' },
                legend: { top: 0 },
                grid: { left: 40, right: 20, top: 36, bottom: 30 },
                xAxis: { type: 'category', data: payload.months },
                yAxis: { type: 'value' },
                series: [
                    { type: 'line', name: 'Score Global', data: payload.global, smooth: true },
                    { type: 'line', name: 'FT-OP-03', data: payload.indicators['FT-OP-03'], smooth: true },
                    { type: 'line', name: 'FT-OP-09', data: payload.indicators['FT-OP-09'], smooth: true },
                    { type: 'line', name: 'FT-OP-02', data: payload.indicators['FT-OP-02'], smooth: true }
                ]
            });
            window.addEventListener('resize', () => chart.resize());
        })();
    </script>
</x-app-layout>
