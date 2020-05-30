<?php

namespace App\Base\Traits;

use Cache;
use Illuminate\Database\Eloquent\Model;

trait Cacheable
{
    public static function bootCacheable()
    {
        static::saved(function (Model $model) {
            $model->flush($model->getTag());
        });

        static::deleted(function (Model $model) {
            $model->flush($model->getTag());
        });
    }    
    
    /*
     * @param string $tag
     */
    public function flush(string $tag)
    {
        Cache::tags($tag)->flush();        
    }

    /*
     * @return string
     */
    public function getTag(): string
    {
        return $this->table;
    }
}
