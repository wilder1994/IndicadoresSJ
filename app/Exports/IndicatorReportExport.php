<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IndicatorReportExport implements FromView
{
    public function __construct(
        private readonly string $viewName,
        private readonly array $data
    ) {
    }

    public function view(): View
    {
        return view($this->viewName, $this->data);
    }
}
