<x-app-layout>
    <x-slot name="header">
        @if($headerFilters)
            <div>
                <div class="w-full flex items-end gap-3">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate max-w-[360px]">{{ $indicator->code }} - {{ $indicator->name }}</h2>
                    <form method="GET" action="{{ route('indicators.show', $indicator) }}" class="flex items-end gap-2 flex-1 justify-end min-w-0">
                        <div class="w-[96px] shrink-0">
                            <x-input-label value="Ano" />
                            <select name="year" onchange="this.form.submit()" class="mt-1 block w-full h-[44px] py-0 rounded-md border-gray-300">
                                @foreach ($headerFilters['years'] as $year)
                                    <option value="{{ $year }}" @selected($headerFilters['selectedYear'] === (int) $year)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-[128px] shrink-0">
                            <x-input-label value="Mes" />
                            <select name="month" onchange="this.form.submit()" class="mt-1 block w-full h-[44px] py-0 rounded-md border-gray-300">
                                @foreach ($headerFilters['months'] as $monthNumber => $monthName)
                                    <option value="{{ $monthNumber }}" @selected($headerFilters['selectedMonth'] === (int) $monthNumber)>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-[248px] shrink-0">
                            <x-input-label value="Zona" />
                            <select name="zone_id" onchange="this.form.submit()" class="mt-1 block w-full h-[44px] py-0 rounded-md border-gray-300">
                                @foreach ($headerFilters['zones'] as $zone)
                                    <option value="{{ $zone->id }}" @selected($headerFilters['selectedZoneId'] === (int) $zone->id)>{{ $zone->code }} - {{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-[170px] flex items-end shrink-0">
                            <div class="w-full h-[44px] rounded-md border px-3 inline-flex items-center text-sm {{ $headerFilters['isPeriodClosed'] ? 'border-red-300 bg-red-50 text-red-700' : 'border-emerald-300 bg-emerald-50 text-emerald-700' }}">
                                {{ $headerFilters['isPeriodClosed'] ? 'Periodo cerrado' : 'Periodo abierto' }}
                            </div>
                        </div>
                    </form>
                    <a href="{{ route('indicators.index') }}" class="w-[96px] h-[44px] rounded-md border border-gray-300 px-3 inline-flex items-center justify-center text-sm whitespace-nowrap shrink-0">Volver</a>
                </div>
            </div>
        @else
            <div class="flex items-center justify-between gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $indicator->code }} - {{ $indicator->name }}</h2>
                <a href="{{ route('indicators.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Volver</a>
            </div>
        @endif
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
