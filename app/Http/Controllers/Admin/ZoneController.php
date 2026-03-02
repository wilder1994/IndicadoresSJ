<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ZoneStoreRequest;
use App\Http\Requests\ZoneUpdateRequest;
use App\Models\Zone;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
        $this->authorizeResource(Zone::class, 'zone');
    }

    public function index(): View
    {
        $zones = Zone::query()->withCount('users')->orderBy('name')->paginate(15);

        return view('admin.zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('admin.zones.create');
    }

    public function store(ZoneStoreRequest $request): RedirectResponse
    {
        $zone = Zone::query()->create($request->validated());

        $this->auditLogService->logModelChange(
            eventType: 'zone',
            action: 'create',
            model: $zone,
            before: null,
            after: $zone->toArray(),
            reason: 'Creacion de zona'
        );

        return redirect()->route('admin.zones.index')->with('status', 'Zona creada correctamente.');
    }

    public function edit(Zone $zone): View
    {
        return view('admin.zones.edit', compact('zone'));
    }

    public function update(ZoneUpdateRequest $request, Zone $zone): RedirectResponse
    {
        $before = $zone->toArray();
        $zone->update($request->validated());
        $after = $zone->fresh()->toArray();

        $this->auditLogService->logModelChange(
            eventType: 'zone',
            action: 'update',
            model: $zone,
            before: $before,
            after: $after,
            reason: 'Actualizacion de zona'
        );

        return redirect()->route('admin.zones.index')->with('status', 'Zona actualizada correctamente.');
    }

    public function destroy(Zone $zone): RedirectResponse
    {
        $before = $zone->toArray();
        $zone->delete();

        $this->auditLogService->logModelChange(
            eventType: 'zone',
            action: 'delete',
            model: $zone,
            before: $before,
            after: null,
            reason: 'Eliminacion de zona'
        );

        return redirect()->route('admin.zones.index')->with('status', 'Zona eliminada correctamente.');
    }
}
