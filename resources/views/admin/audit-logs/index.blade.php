<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Visor de Auditoria</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="event_type" value="Evento" />
                        <select id="event_type" name="event_type" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">Todos</option>
                            @foreach ($eventTypes as $eventType)
                                <option value="{{ $eventType }}" @selected(request('event_type') === $eventType)>{{ $eventType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="action" value="Accion" />
                        <select id="action" name="action" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">Todas</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="self-end">
                        <x-primary-button>Filtrar</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Accion</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Antes</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Despues</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-xs">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->user?->email ?? 'sistema' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->event_type }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->action }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->reason }}</td>
                                <td class="px-4 py-3 text-xs max-w-xs"><pre class="whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></td>
                                <td class="px-4 py-3 text-xs max-w-xs"><pre class="whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-4 text-sm text-gray-500">No hay registros de auditoria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pt-4">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
