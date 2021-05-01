<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; 

class CreateAdminUserSeeder extends Seeder

{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $user = User::create([
            'name' => 'Admin', 
            'email' => 'admin@gmail.com',
            'password' => bcrypt('dst6yfdnbf!y7hkui,fdg')
        ]);

        $role = Role::create(['name' => 'Admin', 'guard_name' => 'api']);
        $permissions = Permission::pluck('id','id')->all();


        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}