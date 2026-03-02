<?php

namespace App\Policies;

use App\Models\DashboardSummary;
use App\Models\User;

class DashboardSummaryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DashboardSummary $dashboardSummary): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, DashboardSummary $dashboardSummary): bool
    {
        return $user->isAdmin();
    }
}
