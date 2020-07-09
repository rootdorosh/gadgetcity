<?php 

declare( strict_types = 1 );

namespace App\Modules\Color\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    
    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'color';
    
    /**
     * The attributes that are mass assignable.
     
     * @var  array
     */
    public $fillable = [
        'id',
        'title',
        'code', 
    ];  
  
   
     
}