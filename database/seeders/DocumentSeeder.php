<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\Indicator;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'slug' => 'reglas-del-sistema',
                'title' => 'Reglas del Sistema',
                'scope' => 'system',
                'indicator_code' => null,
                'content' => 'Documento base con reglas globales, permisos, cierre de periodos, semaforo y trazabilidad.',
            ],
            [
                'slug' => 'pesos-dashboard',
                'title' => 'Pesos del Dashboard Ejecutivo',
                'scope' => 'dashboard',
                'indicator_code' => null,
                'content' => 'Define la ponderacion por indicador y criterio de estado general.',
            ],
            [
                'slug' => 'metodologia-analisis',
                'title' => 'Metodologia de Analisis',
                'scope' => 'system',
                'indicator_code' => null,
                'content' => 'Describe modos de analisis (rules, local_ai, openai), configuracion activa y criterios de fallback.',
            ],
            [
                'slug' => 'plantillas-analisis',
                'title' => 'Plantillas de Analisis',
                'scope' => 'system',
                'indicator_code' => null,
                'content' => 'Plantillas por indicador: plantilla_cumple, plantilla_no_cumple y sugerencias_accion.',
            ],
        ];

        foreach ($documents as $item) {
            $this->upsertDocument($item);
        }

        $indicators = Indicator::query()->orderBy('code')->get();
        foreach ($indicators as $indicator) {
            $this->upsertDocument([
                'slug' => 'indicador-'.$indicator->code,
                'title' => 'Documento '.$indicator->code,
                'scope' => 'indicator',
                'indicator_code' => $indicator->code,
                'content' => 'Incluye formula, meta, campos, validaciones, semaforo y sustentacion de '.$indicator->code.'.',
            ]);
        }
    }

    private function upsertDocument(array $item): void
    {
        $indicatorId = null;
        if (! empty($item['indicator_code'])) {
            $indicatorId = Indicator::query()
                ->where('code', $item['indicator_code'])
                ->value('id');
        }

        $document = Document::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [
                'title' => $item['title'],
                'scope' => $item['scope'],
                'indicator_id' => $indicatorId,
                'is_active' => true,
            ]
        );

        $version = DocumentVersion::query()->updateOrCreate(
            ['document_id' => $document->id, 'version_number' => 1],
            [
                'status' => 'vigente',
                'content' => $item['content'],
                'change_summary' => 'Version inicial del documento.',
                'change_reason' => 'Creacion inicial.',
                'author_user_id' => null,
                'published_at' => now(),
            ]
        );

        if ($document->current_version_id !== $version->id) {
            $document->current_version_id = $version->id;
            $document->save();
        }
    }
}
