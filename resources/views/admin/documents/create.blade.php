<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Documento</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.documents.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="title" value="Titulo" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
                        </div>
                        <div>
                            <x-input-label for="slug" value="Slug" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug') }}" required />
                        </div>
                        <div>
                            <x-input-label for="scope" value="Scope" />
                            <select id="scope" name="scope" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="system">system</option>
                                <option value="indicator">indicator</option>
                                <option value="dashboard">dashboard</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="indicator_id" value="Indicador (opcional)" />
                            <select id="indicator_id" name="indicator_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">-</option>
                                @foreach ($indicators as $indicator)
                                    <option value="{{ $indicator->id }}" @selected(old('indicator_id') == $indicator->id)>{{ $indicator->code }} - {{ $indicator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="initial_status" value="Estado version inicial" />
                            <select id="initial_status" name="initial_status" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="draft">draft</option>
                                <option value="vigente">vigente</option>
                                <option value="archivado">archivado</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="is_active" value="Activo" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="1">Si</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="content" value="Contenido" />
                        <textarea id="content" name="content" rows="10" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('content') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="change_summary" value="Resumen del cambio" />
                            <x-text-input id="change_summary" name="change_summary" type="text" class="mt-1 block w-full" value="{{ old('change_summary') }}" required />
                        </div>
                        <div>
                            <x-input-label for="change_reason" value="Motivo" />
                            <x-text-input id="change_reason" name="change_reason" type="text" class="mt-1 block w-full" value="{{ old('change_reason') }}" required />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 text-sm rounded-md border border-gray-300">Cancelar</a>
                        <x-primary-button>Guardar documento</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
