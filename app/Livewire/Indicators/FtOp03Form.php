<?php

namespace App\Livewire\Indicators;

class FtOp03Form extends BaseIndicatorForm
{
    protected string $fieldsView = 'livewire.indicators.partials.ft-op-03';

    protected function defaultForm(): array
    {
        return [
            'total_servicios' => null,
            'total_siniestros' => null,
            'clasificacion_por_tipo' => [
                ['tipo' => '', 'cantidad' => null],
            ],
            'facturacion_mensual' => null,
            'valor_pagado_siniestros' => null,
        ];
    }

    protected function fieldRules(): array
    {
        return [
            'form.total_servicios' => ['required', 'numeric', 'min:0.01'],
            'form.total_siniestros' => ['required', 'numeric', 'min:0'],
            'form.clasificacion_por_tipo' => ['required', 'array', 'min:1'],
            'form.clasificacion_por_tipo.*.tipo' => ['required', 'string'],
            'form.clasificacion_por_tipo.*.cantidad' => ['required', 'numeric', 'min:0'],
            'form.facturacion_mensual' => ['required', 'numeric', 'min:0.01'],
            'form.valor_pagado_siniestros' => ['required', 'numeric', 'min:0'],
            'analysisText' => ['required', 'string'],
        ];
    }

    public function addTypeRow(): void
    {
        $this->form['clasificacion_por_tipo'][] = ['tipo' => '', 'cantidad' => null];
    }

    public function removeTypeRow(int $index): void
    {
        unset($this->form['clasificacion_por_tipo'][$index]);
        $this->form['clasificacion_por_tipo'] = array_values($this->form['clasificacion_por_tipo']);
    }

    protected function calculateMetrics(array $form): array
    {
        $totalServicios = (float) ($form['total_servicios'] ?? 0);
        $totalSiniestros = (float) ($form['total_siniestros'] ?? 0);
        $facturacion = (float) ($form['facturacion_mensual'] ?? 0);
        $valorPagado = (float) ($form['valor_pagado_siniestros'] ?? 0);
        $errors = [];

        if ($totalServicios <= 0) {
            $errors[] = 'total_servicios no puede ser 0.';
        }
        if ($facturacion <= 0) {
            $errors[] = 'facturacion_mensual no puede ser 0.';
        }

        $sumTipos = collect($form['clasificacion_por_tipo'] ?? [])->sum(fn ($row) => (float) ($row['cantidad'] ?? 0));
        if (round($sumTipos, 2) !== round($totalSiniestros, 2)) {
            $errors[] = 'La suma por tipo debe ser igual a total_siniestros.';
        }

        $freq = $totalServicios > 0 ? round(($totalSiniestros / $totalServicios) * 100, 2) : 0;
        $impacto = $facturacion > 0 ? round(($valorPagado / $facturacion) * 100, 2) : 0;
        $cumpleA = $totalServicios > 0 && $freq <= 3;
        $cumpleB = $facturacion > 0 && $impacto <= 1;
        $complies = $cumpleA && $cumpleB;

        return [
            'numerator' => $totalSiniestros + $valorPagado,
            'denominator' => $totalServicios + $facturacion,
            'result_percentage' => $freq,
            'complies' => $complies,
            'errors' => $errors,
        ];
    }
}
