<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Jesus Miguel',
                'lastname' => 'Rogliero Colmenares',
                'email' => 'admin@api.com',
                'password' => Hash::make('Jesus24may99!')
            ],

            [
                'name' => 'Patricia',
                'lastname' => 'Rogliero Colmenares',
                'email' => 'admin1@api.com',
                'password' => Hash::make('Jesus24may99!')
            ],

        ];


        foreach ($users as $user) {
            User::firstOrCreate($user, $user);
        }
    }
}
