<?php

use Illuminate\Database\Seeder;
use App\Modules\User\Database\Seeds\PermissionsTableSeeder;
use App\Modules\User\Database\Seeds\RolesTableSeeder;
use App\Modules\User\Database\Seeds\UsersTableSeeder;
use App\Modules\User\Database\Seeds\ConnectRelationshipsSeeder;

class InstallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $this->call(PermissionsTableSeeder::class);
       $this->call(RolesTableSeeder::class);
       $this->call(UsersTableSeeder::class);
       $this->call(ConnectRelationshipsSeeder::class);
    }
}
