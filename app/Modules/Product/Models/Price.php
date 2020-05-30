<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    
    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'product_prices';
    
    /**
     * The attributes that are mass assignable.
     
     * @var  array
     */
    public $fillable = [
        'id',
        'product_id',
        'provider_id',
        'price', 
    ];  
  
    /**
     * Get the product.
     *
     * @return  BelongsTo
     */
    public function product() : BelongsTo
    {
        return $this->belongsTo('App\Modules\Product\Models\Product');
    }
   
    /**
     * Get the provider.
     *
     * @return  BelongsTo
     */
    public function provider() : BelongsTo
    {
        return $this->belongsTo('App\Modules\Product\Models\Provider');
    }
   
     
}