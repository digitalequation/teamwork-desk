<?php

namespace DigitalEquation\TeamworkDesk;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TeamworkDeskServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('teamwork-desk.enabled')) {
            return;
        }

        if ($this->app->runningInConsole()) {

            // Register routes
            Route::group($this->routeApiConfiguration(), function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });

            Route::group($this->routeWebConfiguration(), function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });

            // Publish config file
            $this->publishes([
                __DIR__ . '/../config/teamwork-desk.php' => config_path('teamwork-desk.php'),
            ], 'config');

            // Publish migration files
            if (
                !$this->migrationFileExists('users_add_customer_support_id.php')
                && !$this->migrationFileExists('create_support_tickets_table.php')
            ) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/users_add_customer_support_id.stub' => database_path(sprintf(
                        'migrations/%s_users_add_customer_support_id.php',
                        date('Y_m_d_His')
                    )),

                    __DIR__ . '/../database/migrations/create_support_tickets_table.stub' => database_path(sprintf(
                        'migrations/%s_create_support_tickets_table.php',
                        date('Y_m_d_His')
                    )),
                ], 'migrations');
            }
        }
    }

    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/teamwork-desk.php', 'teamwork-desk');

        // Register the main class to use with the facade
        $this->app->singleton('teamwork-desk', function () {
            return new TeamworkDesk;
        });

        // Register tickets service
        $services = [
            'Contracts\Repositories\TicketRepository' => 'Repositories\TicketRepository',
        ];

        foreach ($services as $key => $value) {
            $this->app->singleton('DigitalEquation\TeamworkDesk\\' . $key, 'DigitalEquation\TeamworkDesk\\' . $value);
        }
    }

    protected function routeApiConfiguration(): array
    {
        return [
            'namespace'  => 'DigitalEquation\TeamworkDesk\Http\Controllers\API',
            'domain'     => config('teamwork-desk.route_group.api.domain', null),
            'as'         => config('teamwork-desk.route_group.api.as', 'api.'),
            'prefix'     => config('teamwork-desk.route_group.api.prefix', 'api'),
            'middleware' => config('teamwork-desk.route_group.api.middleware', ['api', 'auth:api']),
        ];
    }

    protected function routeWebConfiguration(): array
    {
        return [
            'namespace'  => 'DigitalEquation\TeamworkDesk\Http\Controllers',
            'domain'     => config('teamwork-desk.route_group.web.domain', null),
            'as'         => config('teamwork-desk.route_group.web.as', null),
            'prefix'     => config('teamwork-desk.route_group.web.prefix', '/'),
            'middleware' => config('teamwork-desk.route_group.web.middleware', 'web'),
        ];
    }

    private function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
