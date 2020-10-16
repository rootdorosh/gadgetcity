<?php

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\ProviderLog;

use App\Modules\Product\Models\ProductProviderPrice;
use Illuminate\Database\Eloquent\Builder;
use App\Base\Requests\BaseFilter;
use App\Modules\Product\Models\ProviderLog;
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
        return $this->user()->hasPermission('product.providerlog.index');
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
                    'content',
                    'message_time',
                    'create_time',
                ]),
            ],
            'provider_id' => [
                'nullable',
            ],
            'message_id' => [
                'nullable',
            ],
            'content' => [
                'nullable',
            ],
            'message_time' => [
                'nullable',
            ],
            'create_time' => [
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
        $query = ProviderLog::select([
            DB::raw('product_provider_log.*'),
            DB::raw('product_providers.title AS provider_title'),
         ])
            ->leftJoin('product_providers', 'product_providers.id', '=', 'product_provider_log.provider_id');

        if ($this->id !== null) {
            $query->where("product_provider_log.id", "like", "%{$this->id}%");
        }

        if ($this->provider_id !== null) {
            $query->where("product_provider_log.provider_id", $this->provider_id);
        }

        if (!empty($_GET['content'])) {
            $query->where("product_provider_log.content", "like", '%'.$_GET['content'].'%');
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
                'provider_id' => $row->provider_title,
                'content' => $row->content,
                'message_time' => date('Y-m-d H:i:s', $row->message_time),
                'create_time' => date('Y-m-d H:i:s', $row->create_time),
            ];
        });
    }

}
