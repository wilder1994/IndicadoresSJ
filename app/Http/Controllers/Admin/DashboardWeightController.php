<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardWeightUpdateRequest;
use App\Models\DashboardWeight;
use App\Models\Indicator;
use App\Services\AuditLogService;
use App\Services\DocumentationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardWeightController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly DocumentationService $documentationService
    ) {
    }

    public function edit(): View
    {
        $sample = DashboardWeight::query()->first();
        if ($sample) {
            $this->authorize('view', $sample);
        } else {
            $this->authorize('viewAny', DashboardWeight::class);
        }

        $indicators = Indicator::query()
            ->orderBy('code')
            ->with('dashboardWeight')
            ->get();

        return view('admin.settings.weights', compact('indicators'));
    }

    public function update(DashboardWeightUpdateRequest $request): RedirectResponse
    {
        $weights = $request->validated('weights');
        $reason = $request->validated('reason');

        DB::transaction(function () use ($weights, $reason): void {
            $before = DashboardWeight::query()->with('indicator')->get()->toArray();

            foreach ($weights as $indicatorId => $weight) {
                DashboardWeight::query()->updateOrCreate(
                    ['indicator_id' => (int) $indicatorId],
                    [
                        'weight' => $weight,
                        'updated_by_user_id' => auth()->id(),
                    ]
                );
            }

            $afterCollection = DashboardWeight::query()->with('indicator')->get();
            $after = $afterCollection->toArray();

            $content = $afterCollection
                ->sortBy(fn (DashboardWeight $item) => $item->indicator?->code)
                ->map(function (DashboardWeight $item): string {
                    $code = $item->indicator?->code ?? 'N/A';
                    return $code.': '.$item->weight.'%';
                })
                ->implode("\n");

            $version = $this->documentationService->upsertDashboardWeightsDocument($content, $reason);

            $this->auditLogService->logModelChange(
                eventType: 'dashboard_weights',
                action: 'update',
                model: $version->document,
                before: $before,
                after: $after,
                reason: $reason,
                metadata: ['document_version_id' => $version->id]
            );
        });

        return back()->with('status', 'Pesos actualizados y versionados en Documentacion.');
    }
}
