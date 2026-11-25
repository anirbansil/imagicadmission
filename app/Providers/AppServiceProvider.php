<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Artisan commands that drop every table in the database.
     */
    const DESTRUCTIVE_COMMANDS = ['migrate:fresh', 'migrate:refresh', 'migrate:reset', 'db:wipe'];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Most tables were imported from SQL, not created by migrations, so these
        // commands would destroy live data. Blocked in production (any letter case).
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            if (in_array($event->command, self::DESTRUCTIVE_COMMANDS) && strtolower(config('app.env')) === 'production') {
                throw new \RuntimeException($event->command.' is disabled in production: it would drop live tables. Use "php artisan migrate" to apply new migrations.');
            }
        });
    }
}
