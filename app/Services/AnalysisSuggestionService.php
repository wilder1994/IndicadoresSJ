<?php

namespace App\Services;

use App\Models\AnalysisSetting;
use App\Models\Indicator;

class AnalysisSuggestionService
{
    public function generate(Indicator $indicator, array $context): string
    {
        $setting = AnalysisSetting::query()->first();

        if (! $setting) {
            return 'No existe configuracion de analisis. Contacta al administrador.';
        }

        return match ($setting->mode) {
            AnalysisSetting::MODE_RULES => $this->rulesSuggestion($indicator, $context),
            AnalysisSetting::MODE_LOCAL => $this->localAiStub($setting),
            AnalysisSetting::MODE_OPENAI => $this->openAiStub($setting),
            default => 'Modo de analisis no soportado.',
        };
    }

    public function generateDashboardSummary(array $context): string
    {
        $setting = AnalysisSetting::query()->first();
        if (! $setting) {
            return 'No existe configuracion de analisis. Contacta al administrador.';
        }

        return match ($setting->mode) {
            AnalysisSetting::MODE_RULES => $this->rulesDashboardSuggestion($context),
            AnalysisSetting::MODE_LOCAL => $this->localAiStub($setting),
            AnalysisSetting::MODE_OPENAI => $this->openAiStub($setting),
            default => 'Modo de analisis no soportado.',
        };
    }

    private function rulesSuggestion(Indicator $indicator, array $context): string
    {
        $result = (float) ($context['result_percentage'] ?? 0);
        $meta = (float) $indicator->target_value;
        $trend = $context['trend_label'] ?? 'sin tendencia';
        $history = $context['history_label'] ?? 'sin historico';

        $status = $context['complies'] ? 'cumple meta' : 'no cumple meta';

        $template = match ($indicator->code) {
            'FT-OP-02' => 'Incrementar controles operativos y reforzar acciones preventivas para reducir servicios no conformes.',
            'FT-OP-03' => 'Priorizar controles de siniestralidad: revisar frecuencia por tipo y mitigar impacto economico en origen.',
            'FT-OP-06' => 'Meta cero eventos: fortalecer acciones de contencion temprana y seguimiento diario.',
            default => 'Mantener seguimiento del plan operativo y ajustar acciones sobre causas raiz del resultado mensual.',
        };

        return implode("\n", [
            "Sugerencia automatica ({$indicator->code}):",
            "- Resultado actual: {$result}%",
            "- Meta: {$meta}% ({$indicator->target_operator})",
            "- Estado: {$status}",
            "- Tendencia reciente: {$trend}",
            "- Historico: {$history}",
            "- Recomendacion: {$template}",
        ]);
    }

    private function rulesDashboardSuggestion(array $context): string
    {
        $score = $context['score'] ?? 0;
        $state = $context['state'] ?? 'SIN ESTADO';
        $topZone = $context['top_zone'] ?? 'N/A';
        $critical = $context['critical_indicator'] ?? 'N/A';

        return implode("\n", [
            "Resumen ejecutivo sugerido:",
            "- Score global: {$score}%",
            "- Estado general: {$state}",
            "- Zona destacada: {$topZone}",
            "- Indicador mas critico: {$critical}",
            "- Recomendacion: priorizar planes de accion en indicadores en rojo y seguimiento semanal por zona.",
        ]);
    }

    private function localAiStub(AnalysisSetting $setting): string
    {
        if (! $setting->local_endpoint_url || ! $setting->local_model) {
            return 'IA Local no configurada: define URL y modelo en Configuracion de Analisis.';
        }

        return 'IA Local (stub): endpoint configurado. Integraremos llamada HTTP en fase de integracion avanzada.';
    }

    private function openAiStub(AnalysisSetting $setting): string
    {
        if (! env('OPENAI_API_KEY')) {
            return 'OpenAI no configurado: falta OPENAI_API_KEY en .env.';
        }

        return 'OpenAI (stub): credencial detectada y modelo '.$setting->openai_model.' configurado.';
    }
}
