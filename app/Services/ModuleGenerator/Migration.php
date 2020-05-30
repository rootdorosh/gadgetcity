<?php

namespace App\Services\ModuleGenerator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use DB;

/**
 * class Migration
 */
class Migration extends Base
{
    /*
     * @var Migration
     */
    protected $gs;
        
    /*
     * @param ModuleGeneratorService  $gs
     * @param array $model
     */
    public function __construct(ModuleGeneratorService $gs)
    {
        $this->gs = $gs;
    }

    public function drop()
    {
        foreach ($this->getDataFiles() as $item) {
            preg_match('/dropIfExists\(\'(.*?)\'\)/', file_get_contents($item['path']), $match);
            if (isset($match[1])) {
                Schema::dropIfExists($match[1]);
                echo "Table " . $match[1] . " droped \n";
                
                DB::table('migrations')->where('migration', $item['migration'])->delete();
            }
        }
    }
    
    /*
     * @return array
     */
    public function getDataFiles(): array
    {
        $files = is_dir($this->gs->getMigratePath()) ? glob($this->gs->getMigratePath(). '/*') : [];
        
        usort($files, function($a, $b) {
            return strcmp($b, $a);
        });
        
        return array_map(function($value) {
            $file = Str::afterLast($value, '/');
            $data = [
                'path' => $value,
                'file' => $file,
                'migration' => Str::beforeLast($file, '.php'),
            ];
            return $data;
        }, $files);
    }
}