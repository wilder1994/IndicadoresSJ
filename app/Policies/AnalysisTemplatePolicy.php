<?php

namespace App\Policies;

use App\Models\AnalysisTemplate;
use App\Models\User;

class AnalysisTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, AnalysisTemplate $analysisTemplate): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, AnalysisTemplate $analysisTemplate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, AnalysisTemplate $analysisTemplate): bool
    {
        return $user->isAdmin();
    }
}
