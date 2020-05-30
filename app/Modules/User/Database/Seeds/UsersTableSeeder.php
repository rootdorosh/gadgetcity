<?php

namespace App\Modules\User\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Modules\User\Models\{
    User,
    Role,
    Permission
};

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('name', '=', 'Admin')->first();
        $permissions = Permission::all();
        
        $items = [
            [
                'email' => 'vasyldorosh@gmail.com',
                'password' => 'dorosh123',
                'name' => 'VD',
            ],
        ];
        
        foreach ($items as $item) {
            if (User::where('email', '=', $item['email'])->first() === null) {
                $item['is_active'] = 1;
                $user = User::create($item);
                $user->roles()->sync([$adminRole->id]);
            }
        }        
    }
}
