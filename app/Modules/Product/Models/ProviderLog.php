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


    public static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->content = str_tg_clean($model->content);
        });
    }

    /**
     * @param array $attributes
     * @return static|null
     */
    public static function add(array $attributes):? self
    {
        $line = str_tg_clean($attributes['content']);
        $formCustom = (new ParserService)->applyCustomTemplatesSingle($attributes['content']);
        if ($formCustom) {
            $provider = Provider::find($attributes['provider_id']);

            (new ParserService)->parseProviderItem($provider, [
                'attributes' => [
                    'price' => $formCustom['price'],
                    'title' => $formCustom['title'],
                ],
                'price_time' => $attributes['message_time'],
            ]);
        }

        if (preg_match('/(([0-9]{1,5})(gb))/', $line, $match)) {
            $provider = Provider::find($attributes['provider_id']);
            foreach ((new ParserService)->getSplitProductsByColor($line) as $title) {
                (new ParserService)->parseProviderItem($provider, [
                    'attributes' => [
                        'price' => 0,
                        'title' => $title,
                    ],
                    'price_time' => $attributes['message_time'],
                ]);
            }
            return null;
        } else {
            $values = ['16', '32', '64', '128', '256', '512', '1024', '2048', '4096'];
            foreach ($values as $value) {
                if (substr_count($line, $value)) {
                    $provider = Provider::find($attributes['provider_id']);
                    if (in_array($provider->pid, ['swipe_ua', 'ioptua'])) {
                        foreach ((new ParserService)->getSplitProductsByColor($line ) as $title) {
                            (new ParserService)->parseProviderItem($provider, [
                                'attributes' => [
                                    'price' => 0,
                                    'title' => $title,
                                ],
                                'price_time' => $attributes['message_time'],
                            ]);
                        }
                        return null;
                    }
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
