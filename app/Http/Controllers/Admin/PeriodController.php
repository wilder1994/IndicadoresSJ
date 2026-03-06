<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeriodCloseRequest;
use App\Http\Requests\PeriodReopenRequest;
use App\Models\IndicatorCapture;
use App\Models\Period;
use App\Services\AuditLogService;
use App\Services\YearRangeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeriodController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly YearRangeService $yearRangeService
    )
    {
        $this->authorizeResource(Period::class, 'period');
    }

    public function index(): View
    {
        $periods = Period::query()->orderByDesc('year')->orderByDesc('month')->paginate(24);
        $years = $this->yearRangeService->years();

        return view('admin.periods.index', compact('periods', 'years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Period::class);

        $validated = $request->validate([
            'year' => $this->yearRangeService->validationRules(),
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'status' => ['required', Rule::in([Period::STATUS_OPEN, Period::STATUS_CLOSED])],
        ]);

        $period = Period::query()->firstOrCreate(
            ['year' => $validated['year'], 'month' => $validated['month']],
            ['status' => $validated['status']]
        );

        if (! $period->wasRecentlyCreated) {
            return back()->with('status', 'El periodo ya existe.');
        }

        $this->auditLogService->logModelChange(
            eventType: 'period',
            action: 'create',
            model: $period,
            before: null,
            after: $period->toArray(),
            reason: 'Creacion de periodo'
        );

        return back()->with('status', 'Periodo creado correctamente.');
    }

    public function close(PeriodCloseRequest $request, Period $period): RedirectResponse
    {
        $pending = IndicatorCapture::query()
            ->with(['indicator', 'zone'])
            ->where('period_id', $period->id)
            ->where('complies', false)
            ->whereDoesntHave('improvement')
            ->get();

        if ($pending->isNotEmpty()) {
            $items = $pending->map(fn (IndicatorCapture $capture) => [
                'indicator' => $capture->indicator?->code,
                'zone' => $capture->zone?->code,
                'result' => $capture->result_percentage,
            ])->all();

            return back()
                ->withErrors(['close' => 'No se puede cerrar: existen indicadores en rojo sin mejora diligenciada.'])
                ->with('pending_improvements', $items);
        }

        $before = $period->toArray();
        $reason = $request->validated('reason');

        $period->update([
            'status' => Period::STATUS_CLOSED,
            'closed_at' => now(),
            'closed_by_user_id' => auth()->id(),
        ]);

        $this->auditLogService->logModelChange(
            eventType: 'period',
            action: 'close',
            model: $period,
            before: $before,
            after: $period->fresh()->toArray(),
            reason: $reason
        );

        return back()->with('status', 'Periodo cerrado. La edicion de capturas queda bloqueada.');
    }

    public function reopen(PeriodReopenRequest $request, Period $period): RedirectResponse
    {
        $before = $period->toArray();
        $reason = $request->validated('reason');

        $period->update([
            'status' => Period::STATUS_OPEN,
            'reopened_at' => now(),
            'reopened_by_user_id' => auth()->id(),
            'reopen_reason' => $reason,
        ]);

        $this->auditLogService->logModelChange(
            eventType: 'period',
            action: 'reopen',
            model: $period,
            before: $before,
            after: $period->fresh()->toArray(),
            reason: $reason
        );

        return back()->with('status', 'Periodo reabierto correctamente.');
    }
}
