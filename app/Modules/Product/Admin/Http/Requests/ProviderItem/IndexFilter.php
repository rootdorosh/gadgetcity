<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\ProviderItem;

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
            DB::raw('product_providers_items.product_id AS product_id'),
            DB::raw('product_providers_items.provider_id AS provider_id'),
            DB::raw('product_providers.title AS provider_title'),
            DB::raw('product_providers_items.price AS price'),
            DB::raw('product.title AS product_title'),
        ])
            ->leftJoin('product_providers', 'product_providers.id', '=', 'product_providers_items.provider_id')
            ->leftJoin('product', 'product.id', '=', 'product_providers_items.product_id');

        if ($this->id !== null) {
            $query->where("product_providers_items.id", "like", "%{$this->id}%");
        }

        if ($this->provider_id !== null) {
            $query->where("product_providers_items.provider_id", $this->provider_id);
        }

        if ($this->title !== null) {
            $query->where("product_providers_items.title", "like", "%{$this->title}%");
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
        return $this->resolveData(function($row) {
            return [
                'id' => $row->id,
                'provider_id' => $row->provider_id,
                'provider_title' => $row->provider_title,
                'price' => $row->price,
                'product_title' => !empty($row->product_title)
                    ? $row->product_title
                    : '<a href="#" class="js-link-provider-item-set-product" data-id="'.$row->id.'">'. __('product::provider_item.set_product') .'</a>
                       <input data-id="'.$row->id.'" class="js-autocomplete hidden" />',
                'title' => $row->title,
                'product_id' => $row->product_id,
                'status' => '<span class="badge badge-' . ProviderItem::STATUSES_STYLE[$row->status] . '">' . ProviderItem::STATUSES[$row->status] . '</span>',
            ];
        });
    }


}
