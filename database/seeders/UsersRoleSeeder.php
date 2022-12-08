<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UsersRole;

class UsersRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users_role = [
            [ "user_id" => 1, "role_id" => 1 ],
            [ "user_id" => 2, "role_id" => 2 ],
            [ "user_id" => 3, "role_id" => 1 ],
        ];

        foreach ($users_role as $user_role) {
            UsersRole::firstOrCreate($user_role, $user_role);
        }
    }
}
