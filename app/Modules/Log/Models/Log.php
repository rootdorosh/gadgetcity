<?php

namespace App\Modules\Log\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /*
     * @var string
     */
    public $table = 'logable';

    /**
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'logable_id', 'logable_type', 'action', 'user_id', 'properties'
    ];  
}
