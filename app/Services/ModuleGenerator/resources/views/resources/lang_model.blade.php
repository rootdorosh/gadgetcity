<?php 
use Illuminate\Support\Str;

$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";
$tab1 = "    ";

$data = '';
foreach ($model['fields'] as $attr => $item) {
    $data .= "{$tab2}" . '\'' . $attr . '\' => \''. $item['label'] .'\',' . "\n";
}
if (!empty($model['translatable'])) {
    foreach ($model['translatable']['fields'] as $attr => $field) {
        $data .= "{$tab2}" . '\'' . $attr . '\' => \''. $field['label'] .'\',' . "\n";
    }
}
?>

return [
    'title' => [
        'singular' => '<?= $model['name']?>',
        'index' => '<?= $model['name_plural']?>',
        'create' => 'Create <?= strtolower($model['name'])?>',
        'update' => 'Update <?= strtolower($model['name'])?>',
    ],
    'success' => [
        'created' => '<?= $model['name']?> sucess created',
        'updated' => '<?= $model['name']?> sucess updated',
    ],
    'fields' => [
{!! $data !!}    ],
];