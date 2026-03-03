<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardWeightController;
use App\Http\Controllers\Admin\OperationsDashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentVersionController;
use App\Http\Controllers\Admin\MotherIndicatorController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\ZoneDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/indicadores', [IndicatorController::class, 'index'])->name('indicators.index');
    Route::get('/indicadores/{indicator:code}', [IndicatorController::class, 'show'])->name('indicators.show');
    Route::get('/exportes/indicadores/{indicator:code}/zona/excel', [ExportController::class, 'zoneExcel'])->name('exports.zone.excel');
    Route::get('/exportes/indicadores/{indicator:code}/zona/pdf', [ExportController::class, 'zonePdf'])->name('exports.zone.pdf');

    Route::get('/zonas/{zone}', [ZoneDashboardController::class, 'show'])
        ->middleware('zone.access')
        ->name('zones.show');

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('zonas', ZoneController::class)
                ->except(['show'])
                ->names('zones')
                ->parameters(['zonas' => 'zone']);
            Route::resource('usuarios', UserController::class)
                ->except(['show'])
                ->names('users')
                ->parameters(['usuarios' => 'user']);

            Route::get('periodos', [PeriodController::class, 'index'])->name('periods.index');
            Route::post('periodos', [PeriodController::class, 'store'])->name('periods.store');
            Route::post('periodos/{period}/close', [PeriodController::class, 'close'])->name('periods.close');
            Route::post('periodos/{period}/reopen', [PeriodController::class, 'reopen'])->name('periods.reopen');

            Route::get('configuracion/pesos', [DashboardWeightController::class, 'edit'])->name('settings.weights.edit');
            Route::put('configuracion/pesos', [DashboardWeightController::class, 'update'])->name('settings.weights.update');

            Route::resource('documentos', DocumentController::class)
                ->names('documents')
                ->parameters(['documentos' => 'document']);
            Route::post('documentos/{document}/versiones', [DocumentVersionController::class, 'store'])
                ->name('documents.versions.store');

            Route::get('auditoria', [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('madre', [MotherIndicatorController::class, 'index'])->name('mother.index');
            Route::get('madre/{indicator:code}', [MotherIndicatorController::class, 'show'])->name('mother.show');
            Route::get('madre/{indicator:code}/excel', [ExportController::class, 'motherExcel'])->name('exports.mother.excel');
            Route::get('madre/{indicator:code}/pdf', [ExportController::class, 'motherPdf'])->name('exports.mother.pdf');

            Route::get('dashboard', [OperationsDashboardController::class, 'index'])->name('dashboard.index');
            Route::post('dashboard/save-summary', [OperationsDashboardController::class, 'saveSummary'])->name('dashboard.save-summary');
            Route::get('dashboard/zone/{zone}', [OperationsDashboardController::class, 'zoneSummary'])->name('dashboard.zone-summary');
            Route::get('dashboard/pdf', [OperationsDashboardController::class, 'exportPdf'])->name('dashboard.pdf');
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
