<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ProvincesSeeder::class,
            CitiesSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
            PresentationSeeder::class,
            UsersRoleSeeder::class,
            PositionsSeeder::class
        ]);
    }

}
