<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\ProviderItem;

use App\Modules\Product\Models\ProductProviderPrice;
use Illuminate\Database\Eloquent\Builder;
use App\Base\Requests\BaseFilter;
use App\Modules\Product\Models\ProviderItem;
use DB;

/**
 * Class IndexRequest
 *
 * @package  App\Modules\Product
 *
 */
class IndexFilter extends BaseFilter
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('product.provideritem.index');
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
                    'provider_id',
                    'title',
                    'product_title',
                    'status',
                    'price',
                    'price_time',
                ]),
            ],
            'provider_id' => [
                'nullable',
            ],
            'title' => [
                'nullable',
            ],
            'price' => [
                'nullable',
            ],
            'product_title' => [
                'nullable',
            ],
            'status' => [
                'nullable',
            ],
            'id' => [
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
        $query = ProviderItem::select([
            DB::raw('product_providers_items.id AS id'),
            DB::raw('product_providers_items.status AS status'),
            DB::raw('product_providers_items.title AS title'),
            DB::raw('product_providers_items.provider_id AS provider_id'),
            DB::raw('product_providers.title AS provider_title'),
            DB::raw('product_providers_items.price AS price'),
            DB::raw('product_providers_items.price_time AS price_time'),
        ])
            ->leftJoin('product_providers', 'product_providers.id', '=', 'product_providers_items.provider_id');

        if ($this->id !== null) {
            $query->where("product_providers_items.id", "like", "%{$this->id}%");
        }

        if ($this->provider_id !== null) {
            $query->where("product_providers_items.provider_id", $this->provider_id);
        }

        if ($this->title !== null) {
            $titles = explode(' ', $this->title);
            foreach ($titles as $title) {
                $query->where("product_providers_items.title", "like", "%{$title}%");
            }

        }

        if ($this->price !== null) {
            $query->where("product_providers_items.price", "like", "%{$this->price}%");
        }

        if ($this->product_title !== null) {
            $query->where("product.title", "like", "%{$this->product_title}%");
        }

        if ($this->status !== null) {
            $query->where("product_providers_items.status", $this->status);
        }

        return $query;
    }

    /*
     * @return  array
     */
    public function getData()
    {
        $query = $this->getQueryBuilder();
        $count = $query->count();

        $perPage = $this->attr('per_page', self::PER_PAGE);
        $page = $this->attr('page', self::PAGE);
        $sortDir = $this->attr('sort_dir');
        $sortAttr = $this->attr('sort_attr');
        $offset = $page * $perPage - $perPage;


        $query->offset($offset)->limit($perPage);
        if ($sortDir && $sortAttr) {
            $query->orderBy($sortAttr, $sortDir);
        }

        $rows = $query->get();

        $items = [];

        $dataPrices = [];
        $providerItemIds = $rows->pluck('id')->toArray();
        if ($providerItemIds) {
            $prices = ProductProviderPrice::query()
                ->select([
                    DB::raw('product_provider_prices.id AS id'),
                    DB::raw('product_provider_prices.provider_item_id AS provider_item_id'),
                    DB::raw('product.title AS title'),
                ])
                ->leftJoin('product', 'product_provider_prices.product_id', 'product.id')
                ->whereIn('provider_item_id', $providerItemIds)
                ->get();

            foreach ($prices as $item) {
                $dataPrices[$item->provider_item_id][] = [
                    'id' => $item->id,
                    'title' => $item->title,
                ];
            }
        }

        foreach ($rows as $row) {
            $productCell = '';
            if (!empty($dataPrices[$row->id])) {
                foreach ($dataPrices[$row->id] as $price) {
                    $productCell.= '<p>'.$price['title'].' <a href="#" data-id="'.$price['id'].'" class="badge badge-danger js-remove-price">x</a></p>';
                }
            }

            $productCell.= '<a href="#" class="js-link-provider-item-set-product" data-id="'.$row->id.'">'. __('product::provider_item.set_product') .'</a>';
            $productCell.= '<input data-id="'.$row->id.'" class="js-autocomplete hidden" />';

            $item = [
                'id' => $row->id,
                'provider_id' => $row->provider_id,
                'provider_title' => $row->provider_title,
                'price' => $row->price,
                'product_title' => $productCell,
                'price_time' => date('d.m.Y', $row->price_time),
                /*
                !empty($row->product_title)
                    ? $row->product_title
                    : '<a href="#" class="js-link-provider-item-set-product" data-id="'.$row->id.'">'. __('product::provider_item.set_product') .'</a>
                       <input data-id="'.$row->id.'" class="js-autocomplete hidden" />',
                */
                'title' => $row->title,
                'status' => '<span class="badge badge-' . ProviderItem::STATUSES_STYLE[$row->status] . '">' . ProviderItem::STATUSES[$row->status] . '</span>',
            ];

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
