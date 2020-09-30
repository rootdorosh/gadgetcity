<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Base\Requests\BaseFilter;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Provider;
use App\Modules\Product\Models\ProductProviderPrice;
use DB;

/**
 * Class IndexRequest
 *
 * @package  App\Modules\Product
 *
 */
class PriceReportFilter extends BaseFilter
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('product.product.index');
    }

    /*
     * @return  array
     */
    public function rules(): array
    {
        $rules = parent::rules() + [
            'sort_attr' => [
                'nullable',
                'string',
                'in:' . implode(',', [
                    'id',
                    'title',
                ]),
            ],
            'title' => [
                'nullable',
            ],
            'is_availability' => [
                'nullable',
            ],
            'is_show_provider_item_title' => [
                'nullable',
            ],
            'date_from' => [
                'nullable',
            ],
            'date_to' => [
                'nullable',
            ],
        ];

        return $rules;
    }

    /*
     * @return  Builder
     */
    public function getQueryBuilder() : Builder
    {
        $where = [
            'product.id = ppp.product_id',
            'provider.is_active',
        ];

        if (!empty($this->date_from)) {
            $timeFrom = strtotime($this->date_from . ' 00:00:00');
            $where[] = "provider_item.price_time >= $timeFrom";

        }
        if (!empty($this->date_to)) {
            $timeTo = strtotime($this->date_to . ' 23:59:59');
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


        if ($this->title !== null) {
            $query->where("title", "like", "%{$this->title}%");
        }

        if ($this->is_availability !== null) {
            if ($this->is_availability == '0') {
                $query->where("availability", 0);
            } else {
                $query->whereRaw("availability > 0");
            }
        }

        return $query;
    }

    /*
     * @return  array
     */
    public function getData()
    {
        $perPage = $this->attr('per_page', self::PER_PAGE);
        $page = $this->attr('page', self::PAGE);
        $sortDir = $this->attr('sort_dir');
        $sortAttr = $this->attr('sort_attr');
        $offset = $page * $perPage - $perPage;

        $query = $this->getQueryBuilder();
        $count = $this->getCountOfQuery($query);

        $items = [];

        $query->offset($offset)->limit($perPage);
        if ($sortDir && $sortAttr) {
            $query->orderBy($sortAttr, $sortDir);
        }

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

            if (!empty($this->date_from)) {
                $timeFrom = strtotime($this->date_from . ' 00:00:00');
                $query->whereRaw("product_providers_items.price_time >= $timeFrom");
            }
            if (!empty($this->date_to)) {
                $timeTo = strtotime($this->date_to . ' 23:59:59');
                $query->whereRaw("product_providers_items.price_time <= $timeTo");
            }

            //dd(self::sql($query));

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
                'is_availability' => $row->availability ? 1 : 0,
                'is_show_provider_item_title' => $this->is_show_provider_item_title ? 1 : 0,
            ];
            foreach ($providers as $provider) {
                // last price by period
                $prices = !empty($dataPrice[$row->id][$provider->id]) ? $dataPrice[$row->id][$provider->id] : [];
                $price = '';
                if (count($prices)) {
                    usort($prices, function ($a, $b) {
                        return $a['price_time'] <= $b['price_time'] ? 1 : -1;
                    });
                    $price = isset($prices[0]) ? '$' . $prices[0]['price'] : '';
                }

                $item['_provider_' . $provider->id] = $prices;

                $item['provider_' . $provider->id] = $this->is_show_provider_item_title
                    ? !empty($dataPrice[$row->id][$provider->id]) ? $dataPrice[$row->id][$provider->id] : []
                    : $price;
            }

            $items[] = $item;
        }

        return [
            'items' => $items,
            'count' => $count,
            'from' => $offset + 1,
            'to' => $offset + min($offset + count($items), $count),
        ];
    }
}
