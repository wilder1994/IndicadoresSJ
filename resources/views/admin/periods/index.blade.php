<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Periodos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->has('close'))
                <div class="rounded-md border border-red-300 bg-red-50 p-4 text-red-700">
                    <p class="font-semibold">{{ $errors->first('close') }}</p>
                    @if (session('pending_improvements'))
                        <ul class="mt-2 list-disc ps-5 text-sm">
                            @foreach (session('pending_improvements') as $item)
                                <li>{{ $item['indicator'] }} - Zona {{ $item['zone'] }} ({{ $item['result'] }}%)</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Crear periodo</h3>
                <form method="POST" action="{{ route('admin.periods.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <x-input-label for="year" value="Ano" />
                        <select id="year" name="year" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach ($years as $yearOption)
                                <option value="{{ $yearOption }}" @selected((int) old('year', now()->year) === (int) $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="month" value="Mes" />
                        <x-text-input id="month" name="month" type="number" min="1" max="12" class="mt-1 block w-full" value="{{ old('month', now()->month) }}" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Estado inicial" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="open">Abierto</option>
                            <option value="closed">Cerrado</option>
                        </select>
                    </div>
                    <div>
                        <x-primary-button>Crear</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cierre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reapertura</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($periods as $period)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium">{{ $period->year }}-{{ str_pad((string) $period->month, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3 text-sm">{{ $period->status === 'closed' ? 'Cerrado' : 'Abierto' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($period->status === 'open')
                                        <form method="POST" action="{{ route('admin.periods.close', $period) }}" class="flex gap-2 items-center">
                                            @csrf
                                            <input type="text" name="reason" placeholder="Motivo (opcional)" class="rounded-md border-gray-300 text-sm">
                                            <button type="submit" class="rounded-md bg-amber-600 px-3 py-1.5 text-xs text-white">Cerrar</button>
                                        </form>
                                    @else
                                        Cerrado el {{ optional($period->closed_at)->format('Y-m-d H:i') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($period->status === 'closed')
                                        <form method="POST" action="{{ route('admin.periods.reopen', $period) }}" class="flex gap-2 items-center">
                                            @csrf
                                            <input type="text" name="reason" placeholder="Motivo obligatorio" class="rounded-md border-gray-300 text-sm" required>
                                            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs text-white">Reabrir</button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-4 text-sm text-gray-500">No hay periodos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pt-4">{{ $periods->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
