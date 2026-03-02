<?php

namespace App\Policies;

use App\Models\DashboardWeight;
use App\Models\User;

class DashboardWeightPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DashboardWeight $dashboardWeight): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, DashboardWeight $dashboardWeight): bool
    {
        return $user->isAdmin();
    }
}
