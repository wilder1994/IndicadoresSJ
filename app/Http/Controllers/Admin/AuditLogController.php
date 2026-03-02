<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AuditLog::class);

        $logs = AuditLog::query()
            ->with('user')
            ->when($request->filled('event_type'), fn ($q) => $q->where('event_type', $request->get('event_type')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->get('action')))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $eventTypes = AuditLog::query()->select('event_type')->distinct()->orderBy('event_type')->pluck('event_type');
        $actions = AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'eventTypes', 'actions'));
    }
}
