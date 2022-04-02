<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'Gerente'],
            ['name' => 'Administrador'],
            ['name' => 'Cordinador de Almacen'],
            ['name' => 'Cordinador de Produccion'],
            ['name' => 'Cordinador de Administracion']
        ];
       
        foreach ($roles as $role) {
            Role::firstOrCreate($role, $role);
        }
    }
}
