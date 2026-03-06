<x-app-layout>
    <x-slot name="header">
        <div class="w-full flex items-end gap-3">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Tablero general</p>
                <h2 class="mt-1 truncate text-xl font-semibold leading-tight text-slate-800">Panorama operativo</h2>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-2 shrink-0">
                <div class="w-[96px] shrink-0">
                    <x-input-label value="Ano" />
                    <select name="year" onchange="this.form.submit()" class="mt-1 block h-[44px] w-full rounded-md border-gray-300 py-0">
                        @foreach ($headerFilters['years'] as $year)
                            <option value="{{ $year }}" @selected($headerFilters['selectedYear'] === (int) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-[128px] shrink-0">
                    <x-input-label value="Mes" />
                    <select name="month" onchange="this.form.submit()" class="mt-1 block h-[44px] w-full rounded-md border-gray-300 py-0">
                        @foreach ($headerFilters['months'] as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected($headerFilters['selectedMonth'] === (int) $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </x-slot>

    @php
        $summary = $dashboard['summary'];
        $comparison = $dashboard['comparison'];
        $singleZoneView = $summary['zone_count'] === 1;
        $primaryZone = $dashboard['zone_cards'][0] ?? null;
        $stateTone = match ($summary['global_state']) {
            'Estable' => 'border-emerald-300 bg-emerald-50 text-emerald-700',
            'Atencion' => 'border-amber-300 bg-amber-50 text-amber-700',
            'Critico' => 'border-rose-300 bg-rose-50 text-rose-700',
            default => 'border-slate-300 bg-slate-50 text-slate-700',
        };
        $scoreTone = $summary['score_delta'] >= 0 ? 'text-emerald-600' : 'text-rose-600';
        $coverageTone = $summary['coverage_delta'] >= 0 ? 'text-emerald-600' : 'text-rose-600';
        $alertsTone = $summary['alerts_delta'] <= 0 ? 'text-emerald-600' : 'text-rose-600';
        $adminLinks = auth()->user()->isAdmin()
            ? [
                ['label' => 'Dashboard Ops', 'url' => route('admin.dashboard.index')],
                ['label' => 'MADRE', 'url' => route('admin.mother.index')],
                ['label' => 'Periodos', 'url' => route('admin.periods.index')],
                ['label' => 'Pesos', 'url' => route('admin.settings.weights.edit')],
            ]
            : [];
    @endphp

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-[linear-gradient(135deg,#ffffff_0%,#f7fbff_55%,#eef5ff_100%)] shadow-sm">
                <div class="grid gap-8 px-6 py-7 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                    <div>
                        <div class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] {{ $stateTone }}">
                            {{ $summary['global_state'] }}
                        </div>
                        <h3 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">
                            {{ $singleZoneView ? 'Lectura operativa de tu zona' : 'Portafolio operativo de zonas' }}
                        </h3>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                            {{ $dashboard['headline'] }}
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span class="rounded-full bg-white/90 px-3 py-1 shadow-sm ring-1 ring-slate-200">{{ $summary['zone_count'] }} zonas visibles</span>
                            <span class="rounded-full bg-white/90 px-3 py-1 shadow-sm ring-1 ring-slate-200">Periodo {{ $comparison['current_period_label'] }}</span>
                            <span class="rounded-full bg-white/90 px-3 py-1 shadow-sm ring-1 ring-slate-200">Comparado con {{ $comparison['previous_period_label'] }}</span>
                        </div>
                    </div>

                    <div class="grid gap-3 [grid-template-columns:repeat(auto-fit,minmax(220px,1fr))]">
                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Score medio</p>
                            <p class="mt-4 text-[2rem] font-semibold leading-none text-slate-900">{{ number_format($summary['avg_score'], 2, '.', ',') }}</p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Promedio ponderado por cobertura real.</p>
                            <div class="mt-4 h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-slate-900" style="width: {{ max(0, min(100, $summary['avg_score'])) }}%"></div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Cobertura media</p>
                            <p class="mt-4 text-[2rem] font-semibold leading-none text-slate-900">{{ number_format($summary['avg_coverage'], 2, '.', ',') }}%</p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Capturas registradas sobre indicadores activos.</p>
                            <div class="mt-4 h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-sky-500" style="width: {{ max(0, min(100, $summary['avg_coverage'])) }}%"></div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-rose-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-500">Alertas</p>
                            <p class="mt-4 text-[2rem] font-semibold leading-none text-slate-900">{{ $summary['alerts_count'] }}</p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Indicadores en rojo o sin registro.</p>
                        </div>

                        <div class="rounded-3xl border border-amber-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-500">Zonas criticas</p>
                            <p class="mt-4 text-[2rem] font-semibold leading-none text-slate-900">{{ $summary['critical_count'] }}</p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Necesitan atencion prioritaria.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Lectura ejecutiva</p>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">Balance del periodo</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $summary['global_state'] }}</span>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600">Estables</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['stable_count'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-600">Atencion</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['attention_count'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-600">Criticas</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['critical_count'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Sin datos</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['no_data_count'] }}</p>
                    </div>
                </div>

                <div class="mt-6 border-t border-slate-200 pt-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Resumen de alertas</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">Focos de atencion del periodo</h3>

                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @forelse ($dashboard['attention_zones'] as $row)
                            <a href="{{ $row['zone_url'] }}" class="block rounded-2xl border border-slate-200 bg-slate-50/80 p-4 transition hover:border-slate-300 hover:bg-white">
                                <div class="flex h-full flex-col">
                                    <div class="flex items-start justify-between gap-4">
                                        <p class="font-semibold text-slate-900">{{ $row['zone']->name }}</p>
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                            {{ $row['summary']['attention_count'] }} alertas
                                        </span>
                                    </div>
                                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $row['headline'] }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5 text-sm text-emerald-700 md:col-span-2 xl:col-span-4">
                                No hay alertas activas para el periodo seleccionado.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="grid gap-6 {{ $singleZoneView ? 'xl:grid-cols-[1.1fr_0.9fr]' : 'xl:grid-cols-[0.92fr_1.08fr]' }}">
                @if ($singleZoneView && $primaryZone)
                    @php
                        $zoneSummary = $primaryZone['summary'];
                        $zoneTone = match ($zoneSummary['state']) {
                            'Estable' => ['border' => 'border-emerald-300', 'bg' => 'bg-emerald-50/60', 'chip' => 'bg-emerald-100 text-emerald-700', 'bar' => 'bg-emerald-500'],
                            'Atencion' => ['border' => 'border-amber-300', 'bg' => 'bg-amber-50/60', 'chip' => 'bg-amber-100 text-amber-700', 'bar' => 'bg-amber-500'],
                            'Critico' => ['border' => 'border-rose-300', 'bg' => 'bg-rose-50/60', 'chip' => 'bg-rose-100 text-rose-700', 'bar' => 'bg-rose-500'],
                            default => ['border' => 'border-slate-300', 'bg' => 'bg-slate-50/60', 'chip' => 'bg-slate-200 text-slate-700', 'bar' => 'bg-slate-400'],
                        };
                    @endphp
                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Acceso principal</p>
                                <h3 class="mt-2 text-xl font-semibold text-slate-900">Zona asignada</h3>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $zoneTone['chip'] }}">{{ $zoneSummary['state'] }}</span>
                        </div>

                        <article class="mt-6 relative overflow-hidden rounded-[26px] border {{ $zoneTone['border'] }} {{ $zoneTone['bg'] }} p-6 shadow-sm">
                            <div class="absolute inset-x-0 top-0 h-1.5 {{ $zoneTone['bar'] }}"></div>
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $primaryZone['zone']->code }}</p>
                                    <h4 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $primaryZone['zone']->name }}</h4>
                                </div>
                                <a href="{{ $primaryZone['zone_url'] }}" class="inline-flex shrink-0 items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                    Abrir zona
                                </a>
                            </div>

                            <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">{{ $primaryZone['headline'] }}</p>

                            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl bg-white/85 p-4 ring-1 ring-slate-200/80">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Score</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($zoneSummary['score'], 2, '.', ',') }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/85 p-4 ring-1 ring-slate-200/80">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Cobertura</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($zoneSummary['coverage'], 2, '.', ',') }}%</p>
                                </div>
                                <div class="rounded-2xl bg-white/85 p-4 ring-1 ring-slate-200/80">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Alertas</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $zoneSummary['attention_count'] }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex items-center justify-between gap-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                                    <span>Salud operativa</span>
                                    <span>{{ number_format($zoneSummary['score'], 2, '.', ',') }}</span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-white/80 ring-1 ring-slate-200">
                                    <div class="h-2 rounded-full {{ $zoneTone['bar'] }}" style="width: {{ max(0, min(100, $zoneSummary['score'])) }}%"></div>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-5 lg:grid-cols-[1fr_1fr]">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Indicadores destacados</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @forelse ($primaryZone['indicator_cards'] as $indicatorCard)
                                            <span class="rounded-full border border-slate-200 bg-white/90 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-600">
                                                {{ $indicatorCard['indicator']->code }} {{ strtoupper($indicatorCard['status_label']) }}
                                            </span>
                                        @empty
                                            <span class="rounded-full border border-slate-200 bg-white/90 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                                                Sin actividad
                                            </span>
                                        @endforelse
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Atender primero</p>
                                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                                        @forelse ($primaryZone['top_attention'] as $attentionTitle)
                                            <li>{{ $attentionTitle }}</li>
                                        @empty
                                            <li>Sin focos criticos en este periodo.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="grid gap-6">
                        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Actividad reciente</p>
                                    <h3 class="mt-2 text-xl font-semibold text-slate-900">Ultimos movimientos</h3>
                                </div>
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                    {{ count($dashboard['recent_activity']) }} registros
                                </span>
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-2">
                                @forelse ($dashboard['recent_activity'] as $activity)
                                    <a href="{{ $activity['zone_url'] }}" class="group block rounded-2xl border border-slate-200 bg-slate-50/80 p-4 transition hover:border-slate-300 hover:bg-white">
                                        <p class="font-semibold text-slate-900">{{ $activity['zone_name'] }}</p>
                                        <p class="mt-1 text-sm text-slate-600">{{ $activity['title'] }}</p>
                                        <p class="mt-2 text-sm text-slate-500">Periodo {{ $activity['period_label'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">Resultado {{ $activity['result_label'] }}</p>
                                        <p class="mt-3 text-xs text-slate-400">{{ $activity['updated_at_label'] }}</p>
                                    </a>
                                @empty
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-500 md:col-span-2">
                                        Todavia no hay actividad registrada.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Acceso por zona</p>
                                <h3 class="mt-2 text-xl font-semibold text-slate-900">Portafolio operativo</h3>
                            </div>
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                {{ count($dashboard['zone_cards']) }} tarjetas
                            </span>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            @foreach ($dashboard['zone_cards'] as $row)
                                @php
                                    $zoneTone = match ($row['summary']['state']) {
                                        'Estable' => ['border' => 'border-emerald-300', 'bg' => 'bg-emerald-50/60', 'chip' => 'bg-emerald-100 text-emerald-700', 'bar' => 'bg-emerald-500'],
                                        'Atencion' => ['border' => 'border-amber-300', 'bg' => 'bg-amber-50/60', 'chip' => 'bg-amber-100 text-amber-700', 'bar' => 'bg-amber-500'],
                                        'Critico' => ['border' => 'border-rose-300', 'bg' => 'bg-rose-50/60', 'chip' => 'bg-rose-100 text-rose-700', 'bar' => 'bg-rose-500'],
                                        default => ['border' => 'border-slate-300', 'bg' => 'bg-slate-50/60', 'chip' => 'bg-slate-200 text-slate-700', 'bar' => 'bg-slate-400'],
                                    };
                                @endphp
                                <article class="relative overflow-hidden rounded-[24px] border {{ $zoneTone['border'] }} {{ $zoneTone['bg'] }} p-5 shadow-sm">
                                    <div class="absolute inset-x-0 top-0 h-1.5 {{ $zoneTone['bar'] }}"></div>
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $row['zone']->code }}</p>
                                            <h4 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $row['zone']->name }}</h4>
                                        </div>
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $zoneTone['chip'] }}">
                                            {{ $row['summary']['state'] }}
                                        </span>
                                    </div>

                                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $row['headline'] }}</p>

                                    <div class="mt-5 grid grid-cols-3 gap-3 text-sm">
                                        <div class="rounded-2xl bg-white/85 p-3 ring-1 ring-slate-200/80">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Score</p>
                                            <p class="mt-1 font-semibold text-slate-800">{{ number_format($row['summary']['score'], 2, '.', ',') }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-white/85 p-3 ring-1 ring-slate-200/80">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Cobertura</p>
                                            <p class="mt-1 font-semibold text-slate-800">{{ number_format($row['summary']['coverage'], 2, '.', ',') }}%</p>
                                        </div>
                                        <div class="rounded-2xl bg-white/85 p-3 ring-1 ring-slate-200/80">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Alertas</p>
                                            <p class="mt-1 font-semibold text-slate-800">{{ $row['summary']['attention_count'] }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <div class="flex items-center justify-between gap-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                                            <span>Salud operativa</span>
                                            <span>{{ number_format($row['summary']['score'], 2, '.', ',') }}</span>
                                        </div>
                                        <div class="mt-2 h-2 rounded-full bg-white/80 ring-1 ring-slate-200">
                                            <div class="h-2 rounded-full {{ $zoneTone['bar'] }}" style="width: {{ max(0, min(100, $row['summary']['score'])) }}%"></div>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex flex-wrap gap-2">
                                        @forelse ($row['indicator_cards'] as $indicatorCard)
                                            <span class="rounded-full border border-slate-200 bg-white/90 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-600">
                                                {{ $indicatorCard['indicator']->code }} {{ strtoupper($indicatorCard['status_label']) }}
                                            </span>
                                        @empty
                                            <span class="rounded-full border border-slate-200 bg-white/90 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                                                Sin actividad
                                            </span>
                                        @endforelse
                                    </div>

                                    <div class="mt-5">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Atender primero</p>
                                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                                            @forelse ($row['top_attention'] as $attentionTitle)
                                                <li>{{ $attentionTitle }}</li>
                                            @empty
                                                <li>Sin focos criticos en este periodo.</li>
                                            @endforelse
                                        </ul>
                                    </div>

                                    <div class="mt-6 flex items-center justify-between gap-4">
                                        <a href="{{ $row['zone_url'] }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                            Abrir zona
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-6">
                        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Carga operativa</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">Alertas por zona</h3>
                            <div id="dashboard-alerts-chart" class="mt-6 h-[280px]" data-payload='@json($dashboard['alerts_per_zone'])'></div>
                        </div>

                        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Ultimos movimientos</p>
                                    <h3 class="mt-2 text-xl font-semibold text-slate-900">Actividad reciente</h3>
                                </div>
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                    {{ count($dashboard['recent_activity']) }} registros
                                </span>
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-2">
                                @forelse ($dashboard['recent_activity'] as $activity)
                                    <a href="{{ $activity['zone_url'] }}" class="group block rounded-2xl border border-slate-200 bg-slate-50/80 p-4 transition hover:border-slate-300 hover:bg-white">
                                        <p class="font-semibold text-slate-900">{{ $activity['zone_name'] }}</p>
                                        <p class="mt-1 text-sm text-slate-600">{{ $activity['title'] }}</p>
                                        <p class="mt-2 text-sm text-slate-500">Periodo {{ $activity['period_label'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">Resultado {{ $activity['result_label'] }}</p>
                                        <p class="mt-3 text-xs text-slate-400">{{ $activity['updated_at_label'] }}</p>
                                    </a>
                                @empty
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-500 md:col-span-2">
                                        Todavia no hay actividad registrada.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        @if (! empty($adminLinks))
                            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Accesos rapidos</p>
                                <h3 class="mt-2 text-xl font-semibold text-slate-900">Panel administrador</h3>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    @foreach ($adminLinks as $link)
                                        <a href="{{ $link['url'] }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </section>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
        <script>
            (function () {
                function parsePayload(id) {
                    const el = document.getElementById(id);
                    if (!el || !el.dataset.payload) return null;

                    try {
                        return JSON.parse(el.dataset.payload);
                    } catch (error) {
                        return null;
                    }
                }

                function renderAlertsChart() {
                    const el = document.getElementById('dashboard-alerts-chart');
                    const payload = parsePayload('dashboard-alerts-chart');
                    if (!el || !payload || !window.echarts) return;

                    const chart = echarts.getInstanceByDom(el) || echarts.init(el);
                    chart.setOption({
                        animationDuration: 500,
                        color: ['#0f172a'],
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: { type: 'shadow' }
                        },
                        grid: {
                            left: 12,
                            right: 12,
                            top: 16,
                            bottom: 24,
                            containLabel: true
                        },
                        xAxis: {
                            type: 'category',
                            data: payload.labels ?? [],
                            axisLine: { lineStyle: { color: '#cbd5e1' } },
                            axisLabel: { color: '#64748b' }
                        },
                        yAxis: {
                            type: 'value',
                            axisLine: { show: false },
                            splitLine: { lineStyle: { color: '#e2e8f0' } },
                            axisLabel: { color: '#64748b' }
                        },
                        series: [{
                            type: 'bar',
                            data: payload.values ?? [],
                            barMaxWidth: 38,
                            itemStyle: {
                                borderRadius: [10, 10, 0, 0]
                            }
                        }]
                    }, true);
                }

                function resizeCharts() {
                    ['dashboard-alerts-chart'].forEach(function (id) {
                        const el = document.getElementById(id);
                        if (!el || !window.echarts) return;
                        const instance = echarts.getInstanceByDom(el);
                        if (instance) instance.resize();
                    });
                }

                function init() {
                    renderAlertsChart();
                    resizeCharts();
                }

                document.addEventListener('DOMContentLoaded', init);
                document.addEventListener('livewire:navigated', init);
                window.addEventListener('resize', resizeCharts);
            })();
        </script>
    @endpush
</x-app-layout>
