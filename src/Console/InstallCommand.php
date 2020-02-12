<?php

namespace DigitalEquation\TeamworkDesk\Console;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teamwork-desk:install {--force : Force Teamwork Desk to install even it has been already installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Teamwork Desk scaffolding into the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->teamworkDeskAlreadyInstalled() && !$this->option('force')) {
            $this->line('Teamwork Desk is already installed for this project.');
        } else {
            $this->comment('Publishing Teamwork Desk Service Provider...');
            $this->callSilent('vendor:publish', ['--tag' => 'teamwork-desk-provider']);

            $this->comment('Publishing Teamwork Desk Database Migrations...');
            $this->callSilent('vendor:publish', ['--tag' => 'teamwork-desk-migrations']);

            $this->comment('Publishing Teamwork Desk Database Factories...');
            $this->callSilent('vendor:publish', ['--tag' => 'teamwork-desk-factories']);

            $this->comment('Publishing Teamwork Desk Configuration...');
            $this->callSilent('vendor:publish', ['--tag' => 'teamwork-desk-config']);

            $this->registerTeamworkDeskServiceProvider();

            $this->info('Teamwork Desk scaffolding installed successfully.');
        }
    }

    /**
     * Register the Teamwork Desk service provider in the application configuration file.
     */
    protected function registerTeamworkDeskServiceProvider(): void
    {
        $namespace = Str::replaceLast('\\', '', $this->getAppNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace . '\\Providers\\TeamworkDeskServiceProvider::class')) {
            return;
        }

        $lineEndingCount = [
            "\r\n" => substr_count($appConfig, "\r\n"),
            "\r" => substr_count($appConfig, "\r"),
            "\n" => substr_count($appConfig, "\n"),
        ];

        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class," . $eol,
            "{$namespace}\\Providers\EventServiceProvider::class," . $eol . "        {$namespace}\Providers\TeamworkDeskServiceProvider::class," . $eol,
            $appConfig
        ));

        file_put_contents(app_path('Providers/TeamworkDeskServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/TeamworkDeskServiceProvider.php'))
        ));
    }

    /**
     * Determine if SaaS is already installed.
     *
     * @return bool
     */
    protected function teamworkDeskAlreadyInstalled()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        return isset($composer['require']['digitalequation/teamwork-desk']);
    }
}