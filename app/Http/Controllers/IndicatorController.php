<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Illuminate\View\View;

class IndicatorController extends Controller
{
    public function index(): View
    {
        $indicators = Indicator::query()->where('is_active', true)->orderBy('code')->get();

        return view('indicators.index', compact('indicators'));
    }

    public function show(Indicator $indicator): View
    {
        abort_unless($indicator->is_active, 404);

        return view('indicators.show', compact('indicator'));
    }
}
