<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::registerView(function () {
            return view('user.auth.register');
        });

        Fortify::loginView(function () {
            if (RequestFacade::is('admin/login')) {
                return view('admin.auth.login');
            }

            return view('user.auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request -> email;

            return Limit::perMinute(10) -> by($email . $request -> ip());
        });
    }
}
