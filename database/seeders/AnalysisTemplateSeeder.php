<?php

namespace Database\Seeders;

use App\Models\AnalysisTemplate;
use App\Models\Indicator;
use Illuminate\Database\Seeder;

class AnalysisTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $indicators = Indicator::query()->orderBy('code')->get();

        foreach ($indicators as $indicator) {
            AnalysisTemplate::query()->updateOrCreate(
                ['indicator_id' => $indicator->id],
                [
                    'plantilla_cumple' => 'El indicador {indicator_code} ({indicator_name}) cumple la meta {operador} {meta}% con un resultado de {resultado}% en {mes_actual}. Tendencia: {tendencia}.',
                    'plantilla_no_cumple' => 'El indicador {indicator_code} ({indicator_name}) no cumple la meta {operador} {meta}%; resultado actual {resultado}% en {mes_actual}. Mes anterior: {mes_anterior_resultado}%. Tendencia: {tendencia}.',
                    'sugerencias_accion' => [
                        'Revisar causas raiz del desvio y definir responsables por zona.',
                        'Aplicar seguimiento semanal hasta recuperar cumplimiento.',
                        'Registrar evidencia de acciones en el analisis del periodo.',
                    ],
                ]
            );
        }
    }
}
