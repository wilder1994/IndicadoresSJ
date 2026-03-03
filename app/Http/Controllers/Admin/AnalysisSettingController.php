<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalysisSettingUpdateRequest;
use App\Models\AnalysisSetting;
use App\Services\AuditLogService;
use App\Services\DocumentationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalysisSettingController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly DocumentationService $documentationService
    ) {
    }

    public function edit(): View
    {
        $setting = AnalysisSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'mode' => AnalysisSetting::MODE_RULES,
                'rules_enabled' => true,
                'local_timeout_ms' => 10000,
                'openai_model' => 'gpt-4o-mini',
                'openai_timeout_ms' => 10000,
            ]
        );

        $this->authorize('view', $setting);

        return view('admin.settings.analysis', compact('setting'));
    }

    public function update(AnalysisSettingUpdateRequest $request): RedirectResponse
    {
        $setting = AnalysisSetting::query()->firstOrFail();
        $this->authorize('update', $setting);

        $before = $setting->toArray();
        $validated = $request->validated();
        $reason = $validated['reason'];
        unset($validated['reason']);
        $validated['updated_by_user_id'] = auth()->id();

        DB::transaction(function () use ($setting, $validated, $before, $reason): void {
            $setting->update($validated);
            $version = $this->documentationService->upsertAnalysisMethodologyDocument($setting->fresh(), $reason);

            $this->auditLogService->logModelChange(
                eventType: 'analysis_settings',
                action: 'update',
                model: $setting,
                before: $before,
                after: $setting->fresh()->toArray(),
                reason: $reason,
                metadata: ['document_version_id' => $version->id]
            );
        });

        return back()->with('status', 'Configuracion de analisis actualizada.');
    }
}
