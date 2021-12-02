<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Presentation;
use App\Models\User;
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
        $this->createNewUsers();
        $this->createNewRoles();
        $this->createNewPresentations();
    }


    public function createNewUsers() {

        $users = [
            [
                'name' => 'Jesus Miguel',
                'lastname' => 'Rogliero Colmenares',
                'email' => 'admin@api.com',
                'password' => Hash::make('Jesus24may99!')
            ]
        ];


        foreach ($users as $user) {
            User::firstOrCreate($user, $user);
        }
    }

    //roles de usuarios
    public function createNewRoles() {
        $roles = [
            ['name' => 'Gerente'],
            ['name' => 'administrador'],
            ['name' => 'Cordinador de Almacen'],
            ['name' => 'Cordinador de Produccion'],
            ['name' => 'Cordinador de Administracion']
        ];
       
        foreach ($roles as $role) {
            Role::firstOrCreate($role, $role);
        }
    }

    //presentaciones de los productos en el inventario
    public function createNewPresentations() {
        $presentations = [
            ['name' => 'GAL'],
            ['name' => 'UNID'],
            ['name' => 'MTS'],
            ['name' => 'LTS']
        ];
        
        foreach ($presentations as $presentation) {
            Presentation::firstOrCreate($presentation, $presentation);
        }
    }
}
