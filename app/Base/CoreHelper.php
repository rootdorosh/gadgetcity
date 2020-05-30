<?php

namespace App\Base;

use Illuminate\Support\Arr;
use Cache;

/**
 * Class CoreHelper.
 */
class CoreHelper
{
    const TAG = 'core';
    
    /**
     * Get modules
     *
     * @return array
     */
    public static function getModules() : array
    {
        return Cache::tags(self::TAG)->remember(self::TAG . '_getModules_', 60*60*24, function() {
            $skip = ['.', '..'];
            $modules = [];

            $path = app_path() . '/Modules';
            $files = scandir($path);
            foreach ($files as $module) {
                if (!in_array($module, $skip) && is_dir($path . '/' . $module)) {
                    $modules[] = $module;
                }
            }
            
            return $modules;
        });          
    }
              
     /**
     * Get side menu
     *
     * @return array
     */
    public static function getMenu() : array
    {
        return Cache::tags(self::TAG)->remember(self::TAG . '_getMenu_', 60*60*24, function() {
            $items = [];

            foreach (self::getModules() as $module) {
                $file = app_path() . '/Modules/' . $module . '/Admin/Config/menu.php';
                
                preg_match('/app\/Modules\/(.*?)\/Admin\/Config/', $file, $match);
                
                if (is_file($file)) {
                    $itemFile = require $file;
                    //$itemFile[0]['module'] = $match[1];
                    $items = array_merge($items, $itemFile);
                }
            }
            
            $items = array_values(Arr::sort($items, function ($value) {
                return $value['rank'];
            }));        
            
            return $items;
        });   
    }
   
    
}
