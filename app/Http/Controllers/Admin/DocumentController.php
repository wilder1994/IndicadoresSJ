<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentStoreRequest;
use App\Http\Requests\DocumentUpdateRequest;
use App\Models\Document;
use App\Models\Indicator;
use App\Services\AuditLogService;
use App\Services\DocumentationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly DocumentationService $documentationService
    ) {
        $this->authorizeResource(Document::class, 'document');
    }

    public function index(): View
    {
        $documents = Document::query()
            ->with(['indicator', 'currentVersion'])
            ->orderBy('title')
            ->paginate(20);

        return view('admin.documents.index', compact('documents'));
    }

    public function create(): View
    {
        $indicators = Indicator::query()->orderBy('code')->get();
        return view('admin.documents.create', compact('indicators'));
    }

    public function store(DocumentStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $document = Document::query()->create([
            'slug' => $validated['slug'],
            'title' => $validated['title'],
            'scope' => $validated['scope'],
            'indicator_id' => $validated['indicator_id'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        $version = $this->documentationService->createVersion(
            document: $document,
            content: $validated['content'],
            status: $validated['initial_status'],
            changeSummary: $validated['change_summary'],
            changeReason: $validated['change_reason']
        );

        $this->auditLogService->logModelChange(
            eventType: 'document',
            action: 'create',
            model: $document,
            before: null,
            after: $document->fresh()->load('currentVersion')->toArray(),
            reason: $validated['change_reason'],
            metadata: ['document_version_id' => $version->id]
        );

        return redirect()->route('admin.documents.index')->with('status', 'Documento creado correctamente.');
    }

    public function show(Document $document): View
    {
        $document->load(['indicator', 'currentVersion', 'versions.author']);

        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        $indicators = Indicator::query()->orderBy('code')->get();
        return view('admin.documents.edit', compact('document', 'indicators'));
    }

    public function update(DocumentUpdateRequest $request, Document $document): RedirectResponse
    {
        $before = $document->toArray();
        $validated = $request->validated();

        $document->update([
            'slug' => $validated['slug'],
            'title' => $validated['title'],
            'scope' => $validated['scope'],
            'indicator_id' => $validated['indicator_id'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        $this->auditLogService->logModelChange(
            eventType: 'document',
            action: 'update',
            model: $document,
            before: $before,
            after: $document->fresh()->toArray(),
            reason: $validated['reason']
        );

        return redirect()->route('admin.documents.show', $document)->with('status', 'Documento actualizado correctamente.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $before = $document->load('versions')->toArray();
        $document->delete();

        $this->auditLogService->logModelChange(
            eventType: 'document',
            action: 'delete',
            model: $document,
            before: $before,
            after: null,
            reason: 'Eliminacion de documento'
        );

        return redirect()->route('admin.documents.index')->with('status', 'Documento eliminado.');
    }
}
