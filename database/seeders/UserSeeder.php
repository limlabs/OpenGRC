<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Manually create the first user
        User::create([
            'name' => 'Lee Mangold',
            'email' => 'lmangold@outlook.com',
            'email_verified_at' => now(),
            'password' => bcrypt('lee5882'), // Choose a default password
        ]);

        User::create([
            'name' => 'Ashley Archibald',
            'email' => 'asharris89@outlook.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // Choose a default password
        ]);

        User::create([
            'name' => 'Jake Harris',
            'email' => 'jrharris131@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('EatADickJake69!'), // Choose a default password
        ]);

        User::create([
            'name' => 'Jace Powell',
            'email' => 'jace@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // Choose a default password
        ]);


        // Use the factory to create 5 more users
        User::factory()->count(5)->create();
    }
}
