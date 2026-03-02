<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuracion Analisis Inteligente</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.settings.analysis.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="mode" value="Modo activo" />
                        <select id="mode" name="mode" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="rules" @selected(old('mode', $setting->mode) === 'rules')>Modo 1 - Reglas</option>
                            <option value="local_ai" @selected(old('mode', $setting->mode) === 'local_ai')>Modo 2 - IA Local</option>
                            <option value="openai" @selected(old('mode', $setting->mode) === 'openai')>Modo 3 - OpenAI API</option>
                        </select>
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2">
                            <input type="hidden" name="rules_enabled" value="0">
                            <input type="checkbox" name="rules_enabled" value="1" @checked(old('rules_enabled', $setting->rules_enabled))>
                            <span>Reglas habilitadas</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="local_endpoint_url" value="URL IA Local" />
                            <x-text-input id="local_endpoint_url" name="local_endpoint_url" type="url" class="mt-1 block w-full" value="{{ old('local_endpoint_url', $setting->local_endpoint_url) }}" />
                        </div>
                        <div>
                            <x-input-label for="local_model" value="Modelo IA Local" />
                            <x-text-input id="local_model" name="local_model" type="text" class="mt-1 block w-full" value="{{ old('local_model', $setting->local_model) }}" />
                        </div>
                        <div>
                            <x-input-label for="local_timeout_ms" value="Timeout IA Local (ms)" />
                            <x-text-input id="local_timeout_ms" name="local_timeout_ms" type="number" class="mt-1 block w-full" value="{{ old('local_timeout_ms', $setting->local_timeout_ms) }}" />
                        </div>
                        <div>
                            <x-input-label for="openai_model" value="Modelo OpenAI" />
                            <x-text-input id="openai_model" name="openai_model" type="text" class="mt-1 block w-full" value="{{ old('openai_model', $setting->openai_model) }}" />
                        </div>
                        <div>
                            <x-input-label for="openai_timeout_ms" value="Timeout OpenAI (ms)" />
                            <x-text-input id="openai_timeout_ms" name="openai_timeout_ms" type="number" class="mt-1 block w-full" value="{{ old('openai_timeout_ms', $setting->openai_timeout_ms) }}" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="reason" value="Motivo del cambio" />
                        <textarea id="reason" name="reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('reason') }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Guardar configuracion</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
