<?php 
require_once 'vendor/autoload.php';

$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$madelineProto->async(false);
//$MadelineProto->start();

$settings = array(
    'peer' => '@monolitkh', //название_канала, должно начинаться с @, например @breakingmash
    'offset_id' => $params['offset_id'] ?: 10,
    'offset_date' => $params['offset_date'] ?: 0,
    'add_offset' => $params['add_offset'] ?: 0,
    'limit' => $params['limit'] ?: 20, //Количество постов, которые вернет клиент
    'max_id' => $params['max_id'] ?: 0, //Максимальный id поста
    'min_id' => $params['min_id'] ?: 0, //Минимальный id поста - использую для пагинации, при  0 возвращаются последние посты.
    //'hash' => []
);

$data = $MadelineProto->messages->getHistory($settings);
var_dump('bbbbb');
print_r($data);
die();
var_dump('ccccc');

?>