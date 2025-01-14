<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() == 0) {
            $user = User::create([
                'name' => 'Từ Ngọc Vân',
                'email' => 'tungocvan@gmail.com',
                'password' => bcrypt('123456')
            ]);

            $role = Role::create(['name' => 'Admin']);

            $permissions = Permission::pluck('id','id')->all();

            $role->syncPermissions($permissions);

            $user->assignRole([$role->id]);
        }else {
            echo "Table already has data, skipping seeding.\n";
        }
    }
}
