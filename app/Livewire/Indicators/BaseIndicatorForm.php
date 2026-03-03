<?php

namespace App\Livewire\Indicators;

use App\Models\Improvement;
use App\Models\Indicator;
use App\Models\IndicatorCapture;
use App\Models\Period;
use App\Models\Zone;
use App\Services\AuditLogService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

abstract class BaseIndicatorForm extends Component
{
    public Indicator $indicator;
    public int $selectedYear;
    public int $selectedMonth;
    public ?int $selectedZoneId = null;

    public array $zones = [];
    public array $months = [];
    public array $years = [];
    public array $form = [];
    public array $trendRows = [];
    public array $metricErrors = [];

    public ?int $periodId = null;
    public ?int $captureId = null;
    public ?int $improvementId = null;

    public float $resultPercentage = 0.0;
    public float $numerator = 0.0;
    public float $denominator = 0.0;
    public bool $complies = false;
    public string $semaforo = 'ROJO';
    public string $analysisText = '';
    public bool $isPeriodClosed = false;

    public bool $showImprovementModal = false;
    public string $improvementAnalysis = '';
    public string $improvementActionTaken = '';
    public string $improvementActionDefined = '';

    protected AuditLogService $auditLogService;

    protected string $fieldsView = '';

    abstract protected function defaultForm(): array;
    abstract protected function fieldRules(): array;
    abstract protected function calculateMetrics(array $form): array;

    public function boot(AuditLogService $auditLogService): void
    {
        $this->auditLogService = $auditLogService;
    }

    public function mount(Indicator $indicator): void
    {
        $this->indicator = $indicator;
        $this->form = $this->defaultForm();
        $now = now();
        $this->selectedYear = (int) $now->year;
        $this->selectedMonth = (int) $now->month;
        $this->years = range((int) $now->year - 2, (int) $now->year + 1);
        $this->months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $this->loadZones();
        $this->loadContext();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadContext();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadContext();
    }

    public function updatedSelectedZoneId(): void
    {
        $this->loadContext();
    }

    public function updatedForm(): void
    {
        $this->computeCurrentMetrics();
    }

    public function save(): void
    {
        $this->ensureContext();
        $this->validate($this->fieldRules());
        $this->computeCurrentMetrics();

        if ($this->isPeriodClosed) {
            throw ValidationException::withMessages(['period' => 'Periodo cerrado']);
        }

        if (! empty($this->metricErrors)) {
            throw ValidationException::withMessages(['form' => implode(' | ', $this->metricErrors)]);
        }

        $period = Period::query()->findOrFail($this->periodId);

        $existing = IndicatorCapture::query()->where([
            'indicator_id' => $this->indicator->id,
            'zone_id' => $this->selectedZoneId,
            'period_id' => $period->id,
        ])->first();

        $payload = [
            'indicator_id' => $this->indicator->id,
            'zone_id' => $this->selectedZoneId,
            'period_id' => $period->id,
            'input_data' => $this->form,
            'numerator' => $this->numerator,
            'denominator' => $this->denominator,
            'result_percentage' => $this->resultPercentage,
            'complies' => $this->complies,
            'analysis_text' => $this->analysisText,
            'updated_by_user_id' => auth()->id(),
        ];

        if ($existing) {
            $before = $existing->toArray();
            $existing->update($payload);
            $after = $existing->fresh()->toArray();

            $this->auditLogService->logModelChange(
                eventType: 'indicator_capture',
                action: 'update',
                model: $existing,
                before: $before,
                after: $after,
                reason: 'Actualizacion captura mensual'
            );

            $this->captureId = $existing->id;
        } else {
            $payload['created_by_user_id'] = auth()->id();
            $capture = IndicatorCapture::query()->create($payload);

            $this->auditLogService->logModelChange(
                eventType: 'indicator_capture',
                action: 'create',
                model: $capture,
                before: null,
                after: $capture->toArray(),
                reason: 'Creacion captura mensual'
            );

            $this->captureId = $capture->id;
        }

        session()->flash('status', 'Captura guardada correctamente para el mes seleccionado.');
        $this->loadContext();
    }

    public function openImprovementModal(): void
    {
        $this->ensureContext();

        if (! $this->captureId) {
            $this->addError('improvement', 'Primero guarda la captura del mes.');
            return;
        }

        $improvement = Improvement::query()->where('indicator_capture_id', $this->captureId)->first();

        $this->improvementId = $improvement?->id;
        $this->improvementAnalysis = $improvement?->analysis ?? '';
        $this->improvementActionTaken = $improvement?->action_taken ?? '';
        $this->improvementActionDefined = $improvement?->action_defined ?? '';
        $this->showImprovementModal = true;
    }

