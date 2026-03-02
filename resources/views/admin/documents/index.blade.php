<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Documentacion</h2>
            <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Nuevo documento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titulo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scope</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version vigente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($documents as $document)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $document->title }}</td>
                                <td class="px-4 py-3 text-sm">{{ $document->slug }}</td>
                                <td class="px-4 py-3 text-sm">{{ $document->scope }}</td>
                                <td class="px-4 py-3 text-sm">{{ $document->currentVersion?->version_number ? 'v'.$document->currentVersion->version_number : '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.documents.show', $document) }}" class="text-indigo-600">Ver</a>
                                        <a href="{{ route('admin.documents.edit', $document) }}" class="text-indigo-600">Editar</a>
                                        <form method="POST" action="{{ route('admin.documents.destroy', $document) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Eliminar documento?')" class="text-red-600">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-4 text-sm text-gray-500">No hay documentos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pt-4">{{ $documents->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
