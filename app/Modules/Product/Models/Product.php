<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    /**
     * @param string|null $period
     * @return array
     */
    public function getDataForExportPriceReport(string $period = null): array
    {
        $timeFrom = $timeTo = 0;
        if ($period === 'today') {
            $timeFrom = strtotime(date('Y-m-d 00:00:00'));
            $timeTo = time();
        } elseif ($period === 'yesterday') {
            $dayTime = 60 * 60 * 24;
            $timeFrom = strtotime(date('Y-m-d 00:00:00')) - $dayTime;
            $timeTo = strtotime(date('Y-m-d 23:59:59')) - $dayTime;
        } elseif ($period === 'week') {
            $timeFrom = strtotime('-1 week');
            $timeTo = time();
        } elseif ($period === 'month') {
            $timeFrom = strtotime('-1 month');
            $timeTo = time();
        }

        $where = [
            'product.id = ppp.product_id',
            'provider.is_active',
        ];

        if ($timeFrom) {
            $where[] = "provider_item.price_time >= $timeFrom";

        }
        if ($timeTo) {
            $where[] = "provider_item.price_time <= $timeTo";
        }


        $query = Product::query()->select([
            DB::raw('product.*'),
            DB::raw('(
                    SELECT COUNT(*)
                    FROM product_provider_prices AS ppp
                    LEFT JOIN product_providers_items AS provider_item ON ppp.provider_item_id = provider_item.id
                    LEFT JOIN product_providers AS provider ON provider_item.provider_id = provider.id
                    WHERE ' . implode(' AND ', $where) . '
                ) AS count_price'),
        ])
            ->havingRaw('count_price > 0');

        $items = [];
        $rows = $query->get();

        $dataPrice = [];

        $ids = $rows->pluck('id')->toArray();

        if (count($ids)) {
            $providers = Provider::get()->pluck('pid', 'id')->toArray();
            $query = ProductProviderPrice::query()
                ->select([
                    DB::raw('product_provider_prices.product_id AS product_id'),
                    DB::raw('product_provider_prices.price AS price'),
                    DB::raw('product_providers_items.price_time AS price_time'),
                    DB::raw('product_providers_items.title AS title'),
                    DB::raw('product_providers_items.provider_id AS provider_id'),
                ])
                ->leftJoin('product_providers_items', 'product_providers_items.id', 'product_provider_prices.provider_item_id')
                ->whereIn('product_provider_prices.product_id', $ids)->orderBy('price');

            if ($timeFrom) {
                $query->whereRaw("product_providers_items.price_time >= $timeFrom");
            }
            if ($timeTo) {
                $query->whereRaw("product_providers_items.price_time <= $timeTo");
            }

            $prices = $query->get();

            foreach ($prices as $price) {
                if (!empty($providers[$price->provider_id])) {
                    $dataPrice[$price->product_id][$price->provider_id][] = [
                        'price' => $price->price,
                        'title' => $price->title,
                        'price_time' => $price->price_time,
                        'date' => $price->price_time ? date('d.m.Y', $price->price_time) : null,
                    ];
                }
            }
        }

        $providers = Provider::where('is_active', 1)->get();

        foreach ($rows as $row) {
            $item = [
                'title' => $row->title,
                'availability' => $row->availability,
            ];
            foreach ($providers as $provider) {
                $item['provider_' . $provider->id] = !empty($dataPrice[$row->id][$provider->id]) ? $dataPrice[$row->id][$provider->id] : [];
            }

            $items[] = $item;
        }

        return $items;
    }


}
