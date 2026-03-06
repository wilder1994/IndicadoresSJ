<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Services\Dashboard\ZoneDashboardService;
use App\Services\YearRangeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ZoneDashboardService $zoneDashboardService,
        private readonly YearRangeService $yearRangeService
    ) {
    }

    public function __invoke(Request $request): View
    {
        $user = auth()->user();

        $zones = $user->isAdmin()
            ? Zone::query()->where('is_active', true)->orderBy('name')->get()
            : $user->zones()->where('is_active', true)->orderBy('name')->get();

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

        $selectedYear = $this->yearRangeService->normalize((int) $request->integer('year', (int) now()->year));
        $selectedMonth = (int) $request->integer('month', (int) now()->month);

        if (! array_key_exists($selectedMonth, $months)) {
            $selectedMonth = (int) now()->month;
        }

        $dashboard = $this->zoneDashboardService->buildOverview($zones, $selectedYear, $selectedMonth);

        return view('dashboard', [
            'zones' => $zones,
            'dashboard' => $dashboard,
            'headerFilters' => [
                'years' => $this->yearRangeService->years(),
                'months' => $months,
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
            ],
        ]);
    }
}
