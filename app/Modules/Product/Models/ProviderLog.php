<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use App\Services\Parser\ParserService;
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
        $line = str_tg_clean($attributes['content']);
        if (preg_match('/(([0-9]{1,5})(gb))/', $line, $match)) {
            echo $line;

            $provider = Provider::find($attributes['provider_id']);
            (new ParserService)->parseProviderItem($provider, [
                'attributes' => [
                    'price' => 0,
                    'title' => str_tg_clean($attributes['content']),
                ],
                'price_time' => $attributes['message_time'],
            ]);
            return null;
        } elseif (preg_match('/(\s[0-9]{2,5}\s)/', $line, $match)) {
            if (isset($match[1]) && in_array(trim($match[1]), ['16', '32', '128', '256', '512', '1024', '2048', '4096'])) {
                $provider = Provider::find($attributes['provider_id']);
                if (in_array($provider->pid, ['swipe_ua', 'ioptua'])) {
                    (new ParserService)->parseProviderItem($provider, [
                        'attributes' => [
                            'price' => 0,
                            'title' => str_tg_clean($attributes['content']),
                        ],
                        'price_time' => $attributes['message_time'],
                    ]);
                    return null;
                }
            }
        }

        $data = $attributes;
        $data['create_time'] = time();

        $data['content'] = trim($data['content']);
        if (empty($data['content'])) {
            return null;
        }

        return self::firstOrCreate($attributes, $data);
    }

}
