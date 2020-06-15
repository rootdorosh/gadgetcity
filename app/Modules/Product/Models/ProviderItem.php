<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderItem extends Model
{
    const STATUS_AWAIT = 1;
    const STATUS_ACCEPT = 2;
    const STATUS_CANCEL = 3;
    const STATUS_AUTO = 4;
    const STATUSES = [
        self::STATUS_AWAIT => 'await',
        self::STATUS_ACCEPT => 'accept',
        self::STATUS_CANCEL => 'cancel',
        self::STATUS_AUTO => 'auto',
    ];
    const STATUSES_STYLE = [
        self::STATUS_AWAIT => 'secondary',
        self::STATUS_ACCEPT => 'success',
        self::STATUS_CANCEL => 'danger',
        self::STATUS_AUTO => 'warning',
    ];

    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'product_providers_items';

    /**
     * The attributes that are mass assignable.

     * @var  array
     */
    public $fillable = [
        'id',
        'provider_id',
        'title',
        'product_id',
        'status',
        'price',
        'price_time',
    ];

    /**
     * Get the provider.
     *
     * @return  BelongsTo
     */
    public function provider() : BelongsTo
    {
        return $this->belongsTo('App\Modules\Product\Models\Provider');
    }


    /**
     * Get the product.
     *
     * @return  BelongsTo
     */
    public function product() : BelongsTo
    {
        return $this->belongsTo('App\Modules\Product\Models\Product');
    }

    /*
     *
     */
    public function getStatusTitle()
    {
        return !empty(self::STATUSES[$this->status]) ? self::STATUSES[$this->status] : null;
    }

}