    public function closeImprovementModal(): void
    {
        $this->showImprovementModal = false;
    }

    public function saveImprovement(): void
    {
        $this->ensureContext();

        if ($this->isPeriodClosed) {
            throw ValidationException::withMessages(['period' => 'Periodo cerrado']);
        }

        if (! $this->captureId) {
            throw ValidationException::withMessages(['improvement' => 'Debes guardar la captura antes de registrar mejora.']);
        }

        $this->validate([
            'improvementAnalysis' => ['required', 'string'],
            'improvementActionTaken' => ['required', 'string'],
            'improvementActionDefined' => ['required', 'string'],
        ]);

        $capture = IndicatorCapture::query()->findOrFail($this->captureId);
        $beforeCapture = $capture->toArray();
        $block = $this->buildImprovementBlock();
        $analysisWithBlock = $this->replaceOrAppendBlock($capture->analysis_text ?? '', $block);

        $existing = Improvement::query()->where('indicator_capture_id', $capture->id)->first();
        $payload = [
            'indicator_capture_id' => $capture->id,
            'indicator_id' => $this->indicator->id,
            'zone_id' => $this->selectedZoneId,
            'period_id' => $this->periodId,
            'analysis' => $this->improvementAnalysis,
            'action_taken' => $this->improvementActionTaken,
            'action_defined' => $this->improvementActionDefined,
            'integrated_analysis_block' => $block,
            'created_by_user_id' => auth()->id(),
        ];

        if ($existing) {
            $beforeImprovement = $existing->toArray();
            $existing->update($payload);
            $this->auditLogService->logModelChange(
                eventType: 'improvement',
                action: 'update',
                model: $existing,
                before: $beforeImprovement,
                after: $existing->fresh()->toArray(),
                reason: 'Actualizacion mejora mensual'
            );
            $this->improvementId = $existing->id;
        } else {
            $improvement = Improvement::query()->create($payload);
            $this->auditLogService->logModelChange(
                eventType: 'improvement',
                action: 'create',
                model: $improvement,
                before: null,
                after: $improvement->toArray(),
                reason: 'Creacion mejora mensual'
            );
            $this->improvementId = $improvement->id;
        }

        $capture->update([
            'analysis_text' => $analysisWithBlock,
            'updated_by_user_id' => auth()->id(),
        ]);

        $this->auditLogService->logModelChange(
            eventType: 'indicator_capture',
            action: 'update',
            model: $capture,
            before: $beforeCapture,
            after: $capture->fresh()->toArray(),
            reason: 'Integracion de mejora al analisis mensual'
        );

        $this->analysisText = $analysisWithBlock;
        $this->showImprovementModal = false;
        session()->flash('status', 'Mejora guardada correctamente.');
    }

    protected function ensureContext(): void
    {
        if (! $this->selectedZoneId) {
            throw ValidationException::withMessages(['zone' => 'Debes seleccionar una zona.']);
        }
    }

