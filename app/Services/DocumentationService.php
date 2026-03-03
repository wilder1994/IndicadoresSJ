<?php

namespace App\Services;

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

}
