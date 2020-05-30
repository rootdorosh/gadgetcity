<?php

namespace App\Modules\User\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Base\CoreHelper;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Permission Types
         *
         */
        $permissionItems = [];

        $skip = ['.', '..'];
        
        foreach (CoreHelper::getModules() as $module) {
            $permissionFile = app_path() . '/Modules/' . $module . '/Admin/Config/permissions.php';
            if (is_file($permissionFile)) {
                $data = require_once $permissionFile;
                foreach ($data['items'] as $item) {
                    foreach ($item['actions'] as $k => $v) {
                        $permissionItems[] = [
                            'name' => $k,
                            'slug' => $k,
                        ];
                    }
                }
            }
        }
 
        /*
         * Add Permission Items
         *
         */
        foreach ($permissionItems as $permissionItem) {
            $newPermissionitem = config('roles.models.permission')::where('slug', '=', $permissionItem['slug'])->first();
            if ($newPermissionitem === null) {
                $newPermissionitem = config('roles.models.permission')::create($permissionItem);
                echo "Add permission: " . $permissionItem['slug'] . "\n";
            }
        }
    }
}
