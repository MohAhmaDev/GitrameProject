<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Creance;
use App\Models\Dette;
use App\Models\Employe;
use App\Models\Finance;
use App\Policies\CreancePolicy;
use App\Policies\DettePolicy;
use App\Policies\EmployePolicy;
use App\Policies\FinancePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Employe::class => EmployePolicy::class,
        Finance::class => FinancePolicy::class,
        Dette::class => DettePolicy::class,
        Creance::class => CreancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();
    }
}
