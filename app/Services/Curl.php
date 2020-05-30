<?php
namespace App\Services;

use Cache;

class Curl
{
    public static function getPage($url, $params = [])
    {
        $cache = !empty($params['cache']) ? $params['cache'] : true;
        $key = md5($url) . '_';
        $time = $cache ? (60 * 60 * 24) : 1;
        
        return Cache::remember($key, $time, function() use ($url) {
            if (!empty($params['post']) && is_array($params['post'])) {
                $params['post'] = http_build_query($params['post']);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
            
            // откуда пришли на эту страницу
            if (empty($params['ref'])) {
                $params['ref'] = $url;
            }

            curl_setopt($ch, CURLOPT_REFERER, $params['ref']);

            // не проверять SSL сертификат
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // не проверять Host SSL сертификата
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // это необходимо, чтобы cURL не высылал заголовок на ожидание
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ch, CURLOPT_HEADER, 0);

            $result = curl_exec($ch);
            $error = curl_error($ch);

            $result = trim($result);

            return $result;
        });        
    }

}
