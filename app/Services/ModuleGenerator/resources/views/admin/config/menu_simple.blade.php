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
$data .= "{$tab2}'route' => r('admin.".Str::kebab($moduleData['menu']['route']).".index'),\n";
$data .= "{$tab2}'icon' => '".$moduleData['menu']['icon']."',\n";
$data .= "{$tab2}'permission' => '". $moduleData['menu']['permission'] . "',\n";
$data .= "{$tab2}'rank' => 1,\n";
$data .= "{$tab1}],\n";
$data .= "];";
?>

{!! $data !!}