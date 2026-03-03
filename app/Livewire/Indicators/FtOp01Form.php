<?php

namespace App\Livewire\Indicators;

use App\Models\Improvement;
use App\Models\IndicatorCapture;
use App\Models\Period;

class FtOp01Form extends BaseIndicatorForm
{
    protected string $fieldsView = 'livewire.indicators.partials.ft-op-01';
    public array $sheetRows = [];
    public array $chartPayload = [];

    protected function defaultForm(): array
    {
        return ['total_personal' => null, 'personal_capacitado' => null];
    }

    protected function fieldRules(): array
    {
        return [
            'form.total_personal' => ['required', 'numeric', 'min:0.01'],
            'form.personal_capacitado' => ['required', 'numeric', 'min:0'],
            'analysisText' => ['required', 'string'],
        ];
    }

    protected function calculateMetrics(array $form): array
    {
        $den = (float) ($form['total_personal'] ?? 0);
        $num = (float) ($form['personal_capacitado'] ?? 0);
        $errors = [];
        if ($den <= 0) {
            $errors[] = 'total_personal no puede ser 0.';
        }

        $result = $den > 0 ? round(($num / $den) * 100, 2) : 0;
        $complies = $den > 0 && $result >= (float) $this->indicator->target_value;

        return compact('num', 'den', 'result', 'complies', 'errors') + [
            'numerator' => $num,
            'denominator' => $den,
            'result_percentage' => $result,
        ];
    }

    protected function loadContext(): void
    {
        parent::loadContext();
        $this->buildSheetData();
    }

    public function save(): void
    {
        parent::save();
        $this->buildSheetData();
    }

    public function saveImprovement(): void
    {
        parent::saveImprovement();
        $this->buildSheetData();
    }

    private function buildSheetData(): void
    {
        $monthNames = [
            1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC',
        ];

        if (! $this->selectedZoneId) {
            $this->sheetRows = [];
            $this->chartPayload = [];
            return;
        }

        $periods = Period::query()
            ->where('year', $this->selectedYear)
            ->whereBetween('month', [1, 12])
            ->get(['id', 'month'])
            ->keyBy('month');

        $captures = IndicatorCapture::query()
            ->where('indicator_id', $this->indicator->id)
            ->where('zone_id', $this->selectedZoneId)
            ->whereIn('period_id', $periods->pluck('id'))
            ->get(['id', 'period_id', 'input_data', 'result_percentage', 'complies', 'analysis_text'])
            ->keyBy(function (IndicatorCapture $capture) use ($periods): int {
                $period = $periods->firstWhere('id', $capture->period_id);
                return (int) ($period?->month ?? 0);
            });

        $captureIds = $captures->pluck('id')->filter()->values();
        $improvements = Improvement::query()
            ->whereIn('indicator_capture_id', $captureIds)
            ->get(['indicator_capture_id'])
            ->pluck('indicator_capture_id')
            ->flip();

        $rows = [];
        $totals = [];
        $trained = [];
        $percentages = [];

        for ($m = 1; $m <= 12; $m++) {
            /** @var IndicatorCapture|null $capture */
            $capture = $captures->get($m);
            $inputData = is_array($capture?->input_data) ? $capture->input_data : [];
            $totalPersonal = (float) ($inputData['total_personal'] ?? 0);
            $personalCapacitado = (float) ($inputData['personal_capacitado'] ?? 0);
            $result = (float) ($capture?->result_percentage ?? 0);
            $analysis = trim((string) ($capture?->analysis_text ?? ''));
            $analysis = preg_replace('/\n{3,}/', "\n\n", $analysis) ?? $analysis;

            $rows[] = [
                'month_number' => $m,
                'month' => $monthNames[$m],
                'total_personal' => $totalPersonal,
                'personal_capacitado' => $personalCapacitado,
                'result_percentage' => $result,
                'analysis' => $analysis,
                'complies' => (bool) ($capture?->complies ?? false),
                'improvement' => $capture ? $improvements->has($capture->id) : false,
            ];

            $totals[] = $totalPersonal;
            $trained[] = $personalCapacitado;
            $percentages[] = $result;
        }

        $this->sheetRows = $rows;
        $this->chartPayload = [
            'months' => array_values($monthNames),
            'total_personal' => $totals,
            'personal_capacitado' => $trained,
            'result_percentage' => $percentages,
            'meta' => array_fill(0, 12, (float) $this->indicator->target_value),
            'year' => $this->selectedYear,
        ];

        $this->dispatch('ft-op-01-chart-refresh', payload: $this->chartPayload);
    }

    public function render()
    {
        return view('livewire.indicators.ft-op-01-form', [
            'fieldsView' => $this->fieldsView,
        ]);
    }
}
