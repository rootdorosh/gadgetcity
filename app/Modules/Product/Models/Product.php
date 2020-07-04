<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'product';

    /**
     * The attributes that are mass assignable.

     * @var  array
     */
    public $fillable = [
        'id',
        'title',
        'is_active',
        'availability',
    ];



}
