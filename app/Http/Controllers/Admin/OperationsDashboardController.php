<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardSummary;
use App\Models\Zone;
use App\Services\AnalysisSuggestionService;
use App\Services\AuditLogService;
use App\Services\Dashboard\OperationsDashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationsDashboardController extends Controller
{
    public function __construct(
        private readonly OperationsDashboardService $dashboardService,
        private readonly AnalysisSuggestionService $analysisSuggestionService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', DashboardSummary::class);

        $year = (int) $request->integer('year', now()->year);
        $month = (int) $request->integer('month', now()->month);
        $dashboard = $this->dashboardService->build($year, $month);
        $summary = DashboardSummary::query()->where(['year' => $year, 'month' => $month])->first();

        $this->auditLogService->logEvent(
            eventType: 'admin_action',
            action: 'dashboard_view',
            reason: 'Consulta dashboard general de operaciones',
            metadata: ['year' => $year, 'month' => $month]
        );

        return view('admin.dashboard.index', compact('year', 'month', 'dashboard', 'summary'));
    }

    public function generateSummary(Request $request): RedirectResponse
    {
        $this->authorize('create', DashboardSummary::class);

        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $year = (int) $validated['year'];
        $month = (int) $validated['month'];
        $dashboard = $this->dashboardService->build($year, $month);

        $context = [
            'score' => $dashboard['global_score'],
            'state' => $dashboard['global_state'],
            'top_zone' => $dashboard['zone_ranking'][0]['zone']->name ?? 'N/A',
            'critical_indicator' => $dashboard['critical_ranking'][0]['indicator']->code ?? 'N/A',
            'year' => $year,
            'month' => $month,
        ];
        $generatedText = $this->analysisSuggestionService->generateDashboardSummary($context);

        $summary = DashboardSummary::query()->firstOrNew(['year' => $year, 'month' => $month]);
        $before = $summary->exists ? $summary->toArray() : null;

        if (! $summary->exists) {
            $summary->generated_by_user_id = auth()->id();
            $summary->summary_text = $generatedText;
        }

        $summary->generated_text = $generatedText;
        $summary->updated_by_user_id = auth()->id();
        $summary->save();

        $this->auditLogService->logModelChange(
            eventType: 'dashboard_summary',
            action: 'generate',
            model: $summary,
            before: $before,
            after: $summary->fresh()->toArray(),
            reason: 'Generacion de resumen ejecutivo sugerido',
            metadata: ['year' => $year, 'month' => $month]
        );

        return redirect()
            ->route('admin.dashboard.index', ['year' => $year, 'month' => $month])
            ->with('status', 'Sugerencia de resumen generada correctamente.');
    }

    public function saveSummary(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'summary_text' => ['required', 'string'],
        ]);

        $year = (int) $validated['year'];
        $month = (int) $validated['month'];

        $summary = DashboardSummary::query()->firstOrNew(['year' => $year, 'month' => $month]);
        if ($summary->exists) {
            $this->authorize('update', $summary);
        } else {
            $this->authorize('create', DashboardSummary::class);
            $summary->generated_by_user_id = auth()->id();
        }

        $before = $summary->exists ? $summary->toArray() : null;

        $summary->summary_text = $validated['summary_text'];
        $summary->updated_by_user_id = auth()->id();
        $summary->save();

        $this->auditLogService->logModelChange(
            eventType: 'dashboard_summary',
            action: 'save',
            model: $summary,
            before: $before,
            after: $summary->fresh()->toArray(),
            reason: 'Actualizacion de resumen ejecutivo',
            metadata: ['year' => $year, 'month' => $month]
        );

        return redirect()
            ->route('admin.dashboard.index', ['year' => $year, 'month' => $month])
            ->with('status', 'Resumen ejecutivo guardado.');
    }

    public function zoneSummary(Request $request, Zone $zone): View
    {
        $this->authorize('viewAny', DashboardSummary::class);

        $year = (int) $request->integer('year', now()->year);
        $month = (int) $request->integer('month', now()->month);
        $rows = $this->dashboardService->zoneSummary($year, $month, $zone);

        $this->auditLogService->logEvent(
            eventType: 'admin_action',
            action: 'dashboard_zone_summary_view',
            reason: 'Consulta resumen por zona',
            metadata: ['zone_id' => $zone->id, 'year' => $year, 'month' => $month]
        );

        return view('admin.dashboard.zone-summary', compact('zone', 'year', 'month', 'rows'));
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('viewAny', DashboardSummary::class);

        $year = (int) $request->integer('year', now()->year);
        $month = (int) $request->integer('month', now()->month);
        $dashboard = $this->dashboardService->build($year, $month);
        $summary = DashboardSummary::query()->where(['year' => $year, 'month' => $month])->first();

        $this->auditLogService->logEvent(
            eventType: 'export',
            action: 'dashboard_pdf',
            reason: 'Exporte PDF dashboard ejecutivo',
            metadata: ['year' => $year, 'month' => $month]
        );

        $pdf = Pdf::loadView('admin.dashboard.pdf', compact('year', 'month', 'dashboard', 'summary'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('dashboard-ejecutivo-'.$year.'-'.$month.'.pdf');
    }
}
