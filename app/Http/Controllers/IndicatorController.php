<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\Period;
use App\Models\Zone;
use App\Services\YearRangeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndicatorController extends Controller
{
    public function __construct(private readonly YearRangeService $yearRangeService)
    {
    }

    public function index(): View
    {
        $indicators = Indicator::query()->where('is_active', true)->orderBy('code')->get();

        return view('indicators.index', compact('indicators'));
    }

    public function show(Request $request, Indicator $indicator): View
    {
        abort_unless($indicator->is_active, 404);

        $now = now();
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        $years = $this->yearRangeService->years();

        $user = auth()->user();
        $zones = $user->isAdmin()
            ? Zone::query()->where('is_active', true)->orderBy('name')->get(['zones.id', 'zones.name', 'zones.code'])
            : $user->zones()->where('is_active', true)->orderBy('name')->get(['zones.id', 'zones.name', 'zones.code']);

        $selectedYear = $this->yearRangeService->normalize((int) $request->integer('year', (int) $now->year));

        $selectedMonth = (int) $request->integer('month', (int) $now->month);
        if (! array_key_exists($selectedMonth, $months)) {
            $selectedMonth = (int) $now->month;
        }

        $zoneIds = $zones->pluck('id')->map(fn ($id) => (int) $id)->all();
        $selectedZoneId = (int) $request->integer('zone_id', (int) ($zoneIds[0] ?? 0));
        if (! in_array($selectedZoneId, $zoneIds, true)) {
            $selectedZoneId = (int) ($zoneIds[0] ?? 0);
        }

        $period = Period::query()
            ->where('year', $selectedYear)
            ->where('month', $selectedMonth)
            ->first();

        $headerFilters = [
            'years' => $years,
            'months' => $months,
            'zones' => $zones,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'selectedZoneId' => $selectedZoneId,
            'isPeriodClosed' => $period?->isClosed() ?? false,
        ];

        return view('indicators.show', compact('indicator', 'headerFilters'));
    }
}
