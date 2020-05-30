<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Base\Requests\BaseFilter;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Provider;
use App\Modules\Product\Models\Price;

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
                    'is_active',
                ]),
            ],
            'title' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'nullable',
                'integer',
            ],
            'id' => [
                'nullable',
                'integer',
                'min:1',
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
        if ($this->id !== null) {
            $query->where("id", "like", "%{$this->id}%");
        }

        if ($this->title !== null) {
            $query->where("title", "like", "%{$this->title}%");
        }

        if ($this->is_active !== null) {
            $query->where("is_active", "like", "%{$this->is_active}%");
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
        $ids = $rows->pluck('id');

        if (count($ids)) {
            $providers = Provider::get()->pluck('pid', 'id')->toArray();
            $prices = Price::whereIn('product_id', $ids)->orderBy('price')->get();

            foreach ($prices as $price) {
                if (!empty($providers[$price->provider_id])) {
                    $dataPrice[$price->product_id][] = [
                        'price' => $price->price,
                        'provider' => $providers[$price->provider_id],
                    ];
                }
            }
        }

        foreach ($rows as $row) {
            $items[] = [
                'id' => $row->id,
                'title' => $row->title,
                'is_active' => $row->is_active,
                'price' => !empty($dataPrice[$row->id])
                    ? $this->formatPrice($dataPrice[$row->id])
                    : null,
            ];
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
