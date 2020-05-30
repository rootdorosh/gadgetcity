<?php

namespace App\Services\ModuleGenerator;

use Illuminate\Support\Str;

/**
 * class Base
 */
class Base
{
    const ENTER = "\n";
    const TAB2 = '  ';
    const TAB4 = '    ';
    const TAB6 = '      ';
    const TAB8 = '        ';
    const TAB10 = '         ';
    const TAB12 = '           ';
    const TAB14 = '             ';
    const TAB16 = '               ';

    /*
     * @var ModuleGeneratorService
     */
    protected $gs;
    
    /*
     * @var array
     */
    protected $model;
    
    /*
     * @param ModuleGeneratorService  $gs
     * @param  array $model
     */
    public function __construct(ModuleGeneratorService $gs, array $model)
    {
        $this->gs = $gs;
        $this->model = $model;
    }
  
    /*
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->gs->config['module']['name'];
    }
    
    /*
     * @return string|null
     */
    public function getModelName():? string
    {
        $modelName = null;
            if (!empty($this->model['name'])) {
            $modelName = $this->model['name'];

            if (!empty($this->model['parentModel'])) {
                $modelName = $this->model['parentModel'] . $modelName;
            }
        }
        
        return $modelName;
    }
    
    /*
     * @return array
     */
    public function getNamespaceModels(): array
    {
        $data = [];
        
        if (!empty($this->model['parentModel'])) {
            $data = [
                'App\Modules\\' . $this->getModuleName() . '\Models\\' . $this->model['parentModel'],                
                'App\Modules\\' . $this->getModuleName() . '\Models\\' . $this->model['parentModel'] . '\\' . $this->model['name'],                
            ];           
        } else {
            $data = [
                'App\Modules\\' . $this->getModuleName() . '\Models\\' . $this->model['name'],
            ];
        }
        
        return $data;
    }
    
    /*
     * @return array
     */
    public function getConfigParentModel(): array
    {
        $path = resource_path() . '/modules/' . Str::camel($this->getModuleName()) 
            . '/models/' . Str::camel($this->model['parentModel']) . '.php';
        
        return is_file($path) ? require($path) : [];
    }

    /*
     * @return array
     */
    public function getChildrenModels(): array
    {
        $path = resource_path() . '/modules/' . Str::camel($this->getModuleName()) . '/models/*';
        $files = glob($path);
        
        $data = [];
        foreach ($files as $file) {
            $conf = require($file);
            if (!empty($conf['parentModel']) && $conf['parentModel'] === $this->model['name']) {
                $data[$conf['name']] = $this->gs->getExrendedModelConfig($conf);
            }
        }
        
        return $data;
    }
    
    /*
     * @return string
     */
    public function getStorageResponsePath(): string
    {
        $path =  Str::snake($this->getModuleName());
        
        if (!empty($this->model['parentModel'])) {
            $path.= '/' . strtolower(Str::snake(Str::plural($this->model['parentModel'])));
        }
        $path.= '/' . strtolower(Str::snake($this->model['name_plural']));
        
        return $path;
    }
    
    /*
     * @return array
     */
    public function getAdaptiveImages()
    {
        $data = [];
        
        foreach ($this->model['fields'] as $attr => $item) {
            if ($item['uiType'] === 'AdaptiveImage') {
                $data[$attr] = $item;
            }
        }
        
        return $data;
    }
    
    
}