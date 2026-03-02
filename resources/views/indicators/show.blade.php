<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $indicator->code }} - {{ $indicator->name }}</h2>
            <a href="{{ route('indicators.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Volver</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @switch($indicator->code)
                @case('FT-OP-01')
                    @livewire(\App\Livewire\Indicators\FtOp01Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-02')
                    @livewire(\App\Livewire\Indicators\FtOp02Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-03')
                    @livewire(\App\Livewire\Indicators\FtOp03Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-04')
                    @livewire(\App\Livewire\Indicators\FtOp04Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-05')
                    @livewire(\App\Livewire\Indicators\FtOp05Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-06')
                    @livewire(\App\Livewire\Indicators\FtOp06Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-07')
                    @livewire(\App\Livewire\Indicators\FtOp07Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-08')
                    @livewire(\App\Livewire\Indicators\FtOp08Form::class, ['indicator' => $indicator])
                    @break
                @case('FT-OP-09')
                    @livewire(\App\Livewire\Indicators\FtOp09Form::class, ['indicator' => $indicator])
                    @break
                @default
                    <div class="rounded-md border border-amber-300 bg-amber-50 p-4 text-amber-800">
                        Indicador no implementado.
                    </div>
            @endswitch
        </div>
    </div>
</x-app-layout>
