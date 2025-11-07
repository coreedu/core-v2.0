<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permissões
        $permissionsSuperAd = [
            'ver dashboard',
            'ver usuários',
            'criar usuários',
            'editar usuários',
            'deletar usuários',
            'ver cursos',
            'criar cursos',
            'editar cursos',
            'deletar cursos',
        ];

        // Cria permissões
        foreach ($permissionsSuperAd as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Cria o papel 'admin' e associa permissões
        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole->syncPermissions($permissionsSuperAd); // atribui todas

        // Cria ou atualiza o usuário admin e atribui o papel
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'rm' => '000000001',
                'name' => 'Administrador',
                'password' => bcrypt('senha123'), // defina uma senha
            ]
        );

        $adminUser->assignRole($adminRole);
    }
}
