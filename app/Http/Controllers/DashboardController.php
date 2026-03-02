<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $zones = $user->isAdmin()
            ? Zone::query()->orderBy('name')->get()
            : $user->zones()->orderBy('name')->get();

        return view('dashboard', [
            'zones' => $zones,
        ]);
    }
}
