<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

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
    protected $description = 'Install OpenGRC';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //copy .env.example to .env
        if (!file_exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        //Choose a database install option
        $db_driver = select (
            label: 'Choose a database driver',
            options: ['mysql', 'sqlite', 'pgsql'],
            default: 'sqlite',
            hint: 'SQLite is the simplest option for most users'
        );

        if ($db_driver == 'mysql') {
            $db_host = $this->ask('Enter the database host', '127.0.0.1');
            $db_port = $this->ask('Enter the database port', '3306');
            $db_database = $this->ask('Enter the database name', 'opengrc');
            $db_username = $this->ask('Enter the database username', 'root');
            $db_password = $this->secret('Enter the database password');
        } elseif ($db_driver == 'pgsql') {
            $db_host = $this->ask('Enter the database host', '127.0.0.1');
            $db_port = $this->ask('Enter the database port', '5432');
            $db_database = $this->ask('Enter the database name', 'opengrc');
            $db_username = $this->ask('Enter the database username', 'postgres');
            $db_password = $this->secret('Enter the database password');
        } elseif ($db_driver == 'sqlite') {
            $db_database = database_path('opengrc.sqlite');
            $db_host = '';
            $db_port = '';
            $db_username = '';
            $db_password = '';
        }

        //generate app key
        $this->info('Generating application security key');
        $this->call('key:generate');

        // Update .env with database details
        $this->info('Updating the .env file');
        $this->updateEnv([
            'DB_CONNECTION' => $db_driver,
            'DB_HOST' => $db_host,
            'DB_PORT' => $db_port,
            'DB_DATABASE' => $db_database,
            'DB_USERNAME' => $db_username,
            'DB_PASSWORD' => $db_password,
        ]);

        //if sqlite, if the db exists, remove it
        if ($db_driver == 'sqlite') {
            if (file_exists($db_database)) {
                $this->info('Removing existing SQLite database');
                unlink($db_database);
            }
            touch($db_database);
        }

        //reload the .env
        $this->call('config:clear');

        // Run the migrations
        $this->info('Creating database tables');
        $this->call('migrate');

        // Prompt for Admin user and create
        $email = text(
            label: 'Enter the Email Address for the Admin user',
            default: "admin@example.com",
            hint: 'This will also be the username for the Admin user'
        );
        $password = password(
            label: 'What is your password?',
            placeholder: 'password',
            required: true,
            hint: 'Minimum 8 characters.',
        );
        $this->call('opengrc:create-user', [
            'email' => $email,
            'password' =>$password
        ]);

        // Call Settings and Role/Perms seeders
        $this->info('Seeding the database with defaults');
        $this->call('db:seed', ['--class' => 'SettingsSeeder']);
        $this->call('db:seed', ['--class' => 'RolePermissionSeeder']);

        $site_name = text(
            label: 'Enter the Site Name',
            default: "OpenGRC",
            required: true,
            hint: 'This will be displayed in the header of the site'
        );

        $site_url = text(
            label: 'Enter the Site URL',
            default: "https://opengrc.test",
            required: true,
            hint: 'This will be used in emails and other places'
        );

        $this->call('settings:set', [
            'key' => 'general.name',
            'value'=> $site_name]);
        $this->call('settings:set', [
            'key' => 'general.url',
            'value' => $site_url
        ]);

        $this->updateEnv([
            'APP_NAME' => $site_name,
            'APP_URL' => $site_url,
        ]);

        //run npm install and build
        $this->info('Building Front-End assets');
        exec('npm install && npm run build');

        //set starter filesystem permissions
        $this->info('Setting filesystem permissions');
        exec('find . -type f -print0 | xargs --null sudo chmod 666');
        exec('find . -type d -print0 | xargs --null sudo chmod 775');
        exec('sudo chmod 777 set_permissions');
        exec('sudo chmod 777 artisan');
        exec('sudo chmod 777 artisan');
        exec('sudo chmod 777 install.sh');
        exec('sudo chmod 777 vendor/bin/*');
        exec('sudo chmod 777 storage -R');
        exec('sudo chmod 777 database');
        exec('sudo chmod 777 database/opengrc.sqlite');
        exec('sudo chmod 777 node_modules/.bin/*');

        $this->warn("Change the file system permissions for least privilege based on you own system.");

        $this->info('########################################');
        $this->info('OpenGRC has been installed successfully!');
        $this->info('########################################');

    }

    /**
     * Update the .env file with the given key-value pairs.
     *
     * @param array $data
     */
    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        }

        file_put_contents($envPath, $envContent);
    }
}
