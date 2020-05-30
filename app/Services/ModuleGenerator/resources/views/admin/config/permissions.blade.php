<?php 
use Illuminate\Support\Str;

$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";
$tab1 = "    ";


$data = 'return [';
$data .= "\n{$tab1}" . '\'title\' => \'Модуль "'.$moduleName.'"\',' ;
$data .= "\n{$tab1}" . '\'items\' => [';
foreach ($models as $model) { if (!empty($model['skipMap']) && in_array('permission', $model['skipMap'])){continue;}
    $data .= "\n{$tab2}" . '\''. Str::camel($model) .'\' => [';
    
    $data .= "\n{$tab3}" . '\'title\' => ' . "'{$model}',";
    $data .= "\n{$tab3}" . '\'actions\' => [';
    
    foreach (['index', 'store', 'update', 'show', 'destroy'] as $action) {
        $data .= "\n{$tab4}" . '\'' . Str::camel($moduleName) . '.'. Str::camel($model) .'.'.$action.'\' => \'permission.'.$action.'\',';
    }
    
    $data .= "\n{$tab3}],";
    $data .= "\n{$tab2}],";
}
$data .= "\n{$tab1}" . '],';
$data .= "\n];";
?>
{!! $data !!}