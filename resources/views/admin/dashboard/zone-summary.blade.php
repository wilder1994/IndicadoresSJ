<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Resumen Zona: {{ $zone->name }}</h2>
            <a href="{{ route('admin.dashboard.index', ['year' => $year, 'month' => $month]) }}"
               class="rounded-md border border-gray-300 px-3 py-2 text-sm">
                Volver al dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <p class="text-sm text-gray-500 mb-4">Periodo: {{ $year }}-{{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}</p>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Indicador</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Resultado</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Semaforo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($rows as $row)
                            <tr>
                                <td class="px-3 py-2 text-sm">{{ $row['indicator']->code }} - {{ $row['indicator']->name }}</td>
                                <td class="px-3 py-2 text-sm">{{ $row['result'] !== null ? number_format((float) $row['result'], 2).'%' : '-' }}</td>
                                <td class="px-3 py-2 text-sm {{ $row['semaforo'] === 'VERDE' ? 'text-emerald-600' : ($row['semaforo'] === 'ROJO' ? 'text-red-600' : 'text-gray-400') }}">
                                    {{ $row['semaforo'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
