<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Plantilla de Analisis</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.analysis-templates.update', $analysisTemplate) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="indicator_id" value="Indicador" />
                        <select id="indicator_id" name="indicator_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                            @foreach ($indicators as $indicator)
                                <option value="{{ $indicator->id }}" @selected(old('indicator_id', $analysisTemplate->indicator_id) == $indicator->id)>
                                    {{ $indicator->code }} - {{ $indicator->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="plantilla_cumple" value="plantilla_cumple" />
                        <textarea id="plantilla_cumple" name="plantilla_cumple" rows="4" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('plantilla_cumple', $analysisTemplate->plantilla_cumple) }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="plantilla_no_cumple" value="plantilla_no_cumple" />
                        <textarea id="plantilla_no_cumple" name="plantilla_no_cumple" rows="4" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('plantilla_no_cumple', $analysisTemplate->plantilla_no_cumple) }}</textarea>
                    </div>

                    <div>
                        <x-input-label value="sugerencias_accion (una por linea)" />
                        <textarea name="sugerencias_accion_raw" rows="5" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('sugerencias_accion_raw', implode("\n", $analysisTemplate->sugerencias_accion ?? [])) }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="reason" value="Motivo del cambio" />
                        <textarea id="reason" name="reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('reason') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.analysis-templates.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Cancelar</a>
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
