<?php 
use Illuminate\Support\Str;
$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";
$tab1 = "    ";
?>
declare( strict_types = 1 );

namespace App\Modules\{{ $moduleName }}\Models\Lang;

use Illuminate\Database\Eloquent\Model;

class {{ $model['name'] }}Lang extends Model
{
    /**
     * primary key.
     *
     * @var string
     */
    protected $primaryKey = 'translation_id';
       
    /**
     * table name.
     *
     * @var string
     */
    protected $table = '{{ $model['table'] }}_lang';

    /**
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     
     * @var array
     */
    public $fillable = [@foreach ($model['translatable']['fields'] as $attr => $item){!! "\n{$tab2}" !!}'{!! $attr !!}',@endforeach {!! "\n{$tab1}" !!}];
}
