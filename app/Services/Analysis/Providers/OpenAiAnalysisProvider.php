<?php

namespace App\Services\Analysis\Providers;

use App\Models\AnalysisSetting;
use App\Models\Indicator;
use GuzzleHttp\Client;
use Throwable;

class OpenAiAnalysisProvider
{
    public function __construct(private readonly RulesAnalysisProvider $rulesProvider)
    {
    }

    public function generateForIndicator(AnalysisSetting $setting, Indicator $indicator, array $context): array
    {
        $apiKey = (string) env('OPENAI_API_KEY', '');
        if ($apiKey === '') {
            return $this->fallback('OpenAI no configurado: falta OPENAI_API_KEY en .env.', $indicator, $context);
        }

        try {
            $payload = $this->callOpenAi($apiKey, $setting, $this->buildPrompt($indicator, $context));
            if (! $payload) {
                return $this->fallback('OpenAI respondio sin texto util.', $indicator, $context);
            }

            return [
                'text' => $payload,
                'provider' => 'openai',
                'fallback' => false,
                'message' => null,
            ];
        } catch (Throwable $e) {
            return $this->fallback('OpenAI no disponible: '.$e->getMessage(), $indicator, $context);
        }
    }

    public function generateForDashboard(AnalysisSetting $setting, array $context): array
    {
        $apiKey = (string) env('OPENAI_API_KEY', '');
        if ($apiKey === '') {
            return [
                'text' => $this->rulesProvider->generateForDashboard($context),
                'provider' => 'rules',
                'fallback' => true,
                'message' => 'OpenAI no configurado: falta OPENAI_API_KEY en .env.',
            ];
        }

        try {
            $payload = $this->callOpenAi(
                $apiKey,
                $setting,
                'Genera un resumen ejecutivo breve para operaciones de seguridad privada con foco en prioridades del mes.'
            );

            if (! $payload) {
                return [
                    'text' => $this->rulesProvider->generateForDashboard($context),
                    'provider' => 'rules',
                    'fallback' => true,
                    'message' => 'OpenAI respondio sin texto util.',
                ];
            }

            return [
                'text' => $payload,
                'provider' => 'openai',
                'fallback' => false,
                'message' => null,
            ];
        } catch (Throwable $e) {
            return [
                'text' => $this->rulesProvider->generateForDashboard($context),
                'provider' => 'rules',
                'fallback' => true,
                'message' => 'OpenAI no disponible: '.$e->getMessage(),
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
        return "Analiza {$indicator->code} ({$indicator->name}), resultado {$result}%, tendencia {$trend}. Entrega texto breve con analisis y accion definida.";
    }

    private function callOpenAi(string $apiKey, AnalysisSetting $setting, string $prompt): ?string
    {
        $client = new Client([
            'base_uri' => 'https://api.openai.com',
            'timeout' => max(1, ((int) $setting->openai_timeout_ms) / 1000),
            'connect_timeout' => max(1, ((int) $setting->openai_timeout_ms) / 1000),
        ]);

        $model = $setting->openai_model ?: (string) env('OPENAI_MODEL', 'gpt-4o-mini');

        $response = $client->post('/v1/responses', [
            'headers' => [
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [
                            ['type' => 'input_text', 'text' => 'Eres analista senior de operaciones de seguridad privada. Responde en espanol claro y accionable.'],
                        ],
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'input_text', 'text' => $prompt],
                        ],
                    ],
                ],
            ],
        ]);

        $decoded = json_decode((string) $response->getBody(), true);
        if (! is_array($decoded)) {
            return null;
        }

        if (! empty($decoded['output_text'])) {
            return trim((string) $decoded['output_text']);
        }

        $chunks = [];
        foreach (($decoded['output'] ?? []) as $output) {
            foreach (($output['content'] ?? []) as $content) {
                if (($content['type'] ?? null) === 'output_text' && ! empty($content['text'])) {
                    $chunks[] = $content['text'];
                }
            }
        }

        return $chunks ? trim(implode("\n", $chunks)) : null;
    }
}
