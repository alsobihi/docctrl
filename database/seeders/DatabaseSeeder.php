<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        if (App::environment('local')) {
            $this->call(DummyDataSeeder::class);
        }
    }
}
// Note: The DummyDataSeeder is only run in the local environment to avoid creating dummy data in production or staging environments.
