<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\DashboardWeight;
use App\Models\DashboardSummary;
use App\Models\Document;
use App\Models\Period;
use App\Models\User;
use App\Models\Zone;
use App\Policies\AuditLogPolicy;
use App\Policies\DashboardWeightPolicy;
use App\Policies\DashboardSummaryPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\PeriodPolicy;
use App\Policies\UserPolicy;
use App\Policies\ZonePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Zone::class => ZonePolicy::class,
        User::class => UserPolicy::class,
        Period::class => PeriodPolicy::class,
        DashboardWeight::class => DashboardWeightPolicy::class,
        DashboardSummary::class => DashboardSummaryPolicy::class,
        Document::class => DocumentPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn (User $user): bool => $user->isAdmin());
        Gate::define('access-zone', fn (User $user, Zone $zone): bool => $user->hasZoneAccess($zone->id));
    }
}
