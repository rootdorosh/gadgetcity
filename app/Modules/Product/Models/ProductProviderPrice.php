<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductProviderPrice extends Model
{

    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'product_provider_prices';

    /**
     * The attributes that are mass assignable.

     * @var  array
     */
    public $fillable = [
        'id',
        'product_id',
        'provider_item_id',
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
    public function providerItem() : BelongsTo
    {
        return $this->belongsTo('App\Modules\Product\Models\ProviderItem');
    }


}
