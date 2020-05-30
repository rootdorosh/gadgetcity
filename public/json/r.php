<?php
header("Content-type: application/json; charset=utf-8");
// Создаем поток
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept: application/json\r\n" .
              "user-key: ceb6cbc51909a047c0756865b7cad084\r\n"
  )
);

$context = stream_context_create($opts);

// Открываем файл с помощью установленных выше HTTP-заголовков
//$file = file_get_contents('https://developers.zomato.com/api/v2.1/restaurant?res_id=' . $_GET['id'], false, $context);
//$file = file_get_contents('https://developers.zomato.com/api/v2.1/categories', false, $context);
$file = file_get_contents('https://developers.zomato.com/api/v2.1/cities?city_ids=' . $_GET['q'], false, $context);

echo $file;
?>
