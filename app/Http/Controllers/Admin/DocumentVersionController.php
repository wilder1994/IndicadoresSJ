<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentVersionStoreRequest;
use App\Models\Document;
use App\Services\AuditLogService;
use App\Services\DocumentationService;
use Illuminate\Http\RedirectResponse;

class DocumentVersionController extends Controller
{
    public function __construct(
        private readonly DocumentationService $documentationService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function store(DocumentVersionStoreRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $before = $document->load('currentVersion')->toArray();
        $validated = $request->validated();

        $version = $this->documentationService->createVersion(
            document: $document,
            content: $validated['content'],
            status: $validated['status'],
            changeSummary: $validated['change_summary'],
            changeReason: $validated['change_reason']
        );

        $this->auditLogService->logModelChange(
            eventType: 'document_version',
            action: 'create',
            model: $document,
            before: $before,
            after: $document->fresh()->load('currentVersion')->toArray(),
            reason: $validated['change_reason'],
            metadata: ['document_version_id' => $version->id]
        );

        return redirect()->route('admin.documents.show', $document)->with('status', 'Nueva version registrada correctamente.');
    }
}
