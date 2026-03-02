<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('IndicadoresSJ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="rounded-md bg-gray-50 p-4 border border-gray-200">
                        <p><span class="font-semibold">Usuario:</span> {{ auth()->user()->name }}</p>
                        <p><span class="font-semibold">Rol:</span> {{ strtoupper(auth()->user()->role) }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-lg mb-2">Zonas disponibles</h3>
                        @if ($zones->isEmpty())
                            <p class="text-sm text-gray-600">No hay zonas asignadas.</p>
                        @else
                            <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($zones as $zone)
                                    <li>
                                        <a href="{{ route('zones.show', $zone) }}"
                                           class="block rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50 transition">
                                            <p class="font-semibold">{{ $zone->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $zone->code }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    @if (auth()->user()->isAdmin())
                        <div>
                            <h3 class="font-semibold text-lg mb-2">Administracion</h3>
                            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                <a href="{{ route('admin.users.index') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Usuarios</a>
                                <a href="{{ route('admin.zones.index') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Zonas</a>
                                <a href="{{ route('admin.periods.index') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Periodos</a>
                                <a href="{{ route('admin.settings.analysis.edit') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Config Analisis</a>
                                <a href="{{ route('admin.settings.weights.edit') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Pesos Dashboard</a>
                                <a href="{{ route('admin.documents.index') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Documentacion</a>
                                <a href="{{ route('admin.mother.index') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Consolidado MADRE</a>
                                <a href="{{ route('admin.audit-logs.index') }}" class="rounded-md border border-gray-200 px-4 py-3 hover:border-indigo-500 hover:bg-indigo-50">Auditoria</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
