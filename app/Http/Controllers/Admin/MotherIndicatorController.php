<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Indicator;
use App\Services\AuditLogService;
use App\Services\IndicatorMotherService;
use App\Services\YearRangeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MotherIndicatorController extends Controller
{
    public function __construct(
        private readonly IndicatorMotherService $motherService,
        private readonly AuditLogService $auditLogService,
        private readonly YearRangeService $yearRangeService
    )
    {
    }

    public function index(): View
    {
        $indicators = Indicator::query()->where('is_active', true)->orderBy('code')->get();
        return view('admin.mother.index', compact('indicators'));
    }

    public function show(Request $request, Indicator $indicator): View
    {
        $year = $this->yearRangeService->normalize((int) $request->integer('year', now()->year));
        $month = (int) $request->integer('month', now()->month);
        $years = $this->yearRangeService->years();

        $monthly = $this->motherService->getMonthlyData($indicator, $year, $month);
        $quarterly = $indicator->code === 'FT-OP-08'
            ? $this->motherService->getQuarterlyDataFtOp08($year)
            : null;

        $this->auditLogService->logEvent(
            eventType: 'admin_action',
            action: 'mother_view',
            reason: 'Consulta consolidado MADRE',
            metadata: ['indicator' => $indicator->code, 'year' => $year, 'month' => $month]
        );

        return view('admin.mother.show', compact('indicator', 'year', 'month', 'years', 'monthly', 'quarterly'));
    }
}
