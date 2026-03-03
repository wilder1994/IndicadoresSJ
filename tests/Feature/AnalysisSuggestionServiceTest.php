<?php

namespace Tests\Feature;

use App\Models\AnalysisSetting;
use App\Models\AnalysisTemplate;
use App\Models\Indicator;
use App\Models\User;
use App\Services\AnalysisSuggestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisSuggestionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_rules_provider_generates_text(): void
    {
        [$indicator] = $this->seedMinimalAnalysisData(AnalysisSetting::MODE_RULES);
        $service = app(AnalysisSuggestionService::class);

        $text = $service->generate($indicator, [
            'result_percentage' => 92.5,
            'complies' => true,
            'trend_label' => 'mejorando',
            'history_label' => 'promedio 89%',
            'month_name' => 'Marzo',
        ]);

        $this->assertStringContainsString('FT-OP-01', $text);
        $this->assertStringContainsString('Sugerencias', $text);
    }

    public function test_openai_mode_without_key_falls_back_to_rules(): void
    {
        [$indicator] = $this->seedMinimalAnalysisData(AnalysisSetting::MODE_OPENAI);
        putenv('OPENAI_API_KEY');
        $_ENV['OPENAI_API_KEY'] = '';
        $_SERVER['OPENAI_API_KEY'] = '';

        $service = app(AnalysisSuggestionService::class);
        $text = $service->generate($indicator, [
            'result_percentage' => 70,
            'complies' => false,
            'trend_label' => 'en descenso',
            'history_label' => 'promedio 80%',
            'month_name' => 'Marzo',
        ]);

        $this->assertStringContainsString('FT-OP-01', $text);
        $this->assertStringContainsString('Nota tecnica', $text);
        $this->assertStringContainsString('OPENAI_API_KEY', $text);
    }

    public function test_local_ai_with_down_endpoint_falls_back_to_rules(): void
    {
        [$indicator, $setting] = $this->seedMinimalAnalysisData(AnalysisSetting::MODE_LOCAL);
        $setting->update([
            'local_endpoint_url' => 'http://127.0.0.1:9/api/generate',
            'local_model' => 'local-test-model',
            'local_timeout_ms' => 1000,
        ]);

        $service = app(AnalysisSuggestionService::class);
        $text = $service->generate($indicator, [
            'result_percentage' => 70,
            'complies' => false,
            'trend_label' => 'en descenso',
            'history_label' => 'promedio 80%',
            'month_name' => 'Marzo',
        ]);

        $this->assertStringContainsString('FT-OP-01', $text);
        $this->assertStringContainsString('Nota tecnica', $text);
        $this->assertStringContainsString('IA Local', $text);
    }

    private function seedMinimalAnalysisData(string $mode): array
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $indicator = Indicator::query()->create([
            'code' => 'FT-OP-01',
            'name' => 'Capacitacion',
            'unit' => 'percentage',
            'target_value' => 90,
            'target_operator' => '>=',
            'frequency' => 'monthly',
            'formula_description' => 'personal_capacitado / total_personal * 100',
            'required_fields' => ['total_personal', 'personal_capacitado', 'analisis_texto'],
            'allows_over_100' => false,
            'is_active' => true,
        ]);

        $setting = AnalysisSetting::query()->create([
            'mode' => $mode,
            'rules_enabled' => true,
            'local_timeout_ms' => 1000,
            'openai_model' => 'gpt-4o-mini',
            'openai_timeout_ms' => 1000,
            'updated_by_user_id' => $admin->id,
        ]);

        AnalysisTemplate::query()->create([
            'indicator_id' => $indicator->id,
            'plantilla_cumple' => 'Indicador {indicator_code} cumple con {resultado}%.',
            'plantilla_no_cumple' => 'Indicador {indicator_code} no cumple con {resultado}%.',
            'sugerencias_accion' => ['Accion 1', 'Accion 2'],
            'updated_by_user_id' => $admin->id,
        ]);

        return [$indicator, $setting];
    }
}
