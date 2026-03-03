<?php

namespace Tests\Feature;

use App\Models\Indicator;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_cannot_access_admin_analysis_settings_or_templates(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USUARIO]);

        $this->actingAs($user)
            ->get(route('admin.settings.analysis.edit'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.analysis-templates.index'))
            ->assertForbidden();
    }

    public function test_usuario_can_access_indicator_page_to_generate_suggestions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USUARIO]);
        $zone = Zone::query()->create([
            'code' => 'Z01',
            'name' => 'Zona 01',
            'is_active' => true,
        ]);
        $user->zones()->attach($zone->id);

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

        $this->actingAs($user)
            ->get(route('indicators.show', ['indicator' => $indicator->code]))
            ->assertOk();
    }
}
