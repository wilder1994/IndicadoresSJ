<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('improvements', function (Blueprint $table): void {
            if (! Schema::hasColumn('improvements', 'improvement_required')) {
                $table->longText('improvement_required')->nullable()->after('action_defined');
            }
        });
    }

    public function down(): void
    {
        Schema::table('improvements', function (Blueprint $table): void {
            if (Schema::hasColumn('improvements', 'improvement_required')) {
                $table->dropColumn('improvement_required');
            }
        });
    }
};

