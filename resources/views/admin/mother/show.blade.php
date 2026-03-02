<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">MADRE {{ $indicator->code }} - {{ $indicator->name }}</h2>
            <a href="{{ route('admin.mother.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Volver</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    <div>
                        <x-input-label value="Año" />
                        <x-text-input type="number" name="year" class="mt-1 block w-full" value="{{ $year }}" />
                    </div>
                    <div>
                        <x-input-label value="Mes" />
                        <x-text-input type="number" name="month" min="1" max="12" class="mt-1 block w-full" value="{{ $month }}" />
                    </div>
                    <div class="md:col-span-2">
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-white text-sm">Aplicar</button>
                    </div>
                    <div class="md:col-span-2 flex gap-2 justify-end">
                        <a href="{{ route('admin.exports.mother.excel', ['indicator' => $indicator->code, 'year' => $year, 'month' => $month]) }}" class="rounded-md border border-emerald-300 px-3 py-2 text-sm text-emerald-700">Exportar Excel</a>
                        <a href="{{ route('admin.exports.mother.pdf', ['indicator' => $indicator->code, 'year' => $year, 'month' => $month]) }}" class="rounded-md border border-rose-300 px-3 py-2 text-sm text-rose-700">Exportar PDF</a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <h3 class="font-semibold mb-3">Tabla por zonas (solo lectura)</h3>
                @php($keys = collect($monthly['rows'])->first()['display'] ?? [])
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Zona</th>
                            @foreach (array_keys($keys) as $field)
                                <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">{{ str_replace('_', ' ', $field) }}</th>
                            @endforeach
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">%</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Semáforo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($monthly['rows'] as $row)
                            <tr>
                                <td class="px-3 py-2 text-sm font-medium">{{ $row['zone']->code }}</td>
                                @foreach ($row['display'] as $value)
                                    <td class="px-3 py-2 text-sm">{{ $value ?? '-' }}</td>
                                @endforeach
                                <td class="px-3 py-2 text-sm">{{ $row['result_percentage'] !== null ? number_format((float) $row['result_percentage'], 2).'%' : '-' }}</td>
                                <td class="px-3 py-2 text-sm {{ $row['semaforo'] === 'VERDE' ? 'text-emerald-600' : 'text-red-600' }}">{{ $row['semaforo'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Consolidado MADRE</h3>
                @if ($indicator->code === 'FT-OP-03')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">A) Frecuencia</p>
                            <p>Numerador: {{ $monthly['consolidated']['a']['numerator'] }}</p>
                            <p>Denominador: {{ $monthly['consolidated']['a']['denominator'] }}</p>
                            <p>%: {{ $monthly['consolidated']['a']['result_percentage'] !== null ? number_format($monthly['consolidated']['a']['result_percentage'],2).'%' : '-' }}</p>
                            <p class="{{ $monthly['consolidated']['a']['semaforo'] === 'VERDE' ? 'text-emerald-600' : 'text-red-600' }}">{{ $monthly['consolidated']['a']['semaforo'] }}</p>
                        </div>
                        <div class="rounded border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">B) Impacto económico</p>
                            <p>Numerador: {{ $monthly['consolidated']['b']['numerator'] }}</p>
                            <p>Denominador: {{ $monthly['consolidated']['b']['denominator'] }}</p>
                            <p>%: {{ $monthly['consolidated']['b']['result_percentage'] !== null ? number_format($monthly['consolidated']['b']['result_percentage'],2).'%' : '-' }}</p>
                            <p class="{{ $monthly['consolidated']['b']['semaforo'] === 'VERDE' ? 'text-emerald-600' : 'text-red-600' }}">{{ $monthly['consolidated']['b']['semaforo'] }}</p>
                        </div>
                        <div class="rounded border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">Estado final</p>
                            <p class="text-lg font-semibold {{ $monthly['consolidated']['final'] === 'VERDE' ? 'text-emerald-600' : 'text-red-600' }}">{{ $monthly['consolidated']['final'] }}</p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="rounded border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">Numerador total</p>
                            <p class="text-lg font-semibold">{{ $monthly['consolidated']['numerator'] }}</p>
                        </div>
                        <div class="rounded border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">Denominador total</p>
                            <p class="text-lg font-semibold">{{ $monthly['consolidated']['denominator'] }}</p>
                        </div>
                        <div class="rounded border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">% consolidado</p>
                            <p class="text-lg font-semibold">{{ $monthly['consolidated']['result_percentage'] !== null ? number_format($monthly['consolidated']['result_percentage'],2).'%' : '-' }}</p>
                        </div>
                        <div class="rounded border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">Semáforo</p>
                            <p class="text-lg font-semibold {{ $monthly['consolidated']['semaforo'] === 'VERDE' ? 'text-emerald-600' : 'text-red-600' }}">{{ $monthly['consolidated']['semaforo'] }}</p>
                        </div>
                    </div>
                @endif
            </div>

            @if ($indicator->code === 'FT-OP-08' && $quarterly)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                    <h3 class="font-semibold mb-3">Consolidado trimestral FT-OP-08 ({{ $year }})</h3>
                    @foreach ($quarterly as $q => $data)
                        <h4 class="font-semibold mt-4 mb-2">{{ $q }}</h4>
                        <table class="min-w-full divide-y divide-gray-200 mb-2">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Zona</th>
                                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Programados</th>
                                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Realizados</th>
                                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">%</th>
                                    <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Semáforo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($data['zones'] as $zr)
                                    <tr>
                                        <td class="px-3 py-2 text-sm">{{ $zr['zone']->code }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $zr['denominator'] }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $zr['numerator'] }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $zr['result_percentage'] !== null ? number_format($zr['result_percentage'],2).'%' : '-' }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $zr['semaforo'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-sm"><strong>Consolidado {{ $q }}:</strong> {{ $data['consolidated']['result_percentage'] !== null ? number_format($data['consolidated']['result_percentage'],2).'%' : '-' }} ({{ $data['consolidated']['semaforo'] }})</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Gráfico 3D (mes seleccionado)</h3>
                <div id="motherChart3d" style="width: 100%; height: 480px;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts-gl@2/dist/echarts-gl.min.js"></script>
    <script>
        (function () {
            const chartEl = document.getElementById('motherChart3d');
            if (!chartEl) return;
            const chart = echarts.init(chartEl);
            const payload = @json($monthly['chart']);
            const option = {
                tooltip: {},
                xAxis3D: { type: 'category', data: payload.zones },
                yAxis3D: { type: 'category', data: ['Numerador', 'Denominador', '%', 'Meta'] },
                zAxis3D: { type: 'value' },
                grid3D: { boxWidth: 120, boxDepth: 40, light: { main: { intensity: 1.2 }, ambient: { intensity: 0.4 } }, viewControl: { alpha: 25, beta: 25 } },
                series: [
                    { type: 'bar3D', data: payload.bars, shading: 'lambert', itemStyle: { color: '#0ea5e9' } },
                    { type: 'line3D', data: payload.linePct, lineStyle: { width: 6, color: '#16a34a' } },
                    { type: 'line3D', data: payload.lineMeta, lineStyle: { width: 4, color: '#dc2626', type: 'dashed' } }
                ]
            };
            chart.setOption(option);
            window.addEventListener('resize', () => chart.resize());
        })();
    </script>
</x-app-layout>
