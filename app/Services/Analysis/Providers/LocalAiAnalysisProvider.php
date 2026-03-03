<?php

namespace App\Services\Analysis\Providers;

use App\Models\AnalysisSetting;
use App\Models\Indicator;
use GuzzleHttp\Client;
use Throwable;

class LocalAiAnalysisProvider
{
    public function __construct(private readonly RulesAnalysisProvider $rulesProvider)
    {
    }

    public function generateForIndicator(AnalysisSetting $setting, Indicator $indicator, array $context): array
    {
        if (! $setting->local_endpoint_url || ! $setting->local_model) {
            return $this->fallback('IA Local no configurada (URL/modelo).', $indicator, $context);
        }

        try {
            $client = new Client([
                'timeout' => max(1, ((int) $setting->local_timeout_ms) / 1000),
                'connect_timeout' => max(1, ((int) $setting->local_timeout_ms) / 1000),
            ]);

            $response = $client->post($setting->local_endpoint_url, [
                'json' => [
                    'model' => $setting->local_model,
                    'prompt' => $this->buildPrompt($indicator, $context),
                    'context' => $context,
                ],
            ]);

            $decoded = json_decode((string) $response->getBody(), true);
            $text = $this->extractText($decoded);

            if (! $text) {
                return $this->fallback('IA Local respondio sin texto util.', $indicator, $context);
            }

            return [
                'text' => trim($text),
                'provider' => 'local_ai',
                'fallback' => false,
                'message' => null,
            ];
        } catch (Throwable $e) {
            return $this->fallback('IA Local no disponible: '.$e->getMessage(), $indicator, $context);
        }
    }

    public function generateForDashboard(AnalysisSetting $setting, array $context): array
    {
        if (! $setting->local_endpoint_url || ! $setting->local_model) {
            return [
                'text' => $this->rulesProvider->generateForDashboard($context),
                'provider' => 'rules',
                'fallback' => true,
                'message' => 'IA Local no configurada (URL/modelo).',
            ];
        }

        try {
            $client = new Client([
                'timeout' => max(1, ((int) $setting->local_timeout_ms) / 1000),
                'connect_timeout' => max(1, ((int) $setting->local_timeout_ms) / 1000),
            ]);

            $response = $client->post($setting->local_endpoint_url, [
                'json' => [
                    'model' => $setting->local_model,
                    'prompt' => 'Genera un resumen ejecutivo para operaciones de seguridad privada.',
                    'context' => $context,
                ],
            ]);

            $decoded = json_decode((string) $response->getBody(), true);
            $text = $this->extractText($decoded);

            if (! $text) {
                return [
                    'text' => $this->rulesProvider->generateForDashboard($context),
                    'provider' => 'rules',
                    'fallback' => true,
                    'message' => 'IA Local respondio sin texto util.',
                ];
            }

            return [
                'text' => trim($text),
                'provider' => 'local_ai',
                'fallback' => false,
                'message' => null,
            ];
        } catch (Throwable $e) {
            return [
                'text' => $this->rulesProvider->generateForDashboard($context),
                'provider' => 'rules',
                'fallback' => true,
                'message' => 'IA Local no disponible: '.$e->getMessage(),
            ];
        }
    }

    private function fallback(string $message, Indicator $indicator, array $context): array
    {
        return [
            'text' => $this->rulesProvider->generateForIndicator($indicator, $context),
            'provider' => 'rules',
            'fallback' => true,
            'message' => $message,
        ];
    }

    private function buildPrompt(Indicator $indicator, array $context): string
    {
        $result = $context['result_percentage'] ?? 0;
        $trend = $context['trend_label'] ?? 'sin tendencia';
        return "Analiza el indicador {$indicator->code} ({$indicator->name}). Resultado: {$result}%. Tendencia: {$trend}. Entrega texto ejecutivo y accionable.";
    }

    private function extractText(?array $decoded): ?string
    {
        if (! is_array($decoded)) {
            return null;
        }

        return $decoded['text']
            ?? $decoded['output_text']
            ?? $decoded['result']
            ?? $decoded['data']['text']
            ?? null;
    }
}
