<?php

namespace App\Http\Controllers;

use App\Exports\IndicatorReportExport;
use App\Models\Indicator;
use App\Models\IndicatorCapture;
use App\Models\Period;
use App\Models\Zone;
use App\Services\AuditLogService;
use App\Services\IndicatorMotherService;
use App\Services\YearRangeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function __construct(
        private readonly IndicatorMotherService $motherService,
        private readonly AuditLogService $auditLogService,
        private readonly YearRangeService $yearRangeService
    ) {
    }

    public function zoneExcel(Request $request, Indicator $indicator): Response
    {
        $report = $this->buildZoneReport($request, $indicator);

        $this->auditLogService->logEvent(
            eventType: 'export',
            action: 'zone_excel',
            reason: 'Exporte Excel por zona',
            metadata: ['indicator' => $indicator->code, 'zone_id' => $report['zone']->id, 'year' => $report['year'], 'month' => $report['month']]
        );

        return Excel::download(
            new IndicatorReportExport('exports.zone', $report),
            'zona-'.$indicator->code.'-'.$report['year'].'-'.$report['month'].'.xlsx'
        );
    }

    public function zonePdf(Request $request, Indicator $indicator)
    {
        $report = $this->buildZoneReport($request, $indicator);

        $this->auditLogService->logEvent(
            eventType: 'export',
            action: 'zone_pdf',
            reason: 'Exporte PDF por zona',
            metadata: ['indicator' => $indicator->code, 'zone_id' => $report['zone']->id, 'year' => $report['year'], 'month' => $report['month']]
        );

        $pdf = Pdf::loadView('exports.zone-pdf', $report)->setPaper('a4', 'portrait');
        return $pdf->download('zona-'.$indicator->code.'-'.$report['year'].'-'.$report['month'].'.pdf');
    }

    public function motherExcel(Request $request, Indicator $indicator): Response
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $report = $this->buildMotherReport($request, $indicator);

        $this->auditLogService->logEvent(
            eventType: 'export',
            action: 'mother_excel',
            reason: 'Exporte Excel consolidado MADRE',
            metadata: ['indicator' => $indicator->code, 'year' => $report['year'], 'month' => $report['month']]
        );

        return Excel::download(
            new IndicatorReportExport('exports.mother', $report),
            'madre-'.$indicator->code.'-'.$report['year'].'-'.$report['month'].'.xlsx'
        );
    }

    public function motherPdf(Request $request, Indicator $indicator)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $report = $this->buildMotherReport($request, $indicator);

        $this->auditLogService->logEvent(
            eventType: 'export',
            action: 'mother_pdf',
            reason: 'Exporte PDF consolidado MADRE',
            metadata: ['indicator' => $indicator->code, 'year' => $report['year'], 'month' => $report['month']]
        );

        $pdf = Pdf::loadView('exports.mother-pdf', $report)->setPaper('a4', 'landscape');
        return $pdf->download('madre-'.$indicator->code.'-'.$report['year'].'-'.$report['month'].'.pdf');
    }

    private function buildZoneReport(Request $request, Indicator $indicator): array
    {
        $year = $this->yearRangeService->normalize((int) $request->integer('year', now()->year));
        $month = (int) $request->integer('month', now()->month);
        $zoneId = (int) $request->integer('zone_id');
        $zone = Zone::query()->findOrFail($zoneId);

        abort_unless(auth()->user()->hasZoneAccess($zone->id), 403);

        $period = Period::query()->where(['year' => $year, 'month' => $month])->first();
        $capture = null;
        if ($period) {
            $capture = IndicatorCapture::query()
                ->with('improvement')
                ->where('indicator_id', $indicator->id)
                ->where('zone_id', $zone->id)
                ->where('period_id', $period->id)
                ->first();
        }

        return [
            'indicator' => $indicator,
            'zone' => $zone,
            'year' => $year,
            'month' => $month,
            'capture' => $capture,
            'display' => $capture?->input_data ?? [],
        ];
    }

    private function buildMotherReport(Request $request, Indicator $indicator): array
    {
        $year = $this->yearRangeService->normalize((int) $request->integer('year', now()->year));
        $month = (int) $request->integer('month', now()->month);
        $monthly = $this->motherService->getMonthlyData($indicator, $year, $month);

        return [
            'indicator' => $indicator,
            'year' => $year,
            'month' => $month,
            'monthly' => $monthly,
        ];
    }
}
