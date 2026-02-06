<?php

namespace App\Providers;

use App\Listeners\LogUserActivity;
use App\Models\FiscalYear;
use App\Models\User;
use App\Policies\FiscalYearPolicy;
use App\Policies\UserPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(FiscalYear::class, FiscalYearPolicy::class);

        $this->registerEventListeners();
    }

    protected function registerEventListeners(): void
    {
        Event::listen(Login::class, LogUserActivity::class);
        Event::listen(Logout::class, LogUserActivity::class);
        Event::listen(Failed::class, LogUserActivity::class);
        Event::listen(PasswordReset::class, LogUserActivity::class);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
