<?php

namespace App\Services;

use App\Models\AnalysisSetting;
use App\Models\AnalysisTemplate;
use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\DB;

class DocumentationService
{
    public function createVersion(
        Document $document,
        string $content,
        string $status,
        string $changeSummary,
        string $changeReason
    ): DocumentVersion {
        return DB::transaction(function () use ($document, $content, $status, $changeSummary, $changeReason): DocumentVersion {
            $nextVersion = ((int) $document->versions()->max('version_number')) + 1;

            $version = $document->versions()->create([
                'version_number' => $nextVersion,
                'status' => $status,
                'content' => $content,
                'change_summary' => $changeSummary,
                'change_reason' => $changeReason,
                'author_user_id' => auth()->id(),
                'published_at' => $status === 'vigente' ? now() : null,
            ]);

            if ($status === 'vigente') {
                $document->current_version_id = $version->id;
                $document->save();
            }

            return $version;
        });
    }

    public function upsertDashboardWeightsDocument(string $content, string $reason): DocumentVersion
    {
        $document = Document::query()->firstOrCreate(
            ['slug' => 'pesos-dashboard'],
            [
                'title' => 'Pesos del Dashboard Ejecutivo',
                'scope' => 'dashboard',
                'indicator_id' => null,
                'is_active' => true,
            ]
        );

        return $this->createVersion(
            document: $document,
            content: $content,
            status: 'vigente',
            changeSummary: 'Actualizacion de pesos del dashboard.',
            changeReason: $reason
        );
    }

    public function upsertAnalysisMethodologyDocument(AnalysisSetting $setting, string $reason): DocumentVersion
    {
        $document = Document::query()->firstOrCreate(
            ['slug' => 'metodologia-analisis'],
            [
                'title' => 'Metodologia de Analisis',
                'scope' => 'system',
                'indicator_id' => null,
                'is_active' => true,
            ]
        );

        $content = implode("\n", [
            'Modo activo: '.$setting->mode,
            'Reglas habilitadas: '.($setting->rules_enabled ? 'SI' : 'NO'),
            'Local endpoint: '.($setting->local_endpoint_url ?: 'N/A'),
            'Local model: '.($setting->local_model ?: 'N/A'),
            'Local timeout(ms): '.$setting->local_timeout_ms,
            'OpenAI model: '.$setting->openai_model,
            'OpenAI timeout(ms): '.$setting->openai_timeout_ms,
            'OPENAI_API_KEY: se gestiona solo por .env (no almacenada en BD).',
        ]);

        return $this->createVersion(
            document: $document,
            content: $content,
            status: 'vigente',
            changeSummary: 'Actualizacion de metodologia de analisis inteligente.',
            changeReason: $reason
        );
    }

    public function upsertAnalysisTemplatesDocument(string $reason): DocumentVersion
    {
        $document = Document::query()->firstOrCreate(
            ['slug' => 'plantillas-analisis'],
            [
                'title' => 'Plantillas de Analisis',
                'scope' => 'system',
                'indicator_id' => null,
                'is_active' => true,
            ]
        );

        $templates = AnalysisTemplate::query()->with('indicator')->orderBy('indicator_id')->get();
        $content = $templates->map(function (AnalysisTemplate $template): string {
            $code = $template->indicator?->code ?? 'N/A';
            $name = $template->indicator?->name ?? 'N/A';
            $sugerencias = collect($template->sugerencias_accion ?? [])
                ->map(fn (string $item) => '- '.$item)
                ->implode("\n");

            return implode("\n", [
                "[$code] $name",
                'plantilla_cumple:',
                $template->plantilla_cumple,
                'plantilla_no_cumple:',
                $template->plantilla_no_cumple,
                'sugerencias_accion:',
                $sugerencias,
                '---',
            ]);
        })->implode("\n");

        return $this->createVersion(
            document: $document,
            content: $content !== '' ? $content : 'Sin plantillas configuradas.',
            status: 'vigente',
            changeSummary: 'Actualizacion de plantillas de analisis por indicador.',
            changeReason: $reason
        );
    }
}
