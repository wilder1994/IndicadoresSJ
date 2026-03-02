<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Zona') }}: {{ $zone->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-2">
                    <p><span class="font-semibold">Codigo:</span> {{ $zone->code }}</p>
                    <p><span class="font-semibold">Estado:</span> {{ $zone->is_active ? 'Activa' : 'Inactiva' }}</p>
                    <p class="text-sm text-gray-600 pt-2">
                        Este espacio servira para la captura de indicadores por zona en la siguiente fase.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
