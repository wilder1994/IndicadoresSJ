<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Models\Zone;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): View
    {
        $users = User::query()->with('zones')->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $zones = Zone::query()->orderBy('name')->get();

        return view('admin.users.create', compact('zones'));
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->zones()->sync($data['zone_ids']);

        $this->auditLogService->logModelChange(
            eventType: 'user',
            action: 'create',
            model: $user,
            before: null,
            after: $user->fresh()->load('zones')->toArray(),
            reason: 'Creacion de usuario'
        );

        return redirect()->route('admin.users.index')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        $zones = Zone::query()->orderBy('name')->get();
        $user->load('zones');

        return view('admin.users.edit', compact('user', 'zones'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $before = $user->load('zones')->toArray();

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);
        $user->zones()->sync($data['zone_ids']);

        $after = $user->fresh()->load('zones')->toArray();

        $this->auditLogService->logModelChange(
            eventType: 'user',
            action: 'update',
            model: $user,
            before: $before,
            after: $after,
            reason: 'Actualizacion de usuario'
        );

        return redirect()->route('admin.users.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $before = $user->load('zones')->toArray();
        $user->delete();

        $this->auditLogService->logModelChange(
            eventType: 'user',
            action: 'delete',
            model: $user,
            before: $before,
            after: null,
            reason: 'Eliminacion de usuario'
        );

        return redirect()->route('admin.users.index')->with('status', 'Usuario eliminado correctamente.');
    }
}
