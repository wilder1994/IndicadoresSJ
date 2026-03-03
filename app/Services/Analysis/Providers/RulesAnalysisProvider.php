<?php

namespace App\Services\Analysis\Providers;

use App\Models\AnalysisTemplate;
use App\Models\Indicator;

class RulesAnalysisProvider
{
    public function generateForIndicator(Indicator $indicator, array $context): string
    {
        $template = AnalysisTemplate::query()->where('indicator_id', $indicator->id)->first();
        $complies = (bool) ($context['complies'] ?? false);
        $base = $complies
            ? ($template?->plantilla_cumple ?? $this->defaultCumpleTemplate())
            : ($template?->plantilla_no_cumple ?? $this->defaultNoCumpleTemplate());

        $suggestions = collect($template?->sugerencias_accion ?? $this->defaultSuggestions())
            ->map(fn (string $item) => '- '.$item)
            ->implode("\n");

        $variables = $this->variables($indicator, $context) + [
            '{sugerencias_accion}' => $suggestions,
        ];

        return trim(strtr($base, $variables))."\n\nSugerencias:\n".$suggestions;
    }

    public function generateForDashboard(array $context): string
    {
        $score = $context['score'] ?? 0;
        $state = $context['state'] ?? 'SIN ESTADO';
        $topZone = $context['top_zone'] ?? 'N/A';
        $critical = $context['critical_indicator'] ?? 'N/A';

        return implode("\n", [
            'Resumen ejecutivo sugerido:',
            "- Score global: {$score}%",
            "- Estado general: {$state}",
            "- Zona destacada: {$topZone}",
            "- Indicador mas critico: {$critical}",
            '- Recomendacion: priorizar planes de accion en indicadores en rojo y seguimiento semanal por zona.',
        ]);
    }

    private function variables(Indicator $indicator, array $context): array
    {
        $result = (float) ($context['result_percentage'] ?? 0);
        $meta = (float) $indicator->target_value;
        $status = (bool) ($context['complies'] ?? false) ? 'cumple meta' : 'no cumple meta';
        $trend = $context['trend_label'] ?? 'sin tendencia';
        $history = $context['history_label'] ?? 'sin historico';
        $monthName = $context['month_name'] ?? 'mes actual';
        $previousMonth = $context['previous_month_result'] ?? null;
        $previousMonthText = $previousMonth !== null ? $previousMonth : 'sin dato';

        return [
            '{indicator_code}' => $indicator->code,
            '{indicator_name}' => $indicator->name,
            '{resultado}' => (string) $result,
            '{meta}' => (string) $meta,
            '{operador}' => $indicator->target_operator,
            '{estado}' => $status,
            '{tendencia}' => $trend,
            '{historico}' => $history,
            '{mes_actual}' => $monthName,
            '{mes_anterior_resultado}' => (string) $previousMonthText,
        ];
    }

    private function defaultCumpleTemplate(): string
    {
        return 'El indicador {indicator_code} ({indicator_name}) cumple la meta {operador} {meta}% con resultado {resultado}% en {mes_actual}. Estado: {estado}. Tendencia: {tendencia}. Historico: {historico}.';
    }

    private function defaultNoCumpleTemplate(): string
    {
        return 'El indicador {indicator_code} ({indicator_name}) no cumple la meta {operador} {meta}% con resultado {resultado}% en {mes_actual}. Mes anterior: {mes_anterior_resultado}%. Tendencia: {tendencia}. Historico: {historico}.';
    }

    private function defaultSuggestions(): array
    {
        return [
            'Revisar causas raiz del desvio y definir responsables por zona.',
            'Aplicar seguimiento semanal hasta recuperar cumplimiento.',
            'Registrar evidencia de las acciones implementadas.',
        ];
    }
}
