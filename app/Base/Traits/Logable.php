<?php

namespace App\Base\Traits;

use App\Modules\Log\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Logable
{

    public static function bootLogable()
    {
        static::created(function (Model $model) {
            self::log('created', $model);
        });

        static::updated(function (Model $model) {
            self::log('updated', $model);
        });

        static::deleted(function (Model $model) {
            self::log('deleted', $model);
        });
    }

    /*
     * @param string $action
     * @param Model $model
     */
    protected static function log(string $action, Model $model)
    {
        $modelClass = get_class($model);
        
        Log::create([
            'action' => $action,
            'logable_id' => $model->id ?? null,
            'logable_type' => $modelClass,
            'user_id' => auth()->id() ?? null,
            'properties' => $model ?? null,
        ]);
    }
    
    /**
     * Get all logs
     *
     * @return MorphMany
     */
    public function logable() : MorphMany
    {
        return $this->morphMany('App\Modules\Log\Models\Log', 'logable');
    }
}
