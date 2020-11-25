<?php 

declare( strict_types = 1 );

namespace App\Modules\Pattern\Models;

use Illuminate\Database\Eloquent\Model;

class Pattern extends Model
{
    
    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'pattern';
    
    /**
     * The attributes that are mass assignable.
     
     * @var  array
     */
    public $fillable = [
        'id',
        'example',
        'value',
        'rank', 
    ];  
  
   
   
     
}