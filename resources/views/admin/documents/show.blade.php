<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $document->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.documents.edit', $document) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Editar metadatos</a>
                <a href="{{ route('admin.documents.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p><span class="font-semibold">Slug:</span> {{ $document->slug }}</p>
                <p><span class="font-semibold">Scope:</span> {{ $document->scope }}</p>
                <p><span class="font-semibold">Indicador:</span> {{ $document->indicator?->code ?? '-' }}</p>
                <p><span class="font-semibold">Version vigente:</span> {{ $document->currentVersion?->version_number ? 'v'.$document->currentVersion->version_number : '-' }}</p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Nueva version</h3>
                <form method="POST" action="{{ route('admin.documents.versions.store', $document) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="status" value="Estado" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="draft">draft</option>
                            <option value="vigente">vigente</option>
                            <option value="archivado">archivado</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="content" value="Contenido" />
                        <textarea id="content" name="content" rows="10" class="mt-1 block w-full rounded-md border-gray-300" required>{{ old('content', $document->currentVersion?->content) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="change_summary" value="Resumen del cambio" />
                            <x-text-input id="change_summary" name="change_summary" type="text" class="mt-1 block w-full" value="{{ old('change_summary') }}" required />
                        </div>
                        <div>
                            <x-input-label for="change_reason" value="Motivo del cambio" />
                            <x-text-input id="change_reason" name="change_reason" type="text" class="mt-1 block w-full" value="{{ old('change_reason') }}" required />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Crear version</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <h3 class="font-semibold mb-4">Historial de versiones</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Autor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resumen</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($document->versions->sortByDesc('version_number') as $version)
                            <tr>
                                <td class="px-4 py-3 text-sm">v{{ $version->version_number }}</td>
                                <td class="px-4 py-3 text-sm">{{ $version->status }}</td>
                                <td class="px-4 py-3 text-sm">{{ $version->author?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $version->change_summary }}</td>
                                <td class="px-4 py-3 text-sm">{{ $version->change_reason }}</td>
                                <td class="px-4 py-3 text-sm">{{ $version->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
