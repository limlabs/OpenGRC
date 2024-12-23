<?php

namespace App\Console\Commands;

use App\Http\Controllers\BundleController;
use App\Http\Controllers\InstallController;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opengrc:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install OpenGRC with default settings';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Installing OpenGRC...');

        InstallController::check_node($this);
        InstallController::check_npm($this);
        InstallController::runNpmBuild($this);
        InstallController::check_appkey($this);
        InstallController::create_database($this);
        $this->call('migrate');
        (new SettingsSeeder)->run();
        InstallController::create_admin_user($this);
        (new RolePermissionSeeder)->run();
        InstallController::set_general_settings($this);

        $this->info('OpenGRC has been installed successfully!');

    }
}
