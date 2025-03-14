<?php

namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Schema;
use App\Models\Audit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable mass assignment protection
        Model::unguard();
        if (! $this->app->environment('local')) {
            URL::forceScheme('https');
        }

        // if table "settings" exists
        if (! app()->runningInConsole()) {
            if (Schema::hasTable('settings')) {

                Config::set('app.name', setting('general.name', 'OpenGRC'));
                Config::set('app.url', setting('general.url', 'https://opengrc.test'));

                $mailConfig = [
                    'host' => setting('mail.host'),
                    'username' => setting('mail.username'),
                    'password' => setting('mail.password'),
                    'encryption' => setting('mail.encryption'),
                    'port' => setting('mail.port'),
                    'from' => [
                        'address' => setting('mail.from'),
                        'name' => setting('general.name'),
                    ],
                ];
                
                // Only set transport if it's not already set or if we have a setting for it
                if (setting('mail.mailer', null) !== null) {
                    $mailConfig['transport'] = setting('mail.mailer');
                }
                
                config()->set('mail', array_merge(config('mail'), $mailConfig));

                // Set session lifetime from settings
                Config::set('session.lifetime', setting('security.session_timeout', 15));
            } else {
                // if table "settings" does not exist
                // Error that app was not installed properly
                abort(500, "OpenGRC was not installed properly. Please review the
                installation guide at https://docs.opengrc.com to install the app.");
            }
        }

        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        FilamentColor::register([
            'bg-grcblue' => [
                50 => '#eaf3f7',
                100 => '#d4e7ef',
                200 => '#a9cfe0',
                300 => '#7eb7d1',
                400 => '#1375a0',
                500 => '#106689',
                600 => '#0d5773',
                700 => '#0a485d',
                800 => '#374151',
                900 => '#212a3a',
            ],
            'danger' => [
                50 => '254, 242, 242',
                100 => '254, 226, 226',
                200 => '254, 202, 202',
                300 => '252, 165, 165',
                400 => '248, 113, 113',
                500 => '239, 68, 68',
                600 => '220, 38, 38',
                700 => '185, 28, 28',
                800 => '153, 27, 27',
                900 => '127, 29, 29',
                950 => '69, 10, 10',
            ],
        ]);

    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
