<?php

namespace App\Services;

use App\Models\AnalysisSetting;
use App\Models\Indicator;
use App\Services\Analysis\Providers\LocalAiAnalysisProvider;
use App\Services\Analysis\Providers\OpenAiAnalysisProvider;
use App\Services\Analysis\Providers\RulesAnalysisProvider;

class AnalysisSuggestionService
{
    public function __construct(
        private readonly RulesAnalysisProvider $rulesProvider,
        private readonly LocalAiAnalysisProvider $localAiProvider,
        private readonly OpenAiAnalysisProvider $openAiProvider,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function generate(Indicator $indicator, array $context): string
    {
        $setting = AnalysisSetting::query()->first();
        if (! $setting) {
            return 'No existe configuracion de analisis. Contacta al administrador.';
        }

        $result = match ($setting->mode) {
            AnalysisSetting::MODE_RULES => [
                'text' => $this->rulesProvider->generateForIndicator($indicator, $context),
                'provider' => 'rules',
                'fallback' => false,
                'message' => null,
            ],
            AnalysisSetting::MODE_LOCAL => $this->localAiProvider->generateForIndicator($setting, $indicator, $context),
            AnalysisSetting::MODE_OPENAI => $this->openAiProvider->generateForIndicator($setting, $indicator, $context),
            default => [
                'text' => $this->rulesProvider->generateForIndicator($indicator, $context),
                'provider' => 'rules',
                'fallback' => true,
                'message' => 'Modo de analisis no soportado, se uso fallback rules.',
            ],
        };

        $this->logGeneration($setting->mode, $result, $indicator->code, $context);

        return $result['message']
            ? $result['text']."\n\n[Nota tecnica] ".$result['message']
            : $result['text'];
    }

    public function generateDashboardSummary(array $context): string
    {
        $setting = AnalysisSetting::query()->first();
        if (! $setting) {
            return 'No existe configuracion de analisis. Contacta al administrador.';
        }

        $result = match ($setting->mode) {
            AnalysisSetting::MODE_RULES => [
                'text' => $this->rulesProvider->generateForDashboard($context),
                'provider' => 'rules',
                'fallback' => false,
                'message' => null,
            ],
            AnalysisSetting::MODE_LOCAL => $this->localAiProvider->generateForDashboard($setting, $context),
            AnalysisSetting::MODE_OPENAI => $this->openAiProvider->generateForDashboard($setting, $context),
            default => [
                'text' => $this->rulesProvider->generateForDashboard($context),
                'provider' => 'rules',
                'fallback' => true,
                'message' => 'Modo de analisis no soportado, se uso fallback rules.',
            ],
        };

        $this->logGeneration($setting->mode, $result, 'DASHBOARD', $context);

        return $result['message']
            ? $result['text']."\n\n[Nota tecnica] ".$result['message']
            : $result['text'];
    }

    private function logGeneration(string $mode, array $result, string $target, array $context): void
    {
        $this->auditLogService->logEvent(
            eventType: 'analysis_generation',
            action: 'generate',
            reason: 'Generacion de sugerencia de analisis',
            metadata: [
                'mode' => $mode,
                'provider' => $result['provider'] ?? 'unknown',
                'fallback' => (bool) ($result['fallback'] ?? false),
                'target' => $target,
                'year' => $context['year'] ?? null,
                'month' => $context['month'] ?? null,
                'zone_id' => $context['zone_id'] ?? null,
            ]
        );
    }
}
