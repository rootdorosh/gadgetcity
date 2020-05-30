<?php

namespace App\Base\Traits;

use Illuminate\Support\Str;

trait RelationMap
{
    /*
     * @param string $relation
     * @return array
     */
    public function getRelationData(string $relation): array
    {
        $function = "getDataRelation" . Str::studly($relation);
        
        return call_user_func([$this, $function]);
    }
}
