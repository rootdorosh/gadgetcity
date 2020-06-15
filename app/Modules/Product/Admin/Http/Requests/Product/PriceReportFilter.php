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
                'string',
            ],
        ];

        return $rules;
    }

    /*
     * @return  Builder
     */
    public function getQueryBuilder() : Builder
    {
        $query = Product::query();

        if ($this->title !== null) {
            $query->where("title", "like", "%{$this->title}%");
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
        $count = $query->count();

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
            $prices = ProductProviderPrice::query()
                ->select([
                    DB::raw('product_provider_prices.product_id AS product_id'),
                    DB::raw('product_provider_prices.price AS price'),
                    DB::raw('product_providers_items.price_time AS price_time'),
                    DB::raw('product_providers_items.title AS title'),
                    DB::raw('product_providers_items.provider_id AS provider_id'),
                ])
                ->leftJoin('product_providers_items', 'product_providers_items.id', 'product_provider_prices.provider_item_id')
                ->whereIn('product_provider_prices.product_id', $ids)->orderBy('price')->get();

            foreach ($prices as $price) {
                if (!empty($providers[$price->provider_id])) {
                    $dataPrice[$price->product_id][$price->provider_id][] = [
                        'price' => $price->price,
                        'title' => $price->title,
                        'date' => $price->price_time ? date('d.m.Y', $price->price_time) : null,
                    ];
                }
            }
        }

        $providers = Provider::where('is_active', 1)->get();

        foreach ($rows as $row) {
            $item = ['title' => $row->title];
            foreach ($providers as $provider) {
                $item['provider_' . $provider->id] = !empty($dataPrice[$row->id][$provider->id]) ? $dataPrice[$row->id][$provider->id] : [];
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

    /*
     *
     */
    public function formatPrice($items)
    {
        $html = '<table>';
        foreach ($items as $item) {
            $html.= '<tr>';
            $html.= '<td>'.$item['provider'].'</td>';
            $html.= '<td>'.$item['price'].'</td>';
            $html.= '</tr>';
        }
        $html.= '</table>';
        return $html;
    }
}
