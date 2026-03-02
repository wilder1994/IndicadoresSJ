<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Usuario</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>
                    <div>
                        <x-input-label for="role" value="Rol" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="usuario" @selected(old('role') === 'usuario')>USUARIO</option>
                            <option value="admin" @selected(old('role') === 'admin')>ADMIN</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('role')" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="password" value="Password" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar password" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Zonas asignadas" />
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($zones as $zone)
                                <label class="flex items-center gap-2 rounded border border-gray-200 p-2">
                                    <input type="checkbox" name="zone_ids[]" value="{{ $zone->id }}" @checked(in_array($zone->id, old('zone_ids', [])))>
                                    <span>{{ $zone->code }} - {{ $zone->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('zone_ids')" />
                        <x-input-error class="mt-2" :messages="$errors->get('zone_ids.*')" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm rounded-md border border-gray-300">Cancelar</a>
                        <x-primary-button>Guardar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
