<?php

namespace App\Http\Controllers;

use App\Models\User;
use Artisan;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Process\Process;

class InstallController extends Controller
{
    public static function check_node($command): bool
    {

        $nodeVersion = trim(shell_exec('node -v | cut -c 2-'));

        if (version_compare($nodeVersion, '16', '<')) {
            $command->info('Checking Node.js version...');
            $command->error("[x] Checking Node.js version... FAILED! Node.js version 16 or higher is required. You have $nodeVersion.");

            return false;
        } else {
            $command->info('[✓] Checking Node.js version... GOOD!');
        }

        return true;
    }

    public static function check_npm($command): bool
    {
        $npmVersion = trim(shell_exec('npm -v'));

        if (version_compare($npmVersion, '9', '<')) {
            $command->info('Checking NPM version...');
            $command->error("[x] Checking NPM version... FAILED! NPM version 9 or higher is required. You have $npmVersion.");

            return false;
        } else {
            $command->info('[✓] Checking NPM version... GOOD!');
        }

        return true;
    }

    public static function check_appkey($command): bool
    {

        if (empty(config('app.key'))) {
            $command->info('[✓] Generating application key.');
            Artisan::call('key:generate');

            return true;
        } else {
            $command->info('[✓] Application key already exists. Good to go.');
        }

        return true;
    }

    public static function create_database($command): bool
    {
        $command->info('Available database backends:');
        $choice = $command->choice(
            'Select your preferred database backend',
            ['sqlite', 'mysql', 'postgres'],
            0 // default
        );
        $command->info("You've selected: $choice");
        if ($choice !== 'sqlite') {
            $dbHost = $command->ask('Enter database host', '127.0.0.1');
            $dbPort = $command->ask('Enter database port', '3306');
            $dbName = $command->ask('Enter database name', 'opengrc');
            $dbUser = $command->ask('Enter database username', 'root');
            $dbPassword = $command->secret('Enter database password');

            // Update .env with credentials
            $envUpdates = [
                "DB_CONNECTION=$choice",
                "DB_HOST=$dbHost",
                "DB_PORT=$dbPort",
                "DB_DATABASE=$dbName",
                "DB_USERNAME=$dbUser",
                "DB_PASSWORD=$dbPassword",
            ];

            foreach ($envUpdates as $update) {
                // Extract the key from the $update string
                $key = explode('=', $update)[0];

                $process = new Process(['sed', '-i', "s/^$key.*/$update/g", '.env']);
                $process->run();

                if (! $process->isSuccessful()) {
                    $command->error("[X] Failed to update .env file with: $update");

                    return false;
                }
            }
        } else {
            // Update .env with DB_CONNECTION only for sqlite
            $database_path = addcslashes(getcwd().'/database/opengrc.sqlite', '/.');
            $process = new Process(['sed', '-i', "s/DB_CONNECTION=.*/DB_CONNECTION=$choice/g", '.env']);
            $process->run();
            $process = new Process(['sed', '-i', "s/DB_DATABASE=.*/DB_DATABASE=$database_path/g", '.env']);
            $process->run();

            if (! $process->isSuccessful()) {
                $command->error('[X] Failed to update .env file.');
                $command->error($process->getErrorOutput());

                return false;
            }
        }

        $command->info('[✓] Updated .env file...');

        return true;
    }

    public static function runNpmBuild($command): bool
    {
        $command->info('[✓] Building Front-End Assets...');
        $process = new Process(['npm', 'run', 'build']);
        $process->run();

        if (! $process->isSuccessful()) {
            $command->error('[X] Failed to build assets');
            $command->error($process->getErrorOutput());

            return false;
        }

        return true;
    }

    public static function create_admin_user($command)
    {
        $username = $command->ask('Admin Username', 'admin@example.com');
        $password = $command->secret('Admin Password', 'password');

        $user = User::create([
            'name' => 'Admin User',
            'email' => $username,
            'password' => Hash::make($password),
            'password_reset_required' => false,

        ])->save();
        $command->info('[✓] Admin User Created...');

    }

    public static function set_general_settings($command)
    {
        $site_name = addcslashes($command->ask('Site Name', 'OpenGRC'), ' -');
        $site_url = addcslashes($command->ask('Site URL', 'https://opengrc.test'), ':/.-');

        setting([
            "general.url" => $site_url,
            "general.name" => $site_name,
        ]);

        $escaped_site_name = addcslashes($site_name, ' -');
        $escaped_site_url = addcslashes($site_url, ':/.-');

        $process = new Process(['sed', '-i', "s/APPNAME=.*/APPNAME=$escaped_site_name/g", '.env']);
        $process->run();
        $process = new Process(['sed', '-i', "s/APP_URL=.*/APP_URL=$escaped_site_url/g", '.env']);
        $process->run();

    }
}
