<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Zona</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.zones.update', $zone) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $zone->name) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label for="code" value="Codigo" />
                        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" value="{{ old('code', $zone->code) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('code')" />
                    </div>
                    <div>
                        <x-input-label for="is_active" value="Estado" />
                        <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="1" @selected(old('is_active', (string) (int) $zone->is_active) === '1')>Activa</option>
                            <option value="0" @selected(old('is_active', (string) (int) $zone->is_active) === '0')>Inactiva</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.zones.index') }}" class="px-4 py-2 text-sm rounded-md border border-gray-300">Cancelar</a>
                        <x-primary-button>Actualizar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
