<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Consolidado MADRE (Admin)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($indicators as $indicator)
                        <a href="{{ route('admin.mother.show', $indicator->code) }}" class="rounded-md border border-gray-200 p-4 hover:border-indigo-500 hover:bg-indigo-50">
                            <p class="font-semibold">{{ $indicator->code }}</p>
                            <p class="text-sm text-gray-700">{{ $indicator->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
