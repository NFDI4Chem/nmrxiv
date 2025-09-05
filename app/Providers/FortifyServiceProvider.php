<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

/**
 * Fortify Service Provider
 *
 * This service provider configures Laravel Fortify authentication features,
 * including user registration, login, password management, and two-factor
 * authentication. It also handles authentication disabling and rate limiting
 * for security purposes.
 *
 * @author NMRXIV Development Team
 *
 * @since 1.0.0
 */
class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method is intentionally empty as Fortify services are
     * configured in the boot method after the application has
     * been fully initialized.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Configures Fortify authentication features, including custom action
     * classes for user management, conditional authentication disabling,
     * and rate limiting for login and two-factor authentication attempts.
     */
    public function boot(): void
    {
        if (config('fortify.auth_disabled')) {
            Fortify::loginView(fn () => abort(403, 'Login is temporarily disabled.'));
            Fortify::registerView(fn () => abort(403, 'Registration is temporarily disabled.'));

            Route::post('/login', fn () => abort(403))->name('login');
            Route::post('/register', fn () => abort(403))->name('register');
        }

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
