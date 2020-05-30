<?php
use Illuminate\Support\Str;

$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";
$tab1 = "    ";

$data = "return [\n";
$data .= "{$tab1}[\n";
$data .= "{$tab2}'module' => '".Str::camel($moduleName)."',\n";
$data .= "{$tab2}'title' => '".$moduleData['menu']['title']."',\n";
$data .= "{$tab2}'route' => '#',\n";
$data .= "{$tab2}'icon' => '".$moduleData['menu']['icon']."',\n";
$data .= "{$tab2}'permission' => '". $moduleData['menu']['permission'] . "',\n";
$data .= "{$tab2}'rank' => 1,\n";
$data .= "{$tab2}'children' => [\n";
foreach ($modelsData as $model){ if (!empty($model['skipMap']) && in_array('menu', $model['skipMap'])){continue;}
    $data .= "{$tab3}[\n";
    $data .= "{$tab4}'title' => '".$model['name_plural']."',\n";
    $data .= "{$tab4}'route' => r('admin.".Str::kebab($moduleName).".".Str::kebab($model['name_plural']).".index'),\n";
    $data .= "{$tab4}'icon' => '".$model['menu']['icon']."',\n";
    $data .= "{$tab4}'permission' => '".Str::camel($moduleName) . '.'. strtolower($model['name']).".index',\n";
    $data .= "{$tab3}],\n";
}
$data .= "{$tab2}],\n";
$data .= "{$tab1}],\n";
$data .= "];";
?>

{!! $data !!}
