<?php

namespace Database\Seeders;

use App\Models\AnalysisSetting;
use Illuminate\Database\Seeder;

class AnalysisSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AnalysisSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'mode' => AnalysisSetting::MODE_RULES,
                'rules_enabled' => true,
                'local_endpoint_url' => 'http://127.0.0.1:11434/api/generate',
                'local_model' => 'local-model',
                'local_timeout_ms' => 10000,
                'openai_model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'openai_timeout_ms' => 10000,
                'updated_by_user_id' => null,
            ]
        );
    }
}
