<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderLog extends Model
{
    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'product_provider_log';

    /**
     * The attributes that are mass assignable.

     * @var  array
     */
    public $fillable = [
        'id',
        'provider_id',
        'content',
        'message_time',
        'create_time',
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
     * @param array $attributes
     * @return static|null
     */
    public static function add(array $attributes):? self
    {
        $data = $attributes;
        $data['create_time'] = time();

        $data['content'] = trim($data['content']);
        if (empty($data['content'])) {
            return null;
        }

        return self::firstOrCreate($attributes, $data);
    }

}
