<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Zonas') }}</h2>
            <a href="{{ route('admin.zones.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Nueva zona
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codigo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zona</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios asignados</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($zones as $zone)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">{{ $zone->code }}</td>
                                        <td class="px-4 py-3 text-sm font-medium">{{ $zone->name }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $zone->users_count }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $zone->is_active ? 'Activa' : 'Inactiva' }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="flex items-center gap-3">
                                                <a class="text-indigo-600 hover:text-indigo-800" href="{{ route('admin.zones.edit', $zone) }}">Editar</a>
                                                <form method="POST" action="{{ route('admin.zones.destroy', $zone) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Eliminar zona?')">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-sm text-gray-500">No hay zonas registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-4">
                        {{ $zones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
