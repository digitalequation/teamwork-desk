<?php

namespace DigitalEquation\TeamworkDesk;

use Carbon\Carbon;
use DigitalEquation\TeamworkDesk\Console\{ConfigCommand, FactoriesCommand, InstallCommand, MigrationsCommand};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TeamworkDeskServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('teamwork-desk.enabled')) {
            return;
        }

        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerPublishing();
        $this->registerCommands();
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
            'Contracts\Repositories\TicketRepository' => 'Repositories\TicketRepository'
        ];

        foreach ($services as $key => $value) {
            $this->app->singleton('DigitalEquation\TeamworkDesk\\' . $key, 'DigitalEquation\TeamworkDesk\\' . $value);
        }
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeApiConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });

        Route::group($this->routeWebConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Get the Teamwork Desk [api] route group configuration array.
     */
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

    /**
     * Get the Teamwork Desk [web] route group configuration array.
     */
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

    /**
     * Register the package migrations.
     */
    private function registerMigrations(): void
    {
        if ($this->app->runningInConsole() && TeamworkDesk::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Register the package artisan commands.
     */
    private function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
            ConfigCommand::class,
            MigrationsCommand::class,
            FactoriesCommand::class
        ]);
    }

    /**
     * Register the package's publishable resources.
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'teamwork-desk-migrations');

            $this->publishes([
                __DIR__ . '/../database/factories' => database_path('factories'),
            ], 'teamwork-desk-factories');

            $this->publishes([
                __DIR__ . '/../config/teamwork-desk.php' => config_path('teamwork-desk.php'),
            ], 'teamwork-desk-config');

            $this->publishes([
                __DIR__ . '/../stubs/app/Providers/TeamworkDeskServiceProvider.stub' => app_path('Providers/TeamworkDeskServiceProvider.php'),
            ], 'teamwork-desk-provider');
        }
    }
}
