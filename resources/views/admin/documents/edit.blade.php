<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Documento</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.documents.update', $document) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="title" value="Titulo" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $document->title) }}" required />
                        </div>
                        <div>
                            <x-input-label for="slug" value="Slug" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug', $document->slug) }}" required />
                        </div>
                        <div>
                            <x-input-label for="scope" value="Scope" />
                            <select id="scope" name="scope" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="system" @selected(old('scope', $document->scope) === 'system')>system</option>
                                <option value="indicator" @selected(old('scope', $document->scope) === 'indicator')>indicator</option>
                                <option value="dashboard" @selected(old('scope', $document->scope) === 'dashboard')>dashboard</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="indicator_id" value="Indicador (opcional)" />
                            <select id="indicator_id" name="indicator_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">-</option>
                                @foreach ($indicators as $indicator)
                                    <option value="{{ $indicator->id }}" @selected(old('indicator_id', $document->indicator_id) == $indicator->id)>{{ $indicator->code }} - {{ $indicator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="is_active" value="Activo" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="1" @selected(old('is_active', (string) (int) $document->is_active) === '1')>Si</option>
                                <option value="0" @selected(old('is_active', (string) (int) $document->is_active) === '0')>No</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="reason" value="Motivo del cambio" />
                        <textarea id="reason" name="reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('reason') }}</textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.documents.show', $document) }}" class="px-4 py-2 text-sm rounded-md border border-gray-300">Cancelar</a>
                        <x-primary-button>Actualizar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
