<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalysisSettingUpdateRequest;
use App\Models\AnalysisSetting;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnalysisSettingController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
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

        $setting->update($validated);

        $this->auditLogService->logModelChange(
            eventType: 'analysis_settings',
            action: 'update',
            model: $setting,
            before: $before,
            after: $setting->fresh()->toArray(),
            reason: $reason
        );

        return back()->with('status', 'Configuracion de analisis actualizada.');
    }
}
