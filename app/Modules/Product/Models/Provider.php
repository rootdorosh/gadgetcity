<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    
    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'product_providers';
    
    /**
     * The attributes that are mass assignable.
     
     * @var  array
     */
    public $fillable = [
        'id',
        'pid',
        'title',
        'is_active',
        'last_guid', 
    ];  
  
   
   
   
     
}