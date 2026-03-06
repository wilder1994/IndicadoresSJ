<x-app-layout>
    <x-slot name="header">
        <div class="w-full flex items-end gap-3">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Tablero de zona</p>
                <h2 class="mt-1 truncate text-xl font-semibold leading-tight text-slate-800">{{ $zone->name }}</h2>
            </div>

            <form method="GET" action="{{ route('zones.show', $zone) }}" class="flex items-end gap-2 shrink-0">
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

                <div class="w-[170px] shrink-0">
                    <x-input-label value="Estado" />
                    @php
                        $periodBadgeClass = match ($headerFilters['periodStatus']) {
                            'Periodo cerrado' => 'border-rose-300 bg-rose-50 text-rose-700',
                            'Periodo abierto' => 'border-emerald-300 bg-emerald-50 text-emerald-700',
                            default => 'border-amber-300 bg-amber-50 text-amber-700',
                        };
                    @endphp
                    <div class="mt-1 inline-flex h-[44px] w-full items-center rounded-md border px-3 text-sm {{ $periodBadgeClass }}">
                        {{ $headerFilters['periodStatus'] }}
                    </div>
                </div>
            </form>

            <a href="{{ route('dashboard') }}" class="inline-flex h-[44px] w-[96px] shrink-0 items-center justify-center rounded-md border border-gray-300 px-3 text-sm text-slate-700">
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $summary = $dashboard['summary'];
        $comparison = $dashboard['comparison'];
        $stateTone = match ($summary['state']) {
            'Estable' => 'border-emerald-300 bg-emerald-50 text-emerald-700',
            'Atencion' => 'border-amber-300 bg-amber-50 text-amber-700',
            'Critico' => 'border-rose-300 bg-rose-50 text-rose-700',
            default => 'border-slate-300 bg-slate-50 text-slate-700',
        };
        $scoreDeltaTone = $summary['score_delta'] >= 0 ? 'text-emerald-600' : 'text-rose-600';
        $coverageDeltaTone = $summary['coverage_delta'] >= 0 ? 'text-emerald-600' : 'text-rose-600';
        $attentionDeltaTone = $summary['attention_delta'] <= 0 ? 'text-emerald-600' : 'text-rose-600';
        $firstAttention = $dashboard['attention'][0] ?? null;
        $latestActivity = $dashboard['recent_activity'][0] ?? null;
    @endphp

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-[linear-gradient(135deg,#ffffff_0%,#f7fbff_55%,#eef5ff_100%)] shadow-sm">
                <div class="grid gap-8 px-6 py-7 lg:grid-cols-[1.25fr_0.95fr] lg:px-8">
                    <div>
                        <div class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] {{ $stateTone }}">
                            {{ $summary['state'] }}
                        </div>
                        <h3 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">
                            Panorama operativo de {{ $zone->name }}
                        </h3>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                            {{ $dashboard['headline'] }}
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span class="rounded-full bg-white/90 px-3 py-1 shadow-sm ring-1 ring-slate-200">Codigo {{ $zone->code }}</span>
                            <span class="rounded-full bg-white/90 px-3 py-1 shadow-sm ring-1 ring-slate-200">Periodo {{ strtolower($headerFilters['periodStatus']) }}</span>
                            <span class="rounded-full bg-white/90 px-3 py-1 shadow-sm ring-1 ring-slate-200">{{ count($dashboard['cards']) }} indicadores activos</span>
                            <span class="rounded-full bg-white/90 px-3 py-1 shadow-sm ring-1 ring-slate-200">Comparado con {{ $comparison['previous_period_label'] }}</span>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm xl:col-span-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Score zona</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($summary['score'], 2, '.', ',') }}</p>
                            <p class="mt-1 text-xs text-slate-500">Base ponderada por los 9 indicadores.</p>
                            <p class="mt-2 text-xs font-semibold {{ $scoreDeltaTone }}">
                                {{ $summary['score_delta'] >= 0 ? '+' : '' }}{{ number_format($summary['score_delta'], 2, '.', ',') }} vs {{ $comparison['previous_period_label'] }}
                            </p>
                            <div class="mt-4 h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-slate-900" style="width: {{ max(0, min(100, $summary['score'])) }}%"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Cobertura</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($summary['coverage'], 2, '.', ',') }}%</p>
                            <p class="mt-1 text-xs text-slate-500">Indicadores con captura en el periodo.</p>
                            <p class="mt-2 text-xs font-semibold {{ $coverageDeltaTone }}">
                                {{ $summary['coverage_delta'] >= 0 ? '+' : '' }}{{ number_format($summary['coverage_delta'], 2, '.', ',') }} pts vs {{ $comparison['previous_period_label'] }}
                            </p>
                            <div class="mt-4 h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-sky-500" style="width: {{ max(0, min(100, $summary['coverage'])) }}%"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-white/90 p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-500">Cumplen</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['comply_count'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Sin alertas para el periodo seleccionado.</p>
                        </div>
                        <div class="rounded-2xl border border-rose-200 bg-white/90 p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-500">No cumplen</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['fail_count'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Requieren seguimiento operativo.</p>
                        </div>
                        <div class="rounded-2xl border border-amber-200 bg-white/90 p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">Sin registro</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['missing_count'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Pendientes por registrar.</p>
                            <p class="mt-2 text-xs font-semibold {{ $attentionDeltaTone }}">
                                {{ $summary['attention_delta'] >= 0 ? '+' : '' }}{{ $summary['attention_delta'] }} alertas vs {{ $comparison['previous_period_label'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 lg:grid-cols-[1.05fr_0.95fr]">
                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Lectura ejecutiva</p>
                            <h3 class="mt-2 text-lg font-semibold text-slate-900">Balance del periodo</h3>
                        </div>
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $summary['state'] }}</span>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600">Verdes</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['comply_count'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-600">Rojos</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['fail_count'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-600">Pendientes</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['missing_count'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Alertas</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['attention_count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Disciplina operativa</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">Dos variables que mandan</h3>

                    <div class="mt-5 space-y-5">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-sm font-semibold text-slate-700">Score ponderado</p>
                                <span class="text-sm font-semibold text-slate-900">{{ number_format($summary['score'], 2, '.', ',') }}</span>
                            </div>
                            <div class="mt-3 h-3 rounded-full bg-slate-100">
                                <div class="h-3 rounded-full bg-[linear-gradient(90deg,#0f172a_0%,#334155_100%)]" style="width: {{ max(0, min(100, $summary['score'])) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-sm font-semibold text-slate-700">Cobertura de captura</p>
                                <span class="text-sm font-semibold text-slate-900">{{ number_format($summary['coverage'], 2, '.', ',') }}%</span>
                            </div>
                            <div class="mt-3 h-3 rounded-full bg-slate-100">
                                <div class="h-3 rounded-full bg-[linear-gradient(90deg,#38bdf8_0%,#0284c7_100%)]" style="width: {{ max(0, min(100, $summary['coverage'])) }}%"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm leading-6 text-slate-600">
                                {{ $comparison['previous_period_label'] }} cerro con score {{ number_format($comparison['summary']['score'], 2, '.', ',') }},
                                cobertura {{ number_format($comparison['summary']['coverage'], 2, '.', ',') }}%
                                y {{ $comparison['summary']['attention_count'] }} alertas.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Prioridad del mes</p>
                    @if ($firstAttention)
                        <p class="mt-3 text-lg font-semibold text-slate-900">{{ $firstAttention['title'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $firstAttention['issue'] }}</p>
                        <a href="{{ $firstAttention['indicator_url'] }}" class="mt-4 inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                            Ir al indicador
                        </a>
                    @else
                        <p class="mt-3 text-lg font-semibold text-slate-900">Sin prioridad critica</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">La zona no tiene alertas abiertas para este periodo.</p>
                    @endif
                </div>

                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Siguiente paso</p>
                    <p class="mt-3 text-lg font-semibold text-slate-900">
                        @if ($summary['missing_count'] > 0)
                            Completar capturas pendientes
                        @elseif ($summary['fail_count'] > 0)
                            Ejecutar seguimiento a alertas
                        @else
                            Mantener disciplina operativa
                        @endif
                    </p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        @if ($summary['missing_count'] > 0)
                            Hay {{ $summary['missing_count'] }} indicadores sin registro. La accion de mayor impacto es completar esos datos.
                        @elseif ($summary['fail_count'] > 0)
                            Prioriza los indicadores en rojo y verifica si las acciones registradas siguen vigentes.
                        @else
                            Usa esta vista para confirmar estabilidad y revisar actividad reciente antes de cerrar el periodo.
                        @endif
                    </p>
                </div>

                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Ultimo movimiento</p>
                    @if ($latestActivity)
                        <p class="mt-3 text-lg font-semibold text-slate-900">{{ $latestActivity['title'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Resultado {{ $latestActivity['result_label'] }} en {{ $latestActivity['period_label'] }}.</p>
                        <p class="mt-2 text-xs text-slate-500">{{ $latestActivity['updated_at_label'] }} | {{ $latestActivity['updated_by_name'] }}</p>
                    @else
                        <p class="mt-3 text-lg font-semibold text-slate-900">Sin actividad reciente</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Todavia no hay movimientos registrados para esta zona.</p>
                    @endif
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[0.92fr_1.08fr]">
                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Enfoque inmediato</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">Requiere atencion</h3>
                        </div>
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                            {{ $summary['attention_count'] }} items
                        </span>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse ($dashboard['attention'] as $item)
                            @php
                                $attentionTone = match ($item['tone']) {
                                    'amber' => 'border-amber-200 bg-amber-50/70',
                                    'rose' => 'border-rose-200 bg-rose-50/70',
                                    default => 'border-slate-200 bg-slate-50',
                                };
                            @endphp
                            <div class="rounded-2xl border p-4 {{ $attentionTone }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900">{{ $item['title'] }}</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['issue'] }}</p>
                                        <p class="mt-1 text-xs font-medium uppercase tracking-[0.16em] text-slate-400">{{ $item['action'] }}</p>
                                    </div>
                                    <a href="{{ $item['indicator_url'] }}" class="inline-flex shrink-0 items-center rounded-full border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-white">
                                        Abrir
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5 text-sm text-emerald-700">
                                No hay alertas activas para esta zona en el periodo seleccionado.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Distribucion</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">Estado de indicadores</h3>
                        <p class="mt-2 text-sm text-slate-500">Lectura rapida del periodo seleccionado por cumplimiento, alerta y ausencia de captura.</p>
                        <div id="zone-status-chart" class="mt-6 h-[320px]" data-payload='@json($dashboard['status_chart'])'></div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Tendencia</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">Pulso de los ultimos 6 meses</h3>
                        <p class="mt-2 text-sm text-slate-500">Compara el score mensual de la zona con la cobertura de registro para detectar caidas operativas.</p>
                        <div id="zone-trend-chart" class="mt-6 h-[320px]" data-payload='@json($dashboard['trend_chart'])'></div>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Indicadores</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">Mapa operativo del periodo</h3>
                        </div>
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                            {{ count($dashboard['cards']) }} tarjetas
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                        @foreach ($dashboard['cards'] as $card)
                            @php
                                $cardTone = match ($card['status_tone']) {
                                    'emerald' => ['border' => 'border-emerald-300', 'bg' => 'bg-emerald-50/70', 'chip' => 'bg-emerald-100 text-emerald-700', 'bar' => 'bg-emerald-500'],
                                    'rose' => ['border' => 'border-rose-300', 'bg' => 'bg-rose-50/70', 'chip' => 'bg-rose-100 text-rose-700', 'bar' => 'bg-rose-500'],
                                    default => ['border' => 'border-slate-300', 'bg' => 'bg-slate-50/70', 'chip' => 'bg-slate-200 text-slate-700', 'bar' => 'bg-slate-400'],
                                };
                            @endphp
                            <article class="relative overflow-hidden rounded-[24px] border {{ $cardTone['border'] }} {{ $cardTone['bg'] }} p-5 shadow-sm">
                                <div class="absolute inset-x-0 top-0 h-1.5 {{ $cardTone['bar'] }}"></div>
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $card['indicator']->code }}</p>
                                        <h4 class="mt-2 text-lg font-semibold leading-6 text-slate-900">{{ $card['indicator']->name }}</h4>
                                    </div>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $cardTone['chip'] }}">
                                        {{ $card['status_label'] }}
                                    </span>
                                </div>

                                <div class="mt-6">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Resultado</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $card['result_label'] }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $card['detail_label'] }}</p>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-2xl bg-white/85 p-3 ring-1 ring-slate-200/80">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Meta</p>
                                        <p class="mt-1 font-semibold text-slate-800">{{ $card['meta_label'] }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white/85 p-3 ring-1 ring-slate-200/80">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Score</p>
                                        <p class="mt-1 font-semibold text-slate-800">{{ number_format($card['score'], 2, '.', ',') }}</p>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <div class="flex items-center justify-between gap-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                                        <span>Salud operativa</span>
                                        <span>{{ number_format($card['score'], 2, '.', ',') }}</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-white/80 ring-1 ring-slate-200">
                                        <div class="h-2 rounded-full {{ $cardTone['bar'] }}" style="width: {{ max(0, min(100, $card['score'])) }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-5 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Ultima actualizacion</p>
                                        <p class="mt-1 truncate text-sm text-slate-600">{{ $card['updated_at_label'] }}</p>
                                        @if ($card['updated_by_name'])
                                            <p class="text-xs text-slate-500">{{ $card['updated_by_name'] }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ $card['indicator_url'] }}" class="inline-flex shrink-0 items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                        Ver indicador
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

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

                    <div class="mt-6 space-y-4">
                        @forelse ($dashboard['recent_activity'] as $activity)
                            <a href="{{ $activity['indicator_url'] }}" class="group block rounded-2xl border border-slate-200 bg-slate-50/80 p-4 transition hover:border-slate-300 hover:bg-white">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-900">{{ $activity['title'] }}</p>
                                        <p class="mt-2 text-sm text-slate-500">Periodo {{ $activity['period_label'] }}</p>
                                        <p class="mt-1 text-sm text-slate-600">Resultado {{ $activity['result_label'] }}</p>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 ring-1 ring-slate-200">
                                        {{ $activity['updated_by_name'] }}
                                    </span>
                                </div>
                                <p class="mt-3 text-xs text-slate-400">{{ $activity['updated_at_label'] }}</p>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-500">
                                Todavia no hay actividad registrada para esta zona.
                            </div>
                        @endforelse
                    </div>
                </div>
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

                function renderStatusChart() {
                    const el = document.getElementById('zone-status-chart');
                    const payload = parsePayload('zone-status-chart');
                    if (!el || !payload || !window.echarts) return;

                    const chart = echarts.getInstanceByDom(el) || echarts.init(el);
                    chart.setOption({
                        animationDuration: 500,
                        color: ['#0f9f6e', '#e11d48', '#94a3b8'],
                        tooltip: {
                            trigger: 'item',
                            formatter: function (params) {
                                return params.name + ': ' + params.value;
                            }
                        },
                        legend: {
                            bottom: 0,
                            icon: 'circle',
                            textStyle: {
                                color: '#475569'
                            }
                        },
                        series: [{
                            type: 'pie',
                            radius: ['54%', '74%'],
                            center: ['50%', '44%'],
                            avoidLabelOverlap: true,
                            label: {
                                show: false
                            },
                            emphasis: {
                                label: {
                                    show: true,
                                    formatter: '{b}\n{c}',
                                    fontSize: 14,
                                    fontWeight: 600,
                                    color: '#0f172a'
                                }
                            },
                            itemStyle: {
                                borderRadius: 14,
                                borderColor: '#ffffff',
                                borderWidth: 4
                            },
                            data: payload
                        }]
                    }, true);
                }

                function renderTrendChart() {
                    const el = document.getElementById('zone-trend-chart');
                    const payload = parsePayload('zone-trend-chart');
                    if (!el || !payload || !window.echarts) return;

                    const chart = echarts.getInstanceByDom(el) || echarts.init(el);
                    chart.setOption({
                        animationDuration: 500,
                        color: ['#1d4ed8', '#cbd5e1'],
                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {
                            bottom: 0,
                            textStyle: {
                                color: '#475569'
                            }
                        },
                        grid: {
                            left: 20,
                            right: 20,
                            top: 16,
                            bottom: 42,
                            containLabel: true
                        },
                        xAxis: [{
                            type: 'category',
                            data: payload.months,
                            axisLine: {
                                lineStyle: {
                                    color: '#cbd5e1'
                                }
                            },
                            axisLabel: {
                                color: '#64748b'
                            }
                        }],
                        yAxis: [
                            {
                                type: 'value',
                                min: 0,
                                max: 100,
                                axisLabel: {
                                    color: '#64748b',
                                    formatter: '{value}'
                                },
                                splitLine: {
                                    lineStyle: {
                                        color: '#e2e8f0'
                                    }
                                }
                            },
                            {
                                type: 'value',
                                min: 0,
                                max: 100,
                                axisLabel: {
                                    color: '#94a3b8',
                                    formatter: '{value}%'
                                },
                                splitLine: {
                                    show: false
                                }
                            }
                        ],
                        series: [
                            {
                                name: 'Score',
                                type: 'line',
                                smooth: true,
                                symbol: 'circle',
                                symbolSize: 10,
                                lineStyle: {
                                    width: 3
                                },
                                areaStyle: {
                                    color: 'rgba(29,78,216,0.12)'
                                },
                                data: payload.scores
                            },
                            {
                                name: 'Cobertura',
                                type: 'bar',
                                yAxisIndex: 1,
                                barWidth: 16,
                                itemStyle: {
                                    color: '#cbd5e1',
                                    borderRadius: [8, 8, 0, 0]
                                },
                                data: payload.coverage
                            }
                        ]
                    }, true);
                }

                function renderZoneDashboardCharts() {
                    renderStatusChart();
                    renderTrendChart();
                }

                if (window.echarts) {
                    renderZoneDashboardCharts();
                } else {
                    window.addEventListener('load', renderZoneDashboardCharts, { once: true });
                }

                window.addEventListener('resize', function () {
                    ['zone-status-chart', 'zone-trend-chart'].forEach(function (id) {
                        const el = document.getElementById(id);
                        if (!el || !window.echarts) return;
                        const chart = echarts.getInstanceByDom(el);
                        if (chart) chart.resize();
                    });
                });
            })();
        </script>
    @endpush
</x-app-layout>
