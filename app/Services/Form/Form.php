<?php

namespace App\Services\Form;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Form
{
    /**
     *
     * @var Illuminate\Database\Eloquent
     */
    public $model;

    /**
     * The form id.
     *
     * @var string
     */
    protected $id;

    /**
     * The form method.
     *
     * @var string
     */
    public $method;

    /**
     * The form method.
     *
     * @var string
     */
    public $tab;

    /**
     * The model relation map.
     *
     * @var array
     */
    public $relations = [];

    /**
     * Html tag form not draw
     *
     * @var bool
     */
    public $hideForm = false;

    /**
     * The form action.
     *
     * @var string
     */
    public $action;

    /**
     * The form class attribute.
     *
     * @var string
     */
    public $formClass = '';

    /**
     * The field wrap attribute.
     *
     * @var string
     */
    public $groupClass = 'form-group';

    /**
     * The buttons group class.
     *
     * @var string
     */
    public $btnGroupClass = '';

    /**
     * The field class.
     *
     * @var string
     */
    public $fieldClass = 'form-control';

    /**
     * Addts form attributes.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Cards
     *
     * @var array
     */
    public $tabs = [];


    /**
     * wrappers.
     *
     * @var array
     */
    public $wrappers = [];

    /**
     * @var array
     */
    public $ajaxifyPanel = [];

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var array
     */
    public $buttons = [];

    /**
     * @var array
     */
    public $labelConfig = [];

    /**
     * @var array
     */
    public $valueMap = [];

    /**
     * @var array
     */
    public $defaultValueMap = [];

