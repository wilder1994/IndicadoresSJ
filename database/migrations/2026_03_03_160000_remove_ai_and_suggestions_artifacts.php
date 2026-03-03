<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('analysis_templates');
        Schema::dropIfExists('analysis_settings');

        if (Schema::hasTable('dashboard_summaries') && Schema::hasColumn('dashboard_summaries', 'generated_text')) {
            Schema::table('dashboard_summaries', function ($table): void {
                $table->dropColumn('generated_text');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intencionalmente vacio: no se restauran artefactos eliminados.
    }
};
