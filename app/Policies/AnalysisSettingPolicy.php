<?php

namespace App\Policies;

use App\Models\AnalysisSetting;
use App\Models\User;

class AnalysisSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, AnalysisSetting $analysisSetting): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, AnalysisSetting $analysisSetting): bool
    {
        return $user->isAdmin();
    }
}
