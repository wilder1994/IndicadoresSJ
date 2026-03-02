<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analysis_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mode', 20)->default('rules');
            $table->boolean('rules_enabled')->default(true);

            $table->string('local_endpoint_url')->nullable();
            $table->string('local_model')->nullable();
            $table->unsignedInteger('local_timeout_ms')->default(10000);

            $table->string('openai_model')->default('gpt-4o-mini');
            $table->unsignedInteger('openai_timeout_ms')->default(10000);

            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_settings');
    }
};
