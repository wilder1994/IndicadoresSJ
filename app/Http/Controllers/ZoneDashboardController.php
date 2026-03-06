<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Services\Dashboard\ZoneDashboardService;
use App\Services\YearRangeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneDashboardController extends Controller
{
    public function __construct(
        private readonly ZoneDashboardService $zoneDashboardService,
        private readonly YearRangeService $yearRangeService
    ) {
    }

    public function show(Request $request, Zone $zone): View
    {
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $years = $this->yearRangeService->years();
        $selectedYear = $this->yearRangeService->normalize((int) $request->integer('year', (int) now()->year));
        $selectedMonth = (int) $request->integer('month', (int) now()->month);

        if (! array_key_exists($selectedMonth, $months)) {
            $selectedMonth = (int) now()->month;
        }

        $dashboard = $this->zoneDashboardService->build($zone, $selectedYear, $selectedMonth);

        return view('zones.show', [
            'zone' => $zone,
            'dashboard' => $dashboard,
            'headerFilters' => [
                'years' => $years,
                'months' => $months,
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
                'periodStatus' => $dashboard['period']
                    ? ($dashboard['period']->isClosed() ? 'Periodo cerrado' : 'Periodo abierto')
                    : 'Periodo no creado',
            ],
        ]);
    }
}