    protected function loadZones(): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $this->zones = Zone::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['zones.id', 'zones.name', 'zones.code'])
                ->toArray();
        } else {
            $this->zones = $user->zones()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['zones.id', 'zones.name', 'zones.code'])
                ->toArray();
        }

        if (! $this->selectedZoneId && ! empty($this->zones)) {
            $this->selectedZoneId = (int) $this->zones[0]['id'];
        }
    }

    protected function loadContext(): void
    {
        if (! $this->selectedZoneId) {
            return;
        }

        $this->assertZoneAccess();
        $period = Period::query()->firstOrCreate(
            ['year' => $this->selectedYear, 'month' => $this->selectedMonth],
            ['status' => Period::STATUS_OPEN]
        );

        $this->periodId = $period->id;
        $this->isPeriodClosed = $period->isClosed();

        $capture = IndicatorCapture::query()->where([
            'indicator_id' => $this->indicator->id,
            'zone_id' => $this->selectedZoneId,
            'period_id' => $period->id,
        ])->first();

        if ($capture) {
            $this->captureId = $capture->id;
            $this->form = array_merge($this->defaultForm(), $capture->input_data ?? []);
            $this->analysisText = $capture->analysis_text ?? '';
            $improvement = Improvement::query()->where('indicator_capture_id', $capture->id)->first();
            $this->improvementId = $improvement?->id;
        } else {
            $this->captureId = null;
            $this->improvementId = null;
            $this->form = $this->defaultForm();
            $this->analysisText = '';
        }

        $this->loadTrend();
        $this->computeCurrentMetrics();
    }

    protected function computeCurrentMetrics(): void
    {
        $calc = $this->calculateMetrics($this->form);

        $this->numerator = (float) ($calc['numerator'] ?? 0);
        $this->denominator = (float) ($calc['denominator'] ?? 0);
        $this->resultPercentage = (float) ($calc['result_percentage'] ?? 0);
        $this->complies = (bool) ($calc['complies'] ?? false);
        $this->metricErrors = $calc['errors'] ?? [];
        $this->semaforo = $this->complies ? 'VERDE' : 'ROJO';
    }

    protected function loadTrend(): void
    {
        $currentDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $months = collect([2, 1, 0])->map(function (int $offset) use ($currentDate): array {
            $d = $currentDate->copy()->subMonths($offset);
            return ['year' => (int) $d->year, 'month' => (int) $d->month];
        });

        $periods = Period::query()
            ->where(function ($query) use ($months): void {
                foreach ($months as $m) {
                    $query->orWhere(fn ($q) => $q->where('year', $m['year'])->where('month', $m['month']));
                }
            })
            ->get()
            ->keyBy(fn (Period $p) => $p->year.'-'.$p->month);

        $this->trendRows = $months->map(function (array $m) use ($periods): array {
            $period = $periods->get($m['year'].'-'.$m['month']);
            if (! $period || ! $this->selectedZoneId) {
                return [
                    'year' => $m['year'],
                    'month' => $m['month'],
                    'result' => null,
                    'semaforo' => '-',
                ];
            }

            $capture = IndicatorCapture::query()->where([
                'indicator_id' => $this->indicator->id,
                'zone_id' => $this->selectedZoneId,
                'period_id' => $period->id,
            ])->first();

            return [
                'year' => $m['year'],
                'month' => $m['month'],
                'result' => $capture?->result_percentage,
                'semaforo' => $capture ? ($capture->complies ? 'VERDE' : 'ROJO') : '-',
            ];
        })->toArray();
    }

    protected function trendLabel(): string
    {
        $vals = collect($this->trendRows)
            ->pluck('result')
            ->filter(fn ($v) => $v !== null)
            ->values();

        if ($vals->count() < 2) {
            return 'sin datos suficientes';
        }

        return $vals->last() >= $vals->first() ? 'mejorando' : 'en descenso';
    }

    protected function historyLabel(): string
    {
        $vals = collect($this->trendRows)
            ->pluck('result')
            ->filter(fn ($v) => $v !== null);

        if ($vals->isEmpty()) {
            return 'sin datos';
        }

        return 'promedio '.round($vals->avg(), 2).' %';
    }

    protected function previousMonthResult(): ?float
    {
        if (count($this->trendRows) < 2) {
            return null;
        }

        $previous = $this->trendRows[count($this->trendRows) - 2]['result'] ?? null;
        return $previous !== null ? (float) $previous : null;
    }

    protected function buildImprovementBlock(): string
    {
        return "Mejora global:\n".
            'Analisis: '.$this->improvementAnalysis."\n".
            'Accion tomada: '.$this->improvementActionTaken."\n".
            'Accion definida: '.$this->improvementActionDefined;
    }

    protected function replaceOrAppendBlock(string $analysis, string $block): string
    {
        $clean = $this->stripImprovementBlock($analysis);
        return $clean !== '' ? $clean."\n\n".$block : $block;
    }

    protected function stripImprovementBlock(string $analysis): string
    {
        $clean = (string) preg_replace('/\s*### MEJORA_GLOBAL_START.*?### MEJORA_GLOBAL_END\s*/s', "\n", $analysis);
        $clean = (string) preg_replace('/\s*Mejora global:\nAnalisis:.*?\nAccion tomada:.*?\nAccion definida:.*?(?=\n{2,}|\z)/s', "\n", $clean);

        return trim($clean);
    }

    protected function assertZoneAccess(): void
    {
        $user = auth()->user();

        if (! $user->hasZoneAccess((int) $this->selectedZoneId)) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.indicators.base-form', [
            'fieldsView' => $this->fieldsView,
        ]);
    }
}