    /**
     * Create a new form.
     *
     * @param  array  $params
     *
     * @param  \Closure|null  $callback
     * @return void
     */
    public function __construct(array $params, Closure $callback = null)
    {
        foreach ($params as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }

        if (! is_null($callback)) {
            $callback($this);
        }
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id ?? Str::random(32);
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     * @param  array   $params
     *
     * @return self
     */
    public function select(string $name, array $options, array $params = []) : self
    {
        //dd($params);
        
        $this->resolveFieldConfig($name, $params);

        $this->addField('select', [
                'name' => !empty($params['multiple']) ? "{$name}[]" : $name,
                'options' => $options,
            ] + $params);
       
        return $this;
    }

    /**
     *
     * @param  string  $name
     * @param  array   $params
     *
     * @return self
     */
    public function image(string $name, array $params = []) : self
    {
        $this->resolveFieldConfig($name, $params);

        $this->addField('image', [
            'name' => $name,
        ] + $params);

        return $this;
    }

    /**
     *
     * @param  string  $type
     * @param  string  $name
     * @param  array   $params
     *
     * @return self
     */
    public function rangeFromTo(string $type, string $name, array $params) : self
    {
        $params['range'] = true;
        $this->resolveFieldConfig($name, $params);

        $this->addFieldRangeFromTo($type, [
            'name' => $name,
        ] + $params);

        return $this;
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     * @param  array   $params
     *
     * @return self
     */
    public function toggle(string $name, array $options = [], array $params = []) : self
    {
        if (empty($options)) {
            $options = [
                0 => __('app.no'),
                1 => __('app.yes'),
            ];
        }
        if (empty($params['value']) &&
            empty($params['default']) &&
            array_key_exists(0, $options) &&
            count($options) === 2
        ) {
            $params['value'] = 0;
        }
        $params['name'] = $name;
        $params['options'] = $options;
        $this->resolveFieldConfig($name, $params);
        $this->addField('toggle', $params);
        return $this;
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     * @param  array   $params
     *
     * @return self
     */
    public function doubleToggle(string $name, array $options = [], array $params = []) : self
    {
        if (empty($options)) {
            $options = [
                0 => __('app.no'),
                1 => __('app.yes'),
            ];
        }
        
        $optionKeys = array_keys($options);
        
        $default = array_key_exists('default', $params) ? $params['default'] : '';
        if (empty($params['value']) && in_array($default, $optionKeys)) {
            $params['value'] = $default;
        } elseif (empty($params['value']) && empty($params['default'])) {
            $params['value'] = $optionKeys[0];
        }
               
        $params['name'] = $name;
        $params['options'] = $options;
        $this->resolveFieldConfig($name, $params);
        $this->addField('doubleToggle', $params);
        
        return $this;
    }

    /**
     *
     * @param  string  $type
     * @param  string  $name
     * @param  array   $options
     *
     * @return self
     */
    public function input(string $type, string $name, array $options = []) : self
    {
        $this->resolveFieldConfig($name, $options);

        $this->addField('input', [
                'type' => $type,
                'name' => $name,
            ] + $options);

        return $this;
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return self
     */
    public function text(string $name, array $options = []) : self
    {
        return $this->input('text', $name, $options);
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return self
     */
    public function hidden(string $name, array $options = []) : self
    {
        return $this->input('hidden', $name, $options);
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return self
     */
    public function password(string $name, array $options = []) : self
    {
        return $this->input('password', $name, $options);
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return self
     */
    public function number(string $name, array $options = []) : self
    {
        return $this->input('number', $name, $options);
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return self
     */
    public function email(string $name, array $options = []) : self
    {
        return $this->input('email', $name, $options);
    }

    /**
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return self
     */
    public function textarea(string $name, array $options = []) : self
    {
        $this->resolveFieldConfig($name, $options);

        $this->addField('textarea', [
                'name' => $name,
            ] + $options);

        return $this;
    }

    /**
     * @param  string $name
     *
     * @return string
     */
    public function resolveName(string $name) : string
    {
        $name = str_replace(['[', ']'], '', $name);

        return $name;
    }

    /**
     * @param  string $name
     *
     * @return string
     */
    public function resolveI18nKey(string $name) : string
    {
        $key = Str::snake($this->getModuleKey()) . '::' . Str::snake($this->getModelKey()) . '.fields.';
        
        preg_match("/^([a-z\_]+)?\[([a-z\_]+)?\]/i", $name, $matches);
        
        if (!count($matches)) {
            $key .= $name;
        } elseif (count($matches) === 2) {
            $key .= $matches[1];
        } elseif (count($matches) === 3 && in_array($matches[1], config('translatable.locales'))) {
            $key = $key . $matches[2];
        } elseif (count($matches) === 3) {
            $key .= $matches[1] . '.' . $matches[2];
        }
        
        $key = str_replace(['[', ']'], ['.', ''], $key);
        
        return $key;
    }

    /**
     * @param  string $name
     *
     * @return string
     */
    public function label(string $name) : string
    {
        $key = $this->resolveI18nKey($name, '.');
        
        return $this->labelConfig[$name]['title'] ?? __($key);
    }

    /**
     * @param  string $name
     *
     * @return string
     */
    public function hint(string $name) : string
    {
        $key = $this->resolveI18nKey($name, '.');
        
        return trans($key . '_helper');
    }

    /**
     * @param  string $name
     *
     * @return string
     */
    public function resolveNameForValue(string $name) : string
    {
        preg_match("/^([a-z\_]+)?\[([a-z\_]+)?\]/i", $name, $matches);
        if (count($matches) === 2) {
            $name = $matches[1];
        }

        return $name;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function nameToDot(string $name)
    {
        $value = str_replace(['[', ']'], ['.', ''], $name);
        return $value;
    }    
    
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function value(string $name)
    {
        //dd($this->relations);
        //dump($this->model->getRelationData('seo'));
        //dd($this->model->getRelationData('banners'));
        
        $old = old();
        
        $dotName = self::nameToDot($name);
        $oldDotValues = !empty($old) ? Arr::dot($old) : [];
        
        // value from input form
        if (array_key_exists($dotName, $oldDotValues)) {
            return $oldDotValues[$dotName];
        
        // value from db    
        } else {
            // seo[1][title]
            if (preg_match("/^([a-z\_]+)?\[([0-9]+)?\]([a-z0-9]+)?/i", $name, $matches) && 
                !empty($matches[1]) && 
                in_array($matches[1], $this->relations)
            ) {
                $relDoted = Arr::dot($this->model->getRelationData($matches[1]));
                $dotName = str_replace($matches[1] . '.', '', $dotName);
                
                if (array_key_exists($dotName, $relDoted)) {
                    return $relDoted[$dotName];
                }  
                
            } elseif (preg_match("/^([a-z\_]+)?\[([a-z\_]+)?\]/i", $name, $matches) && 
                    !empty($matches[2]) && 
                    in_array($matches[1], config('translatable.locales')) &&
                    !empty($this->model->id)
            ) {
                $data = $this->model->translations()->where('locale', $matches[1])->first()->toArray();
                if (!empty($data[$matches[2]])) {
                    return $data[$matches[2]];
                }
                
            } elseif (preg_match("/^([a-z0-9]+)?/i", $name, $matches)) {
                $name = str_replace('[]', '', $name);
                
                if (!empty($this->model->{$name}) && $this->model->{$name} instanceof EloquentCollection) {
                    return $this->model->{$name}->pluck('id')->toArray();
                } else {
                    return $this->model->{$name};
                }
            }
        }
        
        /*
        $name = $this->resolveNameForValue($name);
        if (array_key_exists($name, old()) && old()[$name] !== null) {
            return $this->resolveType(old($name));
        }

        $name = str_replace('[]', '', $name);

        $value = $this->model->{$name};

        if ($value === null) {
            if (isset($this->valueMap[$name]) && $this->valueMap[$name] !== null) {
                $value = $this->valueMap[$name];
            } elseif (isset($this->defaultValueMap[$name]) && $this->defaultValueMap[$name] !== null) {
                $value = $this->defaultValueMap[$name];
            }
        }

        return $this->resolveType($value);
        */
    }

    /*
     * @param mixed $value
     * @return mixed
     */
    public function resolveType($value)
    {
        if ($value instanceof Collection) {
            return $value->pluck('id')->toArray();
        }
        
        $valueInt = $value;
        $valueFloat = $value;
        
        $valueInt = (int) $valueInt;
        $valueFloat = (float) $valueFloat;
        
        if ($value === null) {
            //return $value;
        } elseif ((string)$valueInt == $value) {
            return $valueInt;
        } elseif ((string)$valueFloat == $value) {
            return $valueFloat;
        } else {
            return $value;
        }
    }

    /**
     * Generate field attributes
     *
     * @param  string  $tag
     * @param  array   $options
     *
     * @return void
     */
    public function addField(string $tag, array $options = []) : void
    {
        if (empty($options['tab']) && !empty($this->tab)) {
            $options['tab'] = $this->tab;
        }
        
        $options['tag']     = $tag;
        $options['id']      = $this->resolveFieldId($options['name']);
        $options['label']   = isset($options['title']) ? $options['title'] : $this->label($options['name']);
        //$options['hint']    = $this->hint($options['name']);

        $options['value'] = (isset($options['type']) && in_array($options['type'], ['password']))
            ? '' : $this->value($options['name']);

        if (!isset($options['groupClass'])) {
            $options['groupClass'] = $this->groupClass;
        }

        if (!isset($options['class'])) {
            $options['class'] = $this->fieldClass;
        }

        if (!empty($options['addClass'])) {
            $options['class'] .= ' ' . $options['addClass'];
        }
        
        $this->fields[$options['name']] = $options;
    }

    /**
     * Generate field attributes
     *
     * @param  string  $tag
     * @param  array   $options
     *
     * @return void
     */
    public function addFieldRangeFromTo(string $tag, array $options = []) : void
    {
        $options['tag']     = 'rangeFromTo' . ucfirst($tag);
        $options['id']      = $this->resolveFieldId($options['name']);
        $options['label']   = $this->label($options['name']);
        $options['hint']    = $this->hint($options['name']);

        $options['value_from'] = $this->value($options['name'] . '_from');
        $options['value_to'] = $this->value($options['name'] . '_to');

        if (!isset($options['groupClass'])) {
            $options['groupClass'] = $this->groupClass;
        }
        
        $options['groupClass'] .= ' group-range';

        if (!isset($options['class'])) {
            $options['class'] = $this->fieldClass;
        }

        if (!empty($options['addClass'])) {
            $options['class'] .= ' ' . $options['addClass'];
        }

        $this->fields[$options['name']] = $options;
    }

    /**
     * Generate button attributes
     *
     * @param  string  $type
     * @param  string  $class
     * @param  string  $label
     * @param  array   $options
     *
     * @return void
     */
    public function button(
        string $type,
        string $class,
        string $label,
        array $options = []
    ) : void {
        $this->buttons[] = [
            'type'  => $type,
            'class' => $class,
            'label' => $label,
            'options' => $options,
        ];
    }

    /**
     * Generate wrapper attributes
     *
     * @param  array  $options
     *
     * @return void
     */
    public function wrapper(array $options) : void
    {
        $this->wrappers[] = $options;
    }

    /**
     * @param  array  $actions
     * @param  array  $options
     *
     * @return void
     */
    public function ajaxifyPanel(array $actions, array $options) : void
    {
        $this->ajaxifyPanel['actions'] = $actions;
        $this->ajaxifyPanel['options'] = $options;
    }

    /**
     * @return string
     */
    protected function getModelKey() : string
    {
        if (!empty($this->model)) {
            $shortName = (new \ReflectionClass($this->model))->getShortName();
            return $shortName;
        } else {
            return 'filter';
        }
    }

    /**
     * @return string|null
     */
    protected function getModuleKey() : ?string
    {
        if (!empty($this->model)) {
            preg_match('/App\\\\Modules\\\\(.*?)\\\\Models/', get_class($this->model), $match);
            return $match[1] ?? isset($match[1]);
        } else {
            return null;
        }            
    }

    /**
     *
     * @return array
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     *
     * @return array
     */
    public function getErrors() : array
    {
        $errors = [];
        if (session('errors') !== null && session('errors') instanceof ViewErrorBag) {
            $errors = session('errors')->getMessages();
        }

        return $errors;
    }

    /**
     *
     * @return array
     */
    public function resolveMethod() : array
    {
        $method = strtoupper($this->method);
        $subMethod = '';
        if (in_array($method, ['PUT', 'DELETE'])) {
            $subMethod = $method;
            $method = 'POST';
        }

        return [$method, $subMethod];
    }

    /**
     *
     * @param string $name
     * @return string
     */
    public function resolveFieldId(string $name) : string
    {
        $id = str_replace('[', '_', $name);
        $id = str_replace(']', '', $id);

        return rtrim($id, '_');
    }

    /**
     * @param  string   $field
     * @param  array    $params
     *
     * @return void
     */
    public function resolveFieldConfig(string $field, array $params) : void
    {
        if (!empty($params['label'])) {
            $this->labelConfig[$field] = $params['label'];
        }
        
        if (isset($params['range']) && $params['range'] === true) {
        } else {
            if (isset($params['value'])) {
                $this->valueMap[$field] = $params['value'];
            } elseif (isset($params['default'])) {
                $this->defaultValueMap[$field] = $params['default'];
            }
        }
    }
    
    /*
     * @param string $name
     * @param array $params
     * @return void
     */
    public function addTab(string $name, array $params)
    {
        $this->tabs[$name] = $params;
    }
}
