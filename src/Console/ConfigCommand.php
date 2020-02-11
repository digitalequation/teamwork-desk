<?php

namespace DigitalEquation\TeamworkDesk\Console;

use Illuminate\Console\Command;

class ConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teamwork-desk:config {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-publish the Teamwork Desk config file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'teamwork-desk-config',
            '--force' => $this->option('force'),
        ]);
    }
}