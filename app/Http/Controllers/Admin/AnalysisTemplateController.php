<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalysisTemplateStoreRequest;
use App\Http\Requests\AnalysisTemplateUpdateRequest;
use App\Models\AnalysisTemplate;
use App\Models\Indicator;
use App\Services\AuditLogService;
use App\Services\DocumentationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalysisTemplateController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly DocumentationService $documentationService
    ) {
        $this->authorizeResource(AnalysisTemplate::class, 'analysis_template');
    }

    public function index(): View
    {
        $templates = AnalysisTemplate::query()
            ->with('indicator')
            ->orderBy(
                Indicator::query()->select('code')
                    ->whereColumn('indicators.id', 'analysis_templates.indicator_id')
            )
            ->paginate(20);

        return view('admin.analysis-templates.index', compact('templates'));
    }

    public function create(): View
    {
        $usedIndicatorIds = AnalysisTemplate::query()->pluck('indicator_id');
        $indicators = Indicator::query()
            ->whereNotIn('id', $usedIndicatorIds)
            ->orderBy('code')
            ->get();

        return view('admin.analysis-templates.create', compact('indicators'));
    }

    public function store(AnalysisTemplateStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $reason = $validated['reason'];
        unset($validated['reason']);

        DB::transaction(function () use ($validated, $reason): void {
            $validated['updated_by_user_id'] = auth()->id();
            $template = AnalysisTemplate::query()->create($validated);

            $this->documentationService->upsertAnalysisTemplatesDocument($reason);

            $this->auditLogService->logModelChange(
                eventType: 'analysis_template',
                action: 'create',
                model: $template,
                before: null,
                after: $template->fresh()->toArray(),
                reason: $reason
            );
        });

        return redirect()->route('admin.analysis-templates.index')->with('status', 'Plantilla creada correctamente.');
    }

    public function show(AnalysisTemplate $analysisTemplate): RedirectResponse
    {
        return redirect()->route('admin.analysis-templates.edit', $analysisTemplate);
    }

    public function edit(AnalysisTemplate $analysisTemplate): View
    {
        $indicators = Indicator::query()->orderBy('code')->get();

        return view('admin.analysis-templates.edit', compact('analysisTemplate', 'indicators'));
    }

    public function update(AnalysisTemplateUpdateRequest $request, AnalysisTemplate $analysisTemplate): RedirectResponse
    {
        $validated = $request->validated();
        $reason = $validated['reason'];
        unset($validated['reason']);

        DB::transaction(function () use ($analysisTemplate, $validated, $reason): void {
            $before = $analysisTemplate->toArray();
            $analysisTemplate->update($validated + ['updated_by_user_id' => auth()->id()]);

            $this->documentationService->upsertAnalysisTemplatesDocument($reason);

            $this->auditLogService->logModelChange(
                eventType: 'analysis_template',
                action: 'update',
                model: $analysisTemplate,
                before: $before,
                after: $analysisTemplate->fresh()->toArray(),
                reason: $reason
            );
        });

        return redirect()->route('admin.analysis-templates.index')->with('status', 'Plantilla actualizada correctamente.');
    }

    public function destroy(AnalysisTemplate $analysisTemplate): RedirectResponse
    {
        request()->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $reason = (string) request('reason');

        DB::transaction(function () use ($analysisTemplate, $reason): void {
            $before = $analysisTemplate->toArray();
            $analysisTemplate->delete();

            $this->documentationService->upsertAnalysisTemplatesDocument($reason);

            $this->auditLogService->logModelChange(
                eventType: 'analysis_template',
                action: 'delete',
                model: $analysisTemplate,
                before: $before,
                after: null,
                reason: $reason
            );
        });

        return redirect()->route('admin.analysis-templates.index')->with('status', 'Plantilla eliminada.');
    }
}
