<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Plantillas de Analisis</h2>
            <a href="{{ route('admin.analysis-templates.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Nueva plantilla</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                @if (session('status'))
                    <p class="mb-4 text-sm text-emerald-700">{{ session('status') }}</p>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Indicador</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Plantilla cumple</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Plantilla no cumple</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($templates as $template)
                            <tr>
                                <td class="px-3 py-2 text-sm">{{ $template->indicator?->code }} - {{ $template->indicator?->name }}</td>
                                <td class="px-3 py-2 text-sm">{{ \Illuminate\Support\Str::limit($template->plantilla_cumple, 80) }}</td>
                                <td class="px-3 py-2 text-sm">{{ \Illuminate\Support\Str::limit($template->plantilla_no_cumple, 80) }}</td>
                                <td class="px-3 py-2 text-sm">
                                    <div class="flex gap-2 items-center">
                                        <a href="{{ route('admin.analysis-templates.edit', $template) }}" class="text-indigo-700 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('admin.analysis-templates.destroy', $template) }}" onsubmit="return confirm('Eliminar plantilla?');" class="flex items-center gap-2">
                                            @csrf
                                            @method('DELETE')
                                            <input type="text" name="reason" placeholder="Motivo" class="rounded-md border-gray-300 text-xs" required>
                                            <button class="text-red-700 hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500">No hay plantillas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $templates->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
