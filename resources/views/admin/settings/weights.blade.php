<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuracion de Pesos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.settings.weights.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($indicators as $indicator)
                            <div class="rounded border border-gray-200 p-3">
                                <label class="block text-sm font-medium text-gray-700">{{ $indicator->code }} - {{ $indicator->name }}</label>
                                <input type="number" step="0.01" min="0" max="100" name="weights[{{ $indicator->id }}]"
                                       value="{{ old('weights.'.$indicator->id, $indicator->dashboardWeight?->weight ?? 0) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('weights')" />
                    <div>
                        <x-input-label for="reason" value="Motivo del cambio (obligatorio, genera nueva version en Documentacion)" />
                        <textarea id="reason" name="reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Guardar pesos</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
