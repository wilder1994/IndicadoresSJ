<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\View\View;

class ZoneDashboardController extends Controller
{
    public function show(Zone $zone): View
    {
        return view('zones.show', [
            'zone' => $zone,
        ]);
    }
}
