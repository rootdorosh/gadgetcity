<?php 
use Illuminate\Support\Str;
use App\Services\ModuleGenerator\ModuleGeneratorService;
$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";

$relations=[];
foreach ($model['fields'] as $attr => $item) {
    if (!empty($item['relation'])) {
        $relations[$item['relation']['type']] = $item['relation']['type'];
    }    
}
$tab1 = "    ";
?>
declare( strict_types = 1 );

namespace App\Modules\{{ $moduleName }}\Models;

use Illuminate\Database\Eloquent\Model;
@if (!empty($model['translatable']))
use App\Services\Translatable\Translatable;
@endif
@foreach ($relations as $relation)
use Illuminate\Database\Eloquent\Relations\{{ $relation }};
@endforeach

class {{ $model['name'] }} extends Model
{
@if (!empty($model['translatable']))
    use Translatable;
@endif    
    /**
     * @var bool
     */
    public $timestamps = false;
{!! ModuleGeneratorService::model_const($model) !!}
    /*
     * @var string
     */
    public $table = '{{ $model['table'] }}';
@if (!empty($model['translatable']))
    
    /**
     * @var string
     */
	public $translationForeignKey = '{{ $model['translatable']['owner_id'] }}';	

    /*
     * @var array
     */
    public $translatedAttributes = [@foreach ($model['translatable']['fields'] as $attr => $item){!! "\n{$tab2}" !!}'{!! $attr !!}',@endforeach {!! "\n{$tab1}" !!}];

    /*
     * @var array
     */
    protected $with = ['translations'];
    @endif
    
    /**
     * The attributes that are mass assignable.
     
     * @var array
     */
    public $fillable = [{!! "\n{$tab2}" !!}'id',@foreach ($model['fields'] as $attr => $item){!! "\n{$tab2}" !!}'{!! $attr !!}',@endforeach {!! "\n{$tab1}" !!}];  
@foreach ($model['fields'] as $attr => $item)  
@if (!empty($item['relation']))
    /**
     * Get the {{ $item['relation']['name'] }}.
     *
     * @return {{ $item['relation']['type'] }}
     */
    public function {{ $item['relation']['name'] }}() : {{ $item['relation']['type'] }}
    {
        return $this->{{ Str::camel($item['relation']['type']) }}('{{ $item['relation']['model'] }}');
    }
@endif @endforeach    
}