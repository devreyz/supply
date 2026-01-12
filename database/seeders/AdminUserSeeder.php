<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Adicionando permissões para o papel admin
        $permissions = [
            'view-dashboard',
            'manage-users',
           
        ];

        foreach ($permissions as $permission) {
            $permissionModel = Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($permissionModel);
        }

        // Criando o usuário admin
        $user = User::firstOrCreate([
            'name' => 'Reyzim',
            'email' => 'devreyz137@gmail.com',
            'password' => bcrypt('lost-137'),
        ]);
        $user->assignRole($adminRole);
        $user2 = User::firstOrCreate([
            'name' => 'Reyzim',
            'email' => 'josereisleite2016@gmail.com',
            'password' => bcrypt('lost-137'),
        ]);
        $user2->assignRole($adminRole);
        $user3 = User::firstOrCreate([
            'name' => 'Reyzim',
            'email' => 'reysilver901@gmail.com',
            'password' => bcrypt('lost-137'),
        ]);
        $user3->assignRole($adminRole);
    }
}
