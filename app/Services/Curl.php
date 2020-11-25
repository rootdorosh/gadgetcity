<?php
namespace App\Services;

use Cache;

class Curl
{
    public static $code;

    public static function getPage($url, $params = [])
    {
        $file = storage_path() . '/parsing/' . md5($url) . '.html';

        //if (!is_file($file)) {
        //sleep(2);

        if (!empty($params['post']) && is_array($params['post'])) {
            $params['post'] = http_build_query($params['post']);
        }

        $ch = self::getChInstance();
        curl_setopt($ch, CURLOPT_URL, $url);

        // откуда пришли на эту страницу
        if (empty($params['ref'])) {
            $params['ref'] = $url;
        }
        curl_setopt($ch, CURLOPT_REFERER, $params['ref']);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);

        $result = trim($result);
        curl_close ($ch);

        self::$code = $info['http_code'];

        if ($info['http_code'] === 200) {
            //file_put_contents($file, $result);

            return $result;
        } else {
            dump($info);
            dump($error);
            return false;
        }
        //}

        //return file_get_contents($file);
    }

    public static function getChInstance()
    {
        $userAgents = self::getUserAgents();
        $userAgent = $userAgents[array_rand($userAgents)];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 100,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_COOKIEFILE =>  storage_path() . '/cookies.txt',
            CURLOPT_COOKIEJAR =>  storage_path() . '/cookies.txt',
        ]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //GET /v1/vlrg?kind_id=1&lang_code=en_COM HTTP/1.1
            //Host: api-globalvlrg.osram.info
            //Connection: close
            //sec-ch-ua: "\\Not;A\"Brand";v="99", "Google Chrome";v="85", "Chromium";v="85"
            //sec-ch-ua-mobile: ?0
            //User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36
            //Accept: */*
            //Origin: https://www.osram.com
            //Sec-Fetch-Site: cross-site
            //Sec-Fetch-Mode: cors
            //Sec-Fetch-Dest: empty
            //Referer: https://www.osram.com/
            //Accept-Encoding: gzip, deflate
            //Accept-Language: en-US,en;q=0.9,uk-UA;q=0.8,uk;q=0.7,pl;q=0.6
        ]);

        return $ch;
    }

    public static function getProxies()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://bulbsize.com/proxies.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        $list = explode("\n", $content);
        $list = array_map(function($val) { return trim($val); }, $list);

        return array_filter($list, function ($value) {
            return !empty($value);
        });
    }

    public static function getUserAgents()
    {
        $list = "Mozilla/3.6 (Windows NT 10.0; WOW64) AppleWebKit/566.8 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/566.8
Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)
Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)
Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)
Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727)
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; Zoom 3.6.0; ms-office; MSOffice 16)
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Avant Browser)
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/4.0; MRSPUTNIK 2, 3, 0, 244; MRA 5.7 (build 03686; ms-office; MSOffice 14); SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; InfoPath.3)
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/7.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; InfoPath.3; .NET4.0E; .NET4.0C; ms-office; MSOffice 15)
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; Win64; x64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; ms-office; MSOffice 14)
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729)
Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.2; MSOffice 12)
Mozilla/4.0 (compatible; MSIE 8.0; Linux armv6l; Maemo; Opera Mobi/8; en-GB) Opera 11.00
Mozilla/4.0 (compatible; MSIE 8.0; Linux armv7l; Maemo; Opera Mobi/4; fr) Opera 10.1
Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; InfoPath.3)
Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)
Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)
Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)
Mozilla/4.0 (compatible; Synapse)
Mozilla/4.0 (compatible; Windows Mobile; WCE; Opera Mobi/WMD-50433; U; de) Presto/2.4.13 Version/10.00
Mozilla/4.3 (Windows NT 10.0; WOW64) AppleWebKit/595.9 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/595.9
Mozilla/5.0
Mozilla/5.0 (Android 10; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0
Mozilla/5.0 (Android 2.2.2; Linux; Opera Mobi/ADR-1103311355; U; en; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 11.00
Mozilla/5.0 (Android 4.4.2; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0
Mozilla/5.0 (Android 6.0.1; Mobile; rv:61.0) Gecko/61.0 Firefox/61.0
Mozilla/5.0 (Android 7.0; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0
Mozilla/5.0 (Android 7.1.1; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0
Mozilla/5.0 (Android 8.0.0; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0
Mozilla/5.0 (Android 8.0.0; Mobile; rv:75.0) Gecko/75.0 Firefox/75.0
Mozilla/5.0 (Android 8.1.0; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0
Mozilla/5.0 (Android 9; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0
Mozilla/5.0 (BB10; Kbd) AppleWebKit/537.35+ (KHTML, like Gecko) Version/10.3.3.2205 Mobile Safari/537.35+
Mozilla/5.0 (compatible)
Mozilla/5.0 (compatible; inoreader.com; 1 subscribers)
Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/4.0; InfoPath.2; SV1; .NET CLR 2.0.50727; WOW64)
Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)
Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; SV1)
Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)
Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)
Mozilla/5.0 (compatible; TrendsmapResolver/0.1)
Mozilla/5.0 Firefox/26.0
Mozilla/5.0 (iPad; CPU OS 10_3_3 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) CriOS/71.0.3578.89 Mobile/14G60 Safari/602.1
Mozilla/5.0 (iPad; CPU OS 10_3_3 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.0 Mobile/14G60 Safari/602.1
Mozilla/5.0 (iPad; CPU OS 10_3_4 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.0 Mobile/14G61 Safari/602.1
Mozilla/5.0 (iPad; CPU OS 11_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 12_0_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 12_4_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/16G114 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 12_4_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 12_4_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 12_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 12_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 13_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/81.0.4044.124 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.4.1.207.11 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/81.0.4044.124 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.4.2.269.11 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPad; CPU OS 5_1_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B206 Safari/7534.48.3
Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25
Mozilla/5.0 (iPad; CPU OS 9_3_6 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13G37 Safari/601.1
Mozilla/5.0 (iPad; CPU OS 9_3_6 like Mac OS X) AppleWebKit/601.1 (KHTML, like Gecko) CriOS/63.0.3239.73 Mobile/13G37 Safari/601.1.46
Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_4 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) GSA/68.0.234683655 Mobile/14G61 Safari/602.1
Mozilla/5.0 (iPhone; CPU iPhone OS 10_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/10.0 YaBrowser/20.3.2.277.10 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 11_2_5 like Mac OS X) AppleWebKit/604.5.6 (KHTML, like Gecko) Version/11.0 Mobile/15D60 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 11_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0 Mobile/15C114 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 11_3 like Mac OS X) AppleWebKit/604.1.34 (KHTML, like Gecko) CriOS/76.0.3809.123 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 11_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 11_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15G77 YaBrowser/19.5.2.38.10 YaApp_iOS/23.00 YaApp_iOS_Search/23.00 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 11_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 11_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148
Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/16A366 YaBrowser/19.5.2.38.10 YaApp_iOS/31.00 YaApp_iOS_Browser/31.00 Safari/605.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_1_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_1_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_1_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/73.0.3683.68 Mobile/15E148 Safari/605.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/102.0.304944559 Mobile/15E148 Safari/605.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 YaBrowser/19.5.2.38.10 YaApp_iOS/29.10 YaApp_iOS_Browser/29.10 Safari/605.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/81.0.4044.124 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/85.0.274229539 Mobile/15E148 Safari/605.1
Mozilla/5.0 (iPhone; CPU iPhone OS 12_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148
Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/100.0.302345841 Mobile/17A878 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/88.0.281793270 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/17B111 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.4.2.269.10 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/17D50 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 YaBrowser/19.5.2.38.10 YaApp_iOS/28.10 YaApp_iOS_Browser/28.10 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 YaBrowser/19.5.2.38.10 YaApp_iOS/29.10 YaApp_iOS_Browser/29.10 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 YaBrowser/19.5.2.38.10 YaApp_iOS/31.00 YaApp_iOS_Browser/31.00 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/81.0.4044.124 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/105.0.307913796 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/94.0.294702951 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.4 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/19.3.2.156.10 Mobile/15E148 Safari/605.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.3.2.277.10 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.4.1.207.10 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.4.2.269.10 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko)
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/17E262 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/91.1.292041477 Mobile/17E262 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Instagram 142.0.0.22.109 (iPhone9,4; iOS 13_4_1; ru_RU; ru-RU; scale=2.88; 1080x1920; 214888322)
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 YaBrowser/19.5.2.38.10 YaApp_iOS/25.00 YaApp_iOS_Browser/25.00 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 YaBrowser/19.5.2.38.10 YaApp_iOS/31.00 YaApp_iOS_Browser/31.00 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/81.0.4044.124 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/102.0.304944559 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/105.0.307913796 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/107.0.310639584 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/82.1.267240167 Mobile/15E148 Safari/605.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/91.1.292041477 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.4.1.207.10 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 YaBrowser/20.4.2.269.10 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/81.0.4044.124 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 13_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.1 Mobile/15E148 Safari/604.1
Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_6 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13G37 Safari/601.1
Mozilla/5.0 (iPhone; CPU OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/25.1  Mobile/15E148 Safari/605.1.15
Mozilla/5.0 (iPhone; U; CPU iPhone OS 5_1_1 like Mac OS X; en-us) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B206 Safari/7534.48.3 XiaoMi/MiuiBrowser/12.2.3-g
Mozilla/5.0 (Linux; Android 10.0; MI 9T Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; ANA-NX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 DuckDuckGo/5
Mozilla/5.0 (Linux; Android 10; BKL-AL20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; CPH1979) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; ELE-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; GM1903) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; GM1910) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HD1900) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HD1903) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HLK-AL00) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 HuaweiBrowser/10.1.0.300 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1 Build/HONORHRY-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; HRY-LX1T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; HRY-LX1T Build/HONORHRY-LX1T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; HRY-LX1T Build/HONORHRY-LX1T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; JSN-L21 Build/HONORJSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; JSN-L21 Build/HONORJSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; JSN-L21 Build/HONORJSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/9.75 YaSearchBrowser/9.75
Mozilla/5.0 (Linux; Android 10; JSN-L21 Build/HONORJSN-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.7.11.21.arm64
Mozilla/5.0 (Linux; Android 10; LYA-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MAR-LX1A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MAR-LX1A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MAR-LX1H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MAR-LX1M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MAR-LX1M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 OPR/57.2.2830.52651
Mozilla/5.0 (Linux; Android 10; MAR-LX1M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MAR-LX1M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI 8 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI 8 SE) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI 8 SE) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.56 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9 SE) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.56 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 10; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 10; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9T Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9T Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi 9T Pro Build/QKQ1.190825.002) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPT/2.4
Mozilla/5.0 (Linux; Android 10; Mi A2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi A2 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi A2 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi A3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi A3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI MAX 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; MI MAX 3 Build/QKQ1.190910.002) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; MI MAX 3 Build/QKQ1.190910.002) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; Mi MIX 2S) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 OPR/57.2.2830.52651
Mozilla/5.0 (Linux; Android 10; Mi MIX 2S) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 OPR/57.2.2830.52651,gzip(gfe)
Mozilla/5.0 (Linux; Android 10; Mi MIX 2S) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Mi MIX 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Nokia 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Nokia 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe)
Mozilla/5.0 (Linux; Android 10; Nokia 6.1 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; ONEPLUS A6000) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; ONEPLUS A6000 Build/QKQ1.190716.003; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 10; ONEPLUS A6003) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; PCT-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; PCT-L29 Build/HUAWEIPCT-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; PCT-L29 Build/HUAWEIPCT-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/9.85 YaSearchBrowser/9.85
Mozilla/5.0 (Linux; Android 10; Pixel 2 XL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Pixel 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Pixel Build/QP1A.191005.007.A3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; POCOPHONE F1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; POT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; POT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; POT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; POT-LX1 Build/HUAWEIPOT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36 YaApp_Android/10.44 YaSearchBrowser/10.44
Mozilla/5.0 (Linux; Android 10; POT-LX1 Build/HUAWEIPOT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36 YaApp_Android/10.51 YaSearchBrowser/10.51
Mozilla/5.0 (Linux; Android 10; Power 3 Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; Redmi Note 8 Pro Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; Redmi Note 8 Pro Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; RMX1921) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; RMX1971) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A105F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A107F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.0 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A107F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A305FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A305FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A505FM) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A515F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A600FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-A750FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G960F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G965F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G965F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G970F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-N975F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A015F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A105F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36 YaApp_Android/9.20 YaSearchBrowser/9.20
Mozilla/5.0 (Linux; Android 10; SM-A105F Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.99 Mobile Safari/537.36 YandexSearch/10.44 YandexSearchWebView/10.44
Mozilla/5.0 (Linux; Android 10; SM-A205FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A205FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A207F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A305FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A305FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A305FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 10; SM-A305FN Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; SM-A305FN Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.96 Mobile Safari/537.36 YandexSearch/7.90 YandexSearchBrowser/7.90
Mozilla/5.0 (Linux; Android 10; SM-A307FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A307FN Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; SM-A307FN Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 10; SM-A405FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A405FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A405FM Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36 YaApp_Android/9.20 YaSearchBrowser/9.20
Mozilla/5.0 (Linux; Android 10; SM-A405FM Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/79.0.3945.116 Mobile Safari/537.36 GSA/10.95.9.21.arm64
Mozilla/5.0 (Linux; Android 10; SM-A405FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FM Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36 YaApp_Android/9.20 YaSearchBrowser/9.20
Mozilla/5.0 (Linux; Android 10; SM-A505FM Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.8.9.21.arm64
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36,gzip(gfe)
Mozilla/5.0 (Linux; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A505FN Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36 YaApp_Android/9.20 YaSearchBrowser/9.20
Mozilla/5.0 (Linux; Android 10; SM-A505FN Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; SM-A515F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A515F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A515F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A515F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A515F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A515F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 10; SM-A515F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; SM-A515F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; SM-A600FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A600FN Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36 YaApp_Android/9.20 YaSearchBrowser/9.20
Mozilla/5.0 (Linux; Android 10; SM-A605FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A705FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A705FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A715F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A715F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 10; SM-A750F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A750FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A750FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-A750FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe)
Mozilla/5.0 (Linux; Android 10; SM-A750FN Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36 YaApp_Android/10.44 YaSearchBrowser/10.44
Mozilla/5.0 (Linux; Android 10; SM-A805F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; SM-A920F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 10; SM-G770F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G965F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G970F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G980F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-G980F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe)
Mozilla/5.0 (Linux; Android 10; SM-J600F Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-J600F Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm
Mozilla/5.0 (Linux; Android 10; SM-M205FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-N770F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-N9600) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-N960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-N960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SM-N975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SNE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SNE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; SNE-LX1 Build/HUAWEISNE-LX1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.8.9.21.arm64
Mozilla/5.0 (Linux; Android 10; STK-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; STK-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; STK-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; YAL-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; YAL-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 10; YAL-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 10; YAL-L21 Build/HUAWEIYAL-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.186 Mobile Safari/537.36 YaApp_Android/9.85 YaSearchBrowser/9.85
Mozilla/5.0 (Linux; Android 10; YAL-L41) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/537.36 (KHTML, like Gecko; Mediapartners-Google) Chrome/81.0.4044.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/[WEBKIT_VERSION] (KHTML, like Gecko; Mediapartners-Google) Chrome/[CHROME_VERSION] Mobile Safari/[WEBKIT_VERSION]
Mozilla/5.0 (Linux; Android 4.0.4; GT-S7562 Build/IMM76I) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.1.2; Nokia_X Build/JZO54K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Mobile Safari/537.36 OPR/50.3.2426.146156
Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko; googleweblight) Chrome/38.0.1025.166 Mobile Safari/535.19
Mozilla/5.0 (Linux; Android 4.2.2; C2105 Build/15.3.A.1.17) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.94 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.3; ASUS_T00J Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; GT-I9301I) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; GT-I9505) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 YaBrowser/18.11.1.1011.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; HTC Desire 526G dual sim) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; HTC Desire 620G dual sim) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; HUAWEI Y360-U61 Build/HUAWEIY360-U61) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; HUAWEI Y560-U02) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; Ixion XL240 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; Lenovo A319 Build/MocorDroid4.4.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.95 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; LenovoA3300-HV) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; Lenovo A536) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; Lenovo A5500-H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; Lenovo A916) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.2; LG-D170 Build/KOT49I.A1440471450) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.4; Lenovo A6000) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.4; Lenovo P70-t) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.4; SM-J100FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.4; SM-J110H Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.109 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.4; SM-T560) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Safari/537.36
Mozilla/5.0 (Linux; Android 4.4.4; SM-T561 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.1; GT-I9500) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.1; GT-I9500) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 5.0.1; Lenovo TAB 2 A10-70L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.1; Lenovo TAB 2 A10-70L Build/LRX21M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Safari/537.36 OPR/50.3.2426.136976
Mozilla/5.0 (Linux; Android 5.0.1; SAMSUNG GT-I9505 Build/LRX22C) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/2.1 Chrome/34.0.1847.76 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; ASUS_Z00ED Build/LRX22G; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/47.0.2526.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; ASUS_Z010D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; SAMSUNG SM-G530H) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; SM-A300F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; SM-A300F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; SM-G530H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; SM-G530H Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0.2; SM-G530H Build/LRX22G; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.149 Mobile Safari/537.36 GSA/11.1.9.21.arm
Mozilla/5.0 (Linux; Android 5.0.2; SM-T805) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 5.0; Micromax A107 Build/LRX21M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0; SM-G900F Build/LRX21T; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0; SM-N9005) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.0; SM-N9005 Build/LRX21V; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm
Mozilla/5.0 (Linux; Android 5.0; SM-N900) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; ASUS_X014D Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; D2302) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; G550KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; HUAWEI KII-L21 Build/HUAWEIKII-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; Micromax Q415) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; Redmi 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; Redmi 3 Build/LMY47V; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/73.0.3683.90 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; Redmi 3 Build/LMY47V; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.162 Mobile Safari/537.36 GSA/11.4.9.21.arm64
Mozilla/5.0 (Linux; Android 5.1.1; SAMSUNG SM-G531H) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.2 Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SAMSUNG SM-J320F Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/3.5 Chrome/38.0.2125.102 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SAMSUNG SM-J320H Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/3.5 Chrome/38.0.2125.102 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-G531H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J120F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.90 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J120F Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J120H Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J120H Build/LMY47V; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36 GSA/5.10.32.19.arm
Mozilla/5.0 (Linux; Android 5.1.1; SM-J200H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J200H Build/LMY48B; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm
Mozilla/5.0 (Linux; Android 5.1.1; SM-J320F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J320F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J320F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J320F Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J320H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J320H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-J320H Build/LMY47V; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/76.0.3809.132 Mobile Safari/537.36 Instagram 141.0.0.32.118 Android (22/5.1.1; 320dpi; 720x1280; samsung; SM-J320H; j3x3g; sc8830; ru_RU; 214245295)
Mozilla/5.0 (Linux; Android 5.1.1; SM-T280) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-T285) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Safari/537.36
Mozilla/5.0 (Linux; Android 5.1.1; SM-T285) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; HTC Desire 628 dual sim) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; HUAWEI CUN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; HUAWEI CUN-U29 Build/HUAWEICUN-U29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.89 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; HUAWEI TAG-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; Lenovo A1010a20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; Lenovo A1010a20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; Lenovo TB3-710I) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.105 Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; Lenovo TB3-710I) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.111 Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; Lenovo TB3-710I) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; LG-X210) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; LG-X220) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; LYO-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; LYO-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; m3 note) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; m3 note) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; M3s Build/LMY47I; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/44.0.2403.147 Mobile Safari/537.36 YandexSearch/8.60 YandexSearchBrowser/8.60
Mozilla/5.0 (Linux; Android 5.1; PSP3507DUO) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 5.1; XT1032) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; CPH1607 Build/MMB29M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/63.0.3239.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Lenovo P1a42) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Lenovo P2a42) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Lenovo TB2-X30F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; LEX722) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; LG-K220) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; LG-K220) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; LG-K220) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; LG-K220 Build/MXB48T; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.87 Mobile Safari/537.36 GSA/10.94.12.21.arm
Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Mobile Safari/537.36 Chrome-Lighthouse
Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.92 Mobile Safari/537.36 (compatible; Google-AMPHTML)
Mozilla/5.0 (Linux; Android 6.0.1; NX549J Build/MMB29M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 6.0.1; Redmi 3S) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Redmi 4A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Redmi 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36 OPR/56.0.2780.51441
Mozilla/5.0 (Linux; Android 6.0.1; Redmi Note 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Redmi Note 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Redmi Note 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; Redmi Note 4 Build/MMB29M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.85 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-G532F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-G532F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-G532F Build/MMB29T) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/4.2 Chrome/44.0.2403.133 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-G532F Build/MMB29T) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/6.4 Chrome/56.0.2924.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-G532F Build/MMB29T) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.4 Chrome/67.0.3396.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-G900F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-G925F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-J106F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG-SM-J120A Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.162 Mobile Safari/537.36 GSA/11.2.9.21.arm
Mozilla/5.0 (Linux; Android 6.0.1; SAMSUNG SM-T585) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SGP621) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SM-A700FD) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SM-G532F Build/MMB29T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SM-G900F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SM-G920P Build/MMB29K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SM-G920T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; SM-G930V Build/MMB29M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; ZB500KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; ZB501KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0.1; ZTE B2017G) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; BQS-5020 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 6.0; BQS-5020 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/48.0.2564.106 Mobile Safari/537.36 YandexSearch/5.40
Mozilla/5.0 (Linux; Android 6.0; bravis_A506) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; CAM-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; CRO-U00) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; CRO-U00) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.62 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; Lenovo A2016a40) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; Lenovo A2016a40 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.85 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; LEX653) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 6.0; LG-K350) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.90 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; LG-K430) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; LG-X230) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; LG-X240) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; M5 Note) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.0.2878.53230
Mozilla/5.0 (Linux; Android 6.0; M5s) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; M8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; MYA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; MYA-U29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; NEM-AL10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; P2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 OPR/57.2.2830.52651
Mozilla/5.0 (Linux; Android 6.0; P3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; PLK-L01) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; PSP7501DUO Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 YaBrowser/16.2.1.7529.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; PSP7501DUO Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; SENSEIT_A109 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/50.0.2661.86 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; U20 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.147 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 6.0; X9 Mini Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 6.0; ZTE BLADE V0720) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; 5009D_RU Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; A7 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; A7Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; AGS-L09 Build/HUAWEIAGS-L09; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/64.0.3282.137 Safari/537.36 YandexSearch/5.80/apad
Mozilla/5.0 (Linux; Android 7.0;) AppleWebKit/537.36 (KHTML, like Gecko) Mobile Safari/537.36 (compatible; AspiegelBot)
Mozilla/5.0 (Linux; Android 7.0; ASUS_X008D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; ASUS_X008D Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; ASUS_X018D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; B501) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; BG2-U01 Build/HUAWEIBG2-U01) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; BLN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; BQ-5211 Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/62.0.3202.84 Mobile Safari/537.36 YandexSearch/7.21
Mozilla/5.0 (Linux; Android 7.0; BTV-DL09 Build/HUAWEIBEETHOVEN-DL09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 7.0; City Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 Instagram 142.0.0.34.110 Android (24/7.0; 320dpi; 720x1184; myPhone; City; myPhone_City; mt6735; uk_UA; 215464389)
Mozilla/5.0 (Linux; Android 7.0; DLI-TL20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; DLI-TL20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; DLI-TL20 Build/HONORDLI-TL20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.109 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; DLI-TL20 Build/HONORDLI-TL20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 7.0; DLI-TL20 Build/HONORDLI-TL20; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36 YandexSearch/5.80
Mozilla/5.0 (Linux; Android 7.0; FRD-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; FRD-L09 Build/HUAWEIFRD-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 7.0; FRD-L19) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; FS529 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; HUAWEI CAN-L11) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; HUAWEI CAN-L11) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; HUAWEI CAN-L11 Build/HUAWEICAN-L11) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 7.0; HUAWEI VNS-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Ixion M340) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Ixion X150 Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 7.0; JMM-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; K3 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; KOB-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; KOB-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; KOB-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Lenovo K33a42) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; LG-H870DS) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; LG-M250) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; LG-M320) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; M6T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; MEIZU M6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; MI MAX) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; MI MAX) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Neffos C5a) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; NEM-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; NEM-L51) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Optima 1023N 3G TS1186MG Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; PRA-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; PSP3471DUO) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4 Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4 Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-A310F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/8.2 Chrome/63.0.3239.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-A510F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-G920F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-T719 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/8.2 Chrome/63.0.3239.111 Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SLA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-A310F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.89 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-A310F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 7.0; SM-A320F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-A510F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-A510F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-A510F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-A510F Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/72.0.3626.105 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-A520F Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36 YandexSearch/6.45
Mozilla/5.0 (Linux; Android 7.0; SM-G920F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-G920F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-G925F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-G935F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; SM-J530FM Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/56.0.2924.87 Mobile Safari/537.36 GSA/6.13.25.21.arm
Mozilla/5.0 (Linux; Android 7.0; TRT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; TRT-LX1 Build/HUAWEITRT-LX1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm
Mozilla/5.0 (Linux; Android 7.0; VS501) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; X60L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; ZTE BLADE A520 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 7.0; ZTE BLADE A520 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 7.0; ZTE BLADE V0800 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.77 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; ZTE BLADE V0850) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.0; ZTE BLADE V0850 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 7.1.1; ASUS_X017DA) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; CPH1725) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; CPH1729) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; E6633) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; FS8010) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; H4311) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; MI 6 Build/NMF26X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; MI MAX 2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; MI MAX 2 Build/NMF26F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 7.1.1; Moto E (4) Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; NX591J Build/NMF26F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.0.0 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SAMSUNG SM-J510FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SAMSUNG SM-J510H) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SAMSUNG SM-T555) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SM-J250F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SM-J250F Build/NMF26X; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/64.0.3282.137 Mobile Safari/537.36 YandexSearch/8.11 YandexSearchBrowser/8.11
Mozilla/5.0 (Linux; Android 7.1.1; SM-J510FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SM-J510FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SM-J510FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SM-J510FN Build/NMF26X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36 YaApp_Android/10.51 YaSearchBrowser/10.51
Mozilla/5.0 (Linux; Android 7.1.1; SM-J510H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; SM-J510H Build/NMF26X; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/44.0.2403.119 Mobile Safari/537.36 ACHEETAHI/1
Mozilla/5.0 (Linux; Android 7.1.1; SM-T555) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; ZTE BLADE A0620) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; ZTE BLADE A0620) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.1; ZTE BLADE A0620 Build/NMF26F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; 15 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Lenovo K3 Note) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; M6 Note) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; M6 Note Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Meizu M8c) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; MI 5C) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.89 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4A Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.105 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 4X Build/N2G47H; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.89 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5A Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5 Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi 5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi Note 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi Note 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi Note 5A Prime) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi Note 5A Prime) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi Note 5A Prime) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 7.1.2; Redmi Note 5A Prime Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 8.0.0; AGS2-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; ANE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; ASUS_Z01KD) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; ATU-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; ATU-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; ATU-L31 Build/HUAWEIATU-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L29 Build/HONORAUM-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L41) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L41) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L41 Build/HONORAUM-L41) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.51 YaSearchBrowser/10.51
Mozilla/5.0 (Linux; Android 8.0.0; AUM-L41 Build/HONORAUM-L41) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 8.0.0; BAH2-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; BAH2-L09 Build/HUAWEIBAH2-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70/apad YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 8.0.0; BND-AL10 Build/HONORBND-AL10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 8.0.0; F5122) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; G3212) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; G3212) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; G3412) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; LDN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; LDN-L21 Build/HUAWEILDN-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 YandexSearch/10.70 YandexSearchWebView/10.70
Mozilla/5.0 (Linux; Android 8.0.0; Lenovo L38011 Build/OPR1.170623.032) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; LG-H870S) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; LND-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; MI 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; MI 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; MI 5s) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; Moto Z (2)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; Nokia 5.1 Build/O00623) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.109 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; PIC-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; PRA-LA1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; PRA-LA1 Build/HUAWEIPRA-LA1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 8.0.0; PRA-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; PRA-TL10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; PRA-TL10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; RNE-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; RNE-L21 Build/HUAWEIRNE-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-A320F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-A520F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-A520F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.4 Chrome/67.0.3396.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-A720F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-A720F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-A750FN Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/8.2 Chrome/63.0.3239.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-G930F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-G935F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SAMSUNG SM-G955F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-A320F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-A320F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-A320F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.7.11.21.arm
Mozilla/5.0 (Linux; Android 8.0.0; SM-A520F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-A520F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-A520F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-A520F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 8.0.0; SM-A520F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.119 Mobile Safari/537.36 GSA/10.77.9.21.arm64
Mozilla/5.0 (Linux; Android 8.0.0; SM-A520F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 8.0.0; SM-A600FN Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/68.0.3440.91 Mobile Safari/537.36 YandexSearch/7.20
Mozilla/5.0 (Linux; Android 8.0.0; SM-A720F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-A720F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 8.0.0; SM-A730F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-G570F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.8.9.21.arm
Mozilla/5.0 (Linux; Android 8.0.0; SM-G930F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-G930F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-G930F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.8.9.21.arm64
Mozilla/5.0 (Linux; Android 8.0.0; SM-G935F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-G935F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-G935F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; SM-G935F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Mobile Safari/537.36 OPR/53.0.2569.141117
Mozilla/5.0 (Linux; Android 8.0.0; SM-N9500) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0.0; ZE520KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.0; Galaxy Nexus Build/IMM76B; UCBrowser; ru-RU) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 YaBrowser/20.2.6.114 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; 16) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; 16th) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; 5052D_RU) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; ASUS_X00ID) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; B504_Unit Build/O11019) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; BKK-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; BKK-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; BKK-L21 Build/HONORBKK-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36 YaApp_Android/10.51 YaSearchBrowser/10.51
Mozilla/5.0 (Linux; Android 8.1.0; BKK-LX2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; BL150) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; BQ-1085L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; BQ-1085L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; BQ-5302G Build/O11019) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; CPH1909) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; CPH1909 Build/O11019) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DRA-LX2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DRA-LX2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DRA-LX2 Build/HUAWEIDRA-LX2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DRA-LX5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DRA-LX5 Build/HUAWEIDRA-LX5; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22 Build/HONORDUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 YaBrowser/17.9.0.524.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22 Build/HONORDUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22 Build/HONORDUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 8.1.0; DUA-L22 Build/HONORDUA-L22; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 Instagram 141.0.0.32.118 Android (27/8.1.0; 360dpi; 720x1356; HUAWEI/HONOR; DUA-L22; HWDUA-M; mt6739; ru_RU; 214245346)
Mozilla/5.0 (Linux; Android 8.1.0; DUB-AL20 Build/HUAWEIDUB-AL20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36 YaApp_Android/9.75 YaSearchBrowser/9.75
Mozilla/5.0 (Linux; Android 8.1.0; DUB-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; DUB-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; I4312) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; i5014) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; itel A16 Plus RU) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Lenovo L18021) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.62 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Lenovo L38041) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Lenovo L38043) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Lenovo TB-X304L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; LG-M700) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; LG-M700) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; meizu C9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; meizu X8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; MI 8 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Mi Note 3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; MI PAD 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; MI PAD 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; MI PLAY) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; MI PLAY) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Neffos C5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Neffos C5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 8.1.0; Neffos_C9A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; PAR-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 YaBrowser/19.3.3.285.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; POCOPHONE F1 Build/OPM1.171019.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/68.0.3440.91 Mobile Safari/537.36 YandexSearch/7.45 YandexSearchBrowser/7.45
Mozilla/5.0 (Linux; Android 8.1.0; POCOPHONE F1 Build/OPM1.171019.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.8.9.21.arm64
Mozilla/5.0 (Linux; Android 8.1.0; PSP5522DUO) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; PSP5545DUO) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5A Build/OPM1.171019.026; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5 Build/OPM1.171019.026; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.119 Mobile Safari/537.36 GSA/10.97.8.21.arm64
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5 Plus Build/OPM1.171019.019) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.91 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5 Plus Build/OPM1.171019.019) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Mobile Safari/537.36 YaApp_Android/10.30 YaSearchBrowser/10.30
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 5 Plus Build/OPM1.171019.019; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.8.9.21.arm64
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.90 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi 6 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 8.1.0; Redmi Go) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi Go Build/OPM1.171019.026) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi Go Build/OPM1.171019.026) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi Go Build/OPM1.171019.026) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; Redmi Note 6 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SAMSUNG SM-J710F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SAMSUNG SM-T580) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J260F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J260F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J260F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J260F Build/M1AJB) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J415FN Build/M1AJQ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70/apad YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 8.1.0; SM-J415FN Build/M1AJQ; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.111 Mobile Safari/537.36 GSA/11.5.9.21.arm
Mozilla/5.0 (Linux; Android 8.1.0; SM-J710F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J710F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J710F Build/M1AJQ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 8.1.0; SM-J730FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-J730FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-T580) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; SM-T585) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; T8s) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; VIA_G3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; vivo 1716) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.73 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; vivo 1808) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; vivo 1808 Build/O11019; wv) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36 VivoBrowser/5.8.0.10
Mozilla/5.0 (Linux; Android 8.1.0; vivo 1814 Build/O11019) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36 YaApp_Android/8.80 YaSearchBrowser/8.80
Mozilla/5.0 (Linux; Android 8.1.0; vivo 1820) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.116 Mobile Safari/537.36 OPR/55.2.2719.50740
Mozilla/5.0 (Linux; Android 8.1.0; vivo 1820) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; vivo 1820 Build/O11019; wv) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36 VivoBrowser/5.9.1.5
Mozilla/5.0 (Linux; Android 8.1.0; ZB633KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; ZC520KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 8.1.0; ZTE BLADE A530) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9.0; BDF K107H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 9.0; MI 6X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; A5_Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; AMN-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; AMN-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ANE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ANE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ANE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ANE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ANE-LX1 Build/HUAWEIANE-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/83.0.4103.60 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 9; aosp_c106) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 DuckDuckGo/5
Mozilla/5.0 (Linux; Android 9; ASUS_X00TD) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 9; BKL-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BKL-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BLA-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BND-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BND-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BND-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BND-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BND-L21 Build/HONORBND-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 YaBrowser/18.10.1.902.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BQ-5730L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BQ-6040L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; BQ-6040L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; CLT-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 9; COL-L29 Build/HUAWEICOL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 YaBrowser/17.11.0.542.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; COL-L29 Build/HUAWEICOL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; COL-L29 Build/HUAWEICOL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; COL-L29 Build/HUAWEICOL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/9.35 YaSearchBrowser/9.35
Mozilla/5.0 (Linux; Android 9; COR-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; CPH1907) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; CPH1931) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; CPH1931) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; CPH1941) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.0.2878.53230
Mozilla/5.0 (Linux; Android 9; CPH1941 Build/PKQ1.190714.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; CPH1979) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; EML-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; EML-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; F1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; FIG-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; FIG-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; FIG-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; FIG-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; FIG-LX1 Build/HUAWEIFIG-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; FIG-LX1 Build/HUAWEIFIG-L31; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; FIG-LX1 Build/HUAWEIFIG-L31; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; FLA-LX1 Build/HUAWEIFLA-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; G8142) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; G8342 Build/47.2.A.6.30; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/69.0.3497.100 Mobile Safari/537.36 YandexSearch/7.21
Mozilla/5.0 (Linux; Android 9; G8441) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; G8441 Build/47.2.A.10.80; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/72.0.3626.121 Mobile Safari/537.36 YandexSearch/7.21
Mozilla/5.0 (Linux; Android 9; GM 8 d) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; H4413) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Hisense H30 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; HRY-LX1 Build/HONORHRY-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; HRY-LX1 Build/HONORHRY-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 YandexSearch/7.30 YandexSearchBrowser/7.30
Mozilla/5.0 (Linux; Android 9; HRY-LX1T Build/HONORHRY-LX1T; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 YandexSearch/7.80 YandexSearchBrowser/7.80
Mozilla/5.0 (Linux; Android 9; HTC U12+) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; INE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; INE-LX2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; INE-LX2 Build/HUAWEIINE-LX2; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; itel L5503) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-L41) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-LX1 Build/HONORJAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 YaBrowser/17.11.0.542.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JAT-LX1 Build/HONORJAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; JAT-LX1 Build/HONORJAT-LX1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 YandexSearch/7.30 YandexSearchBrowser/7.30
Mozilla/5.0 (Linux; Android 9; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; KSA-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; KSA-LX9 Build/HONORKSA-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 YaBrowser/17.11.0.542.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; KSA-LX9 Build/HONORKSA-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 YaApp_Android/10.44 YaSearchBrowser/10.44
Mozilla/5.0 (Linux; Android 9; KSA-LX9 Build/HONORKSA-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.60 YaSearchBrowser/10.60
Mozilla/5.0 (Linux; Android 9; KSA-LX9 Build/HONORKSA-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; Lenovo L78071) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Lenovo TB-X705L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 9; lineage_c106) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 9; LLD-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; LLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; LLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; LLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; LLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; LLD-L31 Build/HONORLLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; LLD-L31 Build/HONORLLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; LM-G710) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; MAR-LX1H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; MAR-LX1H Build/HUAWEIMAR-L21H; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 YandexSearch/7.80 YandexSearchBrowser/7.80
Mozilla/5.0 (Linux; Android 9; MAR-LX1M Build/HUAWEIMAR-L21MEA; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 YandexSearch/7.30 YandexSearchBrowser/7.30
Mozilla/5.0 (Linux; Android 9; MI 6 Build/PKQ1.190118.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; MI 8 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi 9 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi 9T Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi A1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi A1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.73 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi A1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi A1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi A1 Build/PKQ1.180917.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.8.9.21.arm64
Mozilla/5.0 (Linux; Android 9; Mi A2 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi A2 Lite Build/PKQ1.180917.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 9; Mi A3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Mi A3 Build/PKQ1.190416.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Mi MIX 2 Build/PKQ1.190118.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPT/2.4
Mozilla/5.0 (Linux; Android 9; Mi Note 10 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; moto g(7) power) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; MRD-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; MRD-LX1F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; MRD-LX1F Build/HUAWEIMRD-LX1F; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/69.0.3497.100 Mobile Safari/537.36 YandexSearch/7.30 YandexSearchBrowser/7.30
Mozilla/5.0 (Linux; Android 9; Nokia 2.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Nokia 3.1 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Nokia 3.1 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Nokia 3.1 Plus) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe)
Mozilla/5.0 (Linux; Android 9; Nokia 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ONEPLUS A3003) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 OPR/57.2.2830.52651
Mozilla/5.0 (Linux; Android 9; ONEPLUS A3003) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ONEPLUS A5000 Build/PKQ1.180716.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; ONEPLUS A5010) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ONEPLUS A5010) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ONEPLUS A5010) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; POCOPHONE F1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 6A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 9; Redmi 6A Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.111 Mobile Safari/537.36 GSA/11.9.16.21.arm
Mozilla/5.0 (Linux; Android 9; Redmi 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 6 Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi 6 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.158 Mobile Safari/537.36 OPR/47.3.2249.130976
Mozilla/5.0 (Linux; Android 9; Redmi 7A Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; Redmi 7A Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi 7A Build/PKQ1.190319.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7A Build/PKQ1.190319.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.7.11.21.arm
Mozilla/5.0 (Linux; Android 9; Redmi 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 8A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 8A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 8A Build/PKQ1.190319.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/10.90.16.21.arm
Mozilla/5.0 (Linux; Android 9; Redmi 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi 8 Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi 8 Build/PKQ1.190319.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.99 Mobile Safari/537.36 GSA/10.90.16.21.arm64
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 9; Redmi Note 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 5 Build/PKQ1.180904.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 YandexSearch/7.61 YandexSearchBrowser/7.61
Mozilla/5.0 (Linux; Android 9; Redmi Note 6 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.93 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 6 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 6 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 6 Pro Build/PKQ1.180904.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 6 Pro Build/PKQ1.180904.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 GSA/11.7.11.21.arm64
Mozilla/5.0 (Linux; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi Note 7 Build/PKQ1.180904.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 YandexSearch/7.61 YandexSearchBrowser/7.61
Mozilla/5.0 (Linux; Android 9; Redmi Note 7 Build/PKQ1.180904.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 7 Build/PKQ1.180904.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.99 Mobile Safari/537.36 Instagram 135.0.0.28.119 Android (28/9; 440dpi; 1080x2131; Xiaomi/xiaomi; Redmi Note 7; lavender; qcom; sk_SK; 206670927)
Mozilla/5.0 (Linux; Android 9; Redmi Note 7 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.0.2878.53275
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Build/PKQ1.190616.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Build/PKQ1.190616.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/83.0.4103.60 Mobile Safari/537.36 GSA/11.9.16.21.arm64
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 OPR/57.2.2830.52651
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.158 Mobile Safari/537.36 OPR/47.3.2249.130976
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36 YaApp_Android/10.44 YaSearchBrowser/10.44
Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 YaBrowser/19.6.1.396.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 YaBrowser/19.6.1.396.00 Mobile Safari/537.36,gzip(gfe)
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T Build/PKQ1.190616.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.158 Mobile Safari/537.36 OPR/47.3.2249.130976
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T Build/PKQ1.190616.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi Note 8T Build/PKQ1.190616.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; Redmi S2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; RMX1941) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; RMX1971) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-A105F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-A305FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-A307FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.1 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-A530F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-A605FN) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G950F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G955F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G965F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-J330F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.2 Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-J701F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-J730FM) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.1 Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-J810F Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-T385) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/11.2 Chrome/75.0.3770.143 Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A105F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A105F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A105F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A105F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A105F Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.132 Mobile Safari/537.36 YandexSearch/7.80 YandexSearchBrowser/7.80
Mozilla/5.0 (Linux; Android 9; SM-A107F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A205FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.89 Mobile Safari/537.36 YaApp_Android/10.41 YaSearchBrowser/10.41
Mozilla/5.0 (Linux; Android 9; SM-A205FN Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/75.0.3770.143 Mobile Safari/537.36 YandexSearch/7.80 YandexSearchBrowser/7.80
Mozilla/5.0 (Linux; Android 9; SM-A207F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.116 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A305FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A305FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.103 YaBrowser/18.7.1.595.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A305FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.96 Mobile Safari/537.36 YaApp_Android/9.50 YaSearchBrowser/9.50
Mozilla/5.0 (Linux; Android 9; SM-A307FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A307FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A307FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 9; SM-A405FM Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36 YaApp_Android/9.05 YaSearchBrowser/9.05
Mozilla/5.0 (Linux; Android 9; SM-A405FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FM Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.111 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.103 YaBrowser/18.7.1.595.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A505FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; SM-A505FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; SM-A530F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A530F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A600FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.101 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A600FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.92 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A600FN Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.162 Mobile Safari/537.36 GSA/11.3.9.21.arm
Mozilla/5.0 (Linux; Android 9; SM-A605FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A605FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A605FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; SM-A705FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A705FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36,gzip(gfe)
Mozilla/5.0 (Linux; Android 9; SM-A730F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A730F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A730F Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36 YaApp_Android/10.61 YaSearchBrowser/10.61
Mozilla/5.0 (Linux; Android 9; SM-A750FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A750FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-A920F Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/79.0.3945.93 Mobile Safari/537.36 YandexSearch/7.80 YandexSearchBrowser/7.80
Mozilla/5.0 (Linux; Android 9; SM-G887N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G950F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.99 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G950F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G950F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G950F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.119 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G950F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G950F Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36 YaApp_Android/10.20 YaSearchBrowser/10.20
Mozilla/5.0 (Linux; Android 9; SM-G950N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G955F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G955F Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 YaApp_Android/8.80 YaSearchBrowser/8.80
Mozilla/5.0 (Linux; Android 9; SM-G960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J330F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J330F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J330F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J330L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J400F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J400F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J400F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J400F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J400F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36,gzip(gfe),gzip(gfe)
Mozilla/5.0 (Linux; Android 9; SM-J415FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J530FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J530FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J530FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J600F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J610FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J610FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J701F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J701F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J730FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J730FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-J730FM Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.116 Mobile Safari/537.36 YandexSearch/7.90 YandexSearchBrowser/7.90
Mozilla/5.0 (Linux; Android 9; SM-J810F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-M307FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-N950F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; SM-T515) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Linux; Android 9; SNE-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; STF-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; STF-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; STF-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; STK-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; STK-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; TA-1053) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; TECNO KC8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; TECNO RA8 Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; vivo 1727) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; vivo 1904) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; vivo 1904) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.117 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; vivo 1906) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; vivo 1907) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; VTR-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; VTR-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; YAL-L21 Build/HUAWEIYAL-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.60 Mobile Safari/537.36 YaApp_Android/9.85 YaSearchBrowser/9.85
Mozilla/5.0 (Linux; Android 9; YAL-L41 Build/HUAWEIYAL-L41; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 YandexSearch/7.80 YandexSearchBrowser/7.80
Mozilla/5.0 (Linux; Android 9; ZB602KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ZB602KL Build/PKQ1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36 YaApp_Android/10.70 YaSearchBrowser/10.70
Mozilla/5.0 (Linux; Android 9; ZB631KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ZB631KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ZB633KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ZB633KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ZB633KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ZB633KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; Android 9; ZTE Blade V10 Vita RU) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; ELE-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; HRY-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.6.114.00 Mobile SA/1 Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 YaBrowser/20.3.0.276.00 Mobile SA/1 Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.4.98.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; JSN-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; MAR-LX1M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.1.123.00 (beta) SA/0 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; MI 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.2.117.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; MI 8 Lite) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.127.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; Mi 9 SE) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 YaBrowser/19.12.3.101.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; Mi A3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-A205FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-A307FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.3.92.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.3.92.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-A505FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-A6060) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-G960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaApp_Android/10.63 YaSearchBrowser/10.63 BroPP/1.0 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-G970F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.4.98.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; SM-N975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; STK-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.107.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; STK-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; STK-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.4.98.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 10; YAL-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 6.0.1; Redmi Note 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.4.104.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 6.0.1; Redmi Note 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 6.0; K6000 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 6.0; MYA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.0; BL5000) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.0; HTC One M9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.3.92.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.0; MEIZU M6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.1.1; MI MAX 2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.1.1; MI MAX 2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.1.2; M6 Note) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 7.1.2; Redmi 4X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.6.114.00 Mobile SA/1 Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.0.0; AUM-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.0.0; AUM-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.0.0; AUM-L41) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.0.0; PRA-TL10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.0.0; RNE-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.0.0; SM-A520F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.6.88.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.0.0; SM-A520F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.5.140.00 Mobile SA/1 Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.1.0; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.143 YaBrowser/19.7.2.90.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.1.0; DUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.4.98.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.1.0; DUA-L22) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.1.0; DUB-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.1.0; MI PAD 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.01 Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 8.1.0; Redmi 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; A1 Alpha) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; FIG-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; G8441) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.1.114.00 (beta) SA/0 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Lenovo YT-X705F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.01 Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; LLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Mi 9T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Nokia 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.36.00 (alpha) SA/0 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.127.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Redmi Note 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; Redmi Note 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; SM-A505FM) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.3.92.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; STF-L09) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.6.88.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; VTR-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.107.00 Mobile SA/1 Safari/537.36
Mozilla/5.0 (Linux; arm_64; Android 9; VTR-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 10; HRY-LX1T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.128.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 10; YAL-L21) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.128.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 6.0.1; Lenovo TB-X103F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.01 Safari/537.36
Mozilla/5.0 (Linux; arm; Android 7.0; BQ-5058) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.143 YaBrowser/19.7.5.90.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 7.0; BQru-4072) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 YaBrowser/19.12.1.121.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 8.1.0; Moto G (5S)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 8.1.0; SM-J710F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; COL-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.128.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.127.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; JAT-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.128.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; KSA-LX9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.0.215.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; LLD-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 YaBrowser/20.2.2.128.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; SM-A107F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.237.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; SM-J415FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.1.126.00 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; SM-J415FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux; arm; Android 9; SM-J810F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.4.98.00 SA/1 Mobile Safari/537.36
Mozilla/5.0 (Linux armv6l; Maemo; Opera Mobi/8; U; en-GB; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 11.00
Mozilla/5.0 (Linux armv7l; Maemo; Opera Mobi/4; U; fr; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.1
Mozilla/5.0 (Linux; U; Android 10; en-US; VOG-L29 Build/HUAWEIVOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/13.1.8.1295 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 10; HMA-L29 Build/HUAWEIHMA-L29; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/81.0.4044.138 Mobile Safari/537.36 OPR/47.2.2254.147957
Mozilla/5.0 (Linux; U; Android 10; ru-ru; MI 8 Build/QKQ1.190828.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 10; ru-ru; MI 8 Build/QKQ1.190828.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.2-g
Mozilla/5.0 (Linux; U; Android 10; ru-ru; Mi 9 Lite Build/QKQ1.190828.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 10; ru-ru; Mi 9T Build/QKQ1.190825.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 10; ru-ru; Mi 9T Pro Build/QKQ1.190825.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 10; ru-ru; Mi 9T Pro Build/QKQ1.190825.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.3-g
Mozilla/5.0 (Linux; U; Android 10; ru-ru; POCOPHONE F1 Build/QKQ1.190828.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.7-g
Mozilla/5.0 (Linux; U; Android 10; ru-ru; Redmi Note 8 Pro Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 2.2.1; ru-ru; GT-S5830 Build/FROYO) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
Mozilla/5.0 (Linux; U; Android 4.1.2; ru-ru; LG-E455 Build/JZO54K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30
Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; HUAWEI Y360-U61 Build/HUAWEIY360-U61) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30;
Mozilla/5.0 (Linux; U; Android 4.4.2; ru-ru; SAMSUNG SM-T315/T315XXSBQB6 Build/KOT49H) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30
Mozilla/5.0 (Linux; U; Android 4.4.2; ru-ru; SM-G313HU Build/KOT49H) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30
Mozilla/5.0 (Linux; U; Android 4.4.2; SM-G7102 Build/KOT49H; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36 OPR/47.2.2254.147957
Mozilla/5.0 (Linux; U; Android 5.0.2; ru-ru; Redmi Note 2 Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/42.0.0.0 Mobile Safari/537.36 XiaoMi/MiuiBrowser/2.1.1
Mozilla/5.0 (Linux; U; Android 5.1.1; en-US; SM-J320H Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/13.0.0.1288 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 5.1; en-US; MTC SMART Sprint 4G Build/LMY47D) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.13.5.1209 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 5.1; zh-CN; MZ-m2 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 MZBrowser/7.13.110-2019112819 UWS/2.15.0.4 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 5.1; zh-CN; MZ-M3s Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 MZBrowser/7.13.110-2019112819 UWS/2.15.0.4 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 5.1; zh-CN; MZ-M3s Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 MZBrowser/8.1.310-2020040818 UWS/2.15.0.4 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 6.0.1; ru-ru; Redmi 4 Build/MMB29M) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 6.0.1; ru-ru; Redmi 4 Build/MMB29M) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 6.0; ru-ru; Redmi Note 4 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 6.0; ru-ru; Redmi Note 4X Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 6.0; zh-CN; MZ-M5s Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 MZBrowser/7.1.110-2018072414 UWS/2.11.0.33 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 6.0; zh-CN; MZ-M5s Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 MZBrowser/7.13.110-2019112819 UWS/2.15.0.4 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 6.0; zh-CN; MZ-M5s Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 MZBrowser/8.1.310-2020040818 UWS/2.15.0.4 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 7.0; ru; M5 Note Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.10.0.1163 UCTurbo/1.9.8.900 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 7.0; ru-ru; Redmi Note 4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.128 Mobile Safari/537.36 XiaoMi/MiuiBrowser/10.4.3-g
Mozilla/5.0 (Linux; U; Android 7.0; ru-ru; Redmi Note 4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 7.1.2; ru-ru; Redmi 4A Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.128 Mobile Safari/537.36 XiaoMi/MiuiBrowser/10.4.3-g
Mozilla/5.0 (Linux; U; Android 7.1.2; ru-ru; Redmi 4A Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 7.1.2; ru-ru; Redmi 5 Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 7.1.2; ru-ru; Redmi 5 Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.3-g
Mozilla/5.0 (Linux; U; Android 8.1.0; en-gb; MI 8 Build/OPM1.171019.026) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.128 Mobile Safari/537.36 XiaoMi/MiuiBrowser/9.8.7
Mozilla/5.0 (Linux; U; Android 8.1.0; en-US; M1852 Build/OPM1.171019.026) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/13.1.2.1293 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 8.1.0; en-US; Redmi 5 Plus Build/OPM1.171019.019) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.9.5.1146 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 8.1.0; ru-ru; Redmi 5 Plus Build/OPM1.171019.019) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 8.1.0; ru-ru; Redmi 6A Build/O11019) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 8.1.0; ru-ru; SM-J710F Build/M1AJQ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.128 Mobile Safari/537.36 XiaoMi/Mint Browser/3.3.1
Mozilla/5.0 (Linux; U; Android 8.1.0; zh-CN; EML-AL00 Build/HUAWEIEML-AL00) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 baidu.sogo.uc.UCBrowser/11.9.4.974 UWS/2.13.1.48 Mobile Safari/537.36 AliApp(DingTalk/4.5.11) com.alibaba.android.rimet/10487439 Channel/227200 language/zh-CN
Mozilla/5.0 (Linux; U; Android 9.0; ru-ru; Galaxy\xA0S8 Build/PKQ1.181007.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.128 Mobile Safari/537.36 XiaoMi/MiuiBrowser/10.3.3
Mozilla/5.0 (Linux; U; Android 9; en-US; MI 6 Build/PKQ1.190118.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/13.1.8.1295 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 9; en-US; Redmi Note 5 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/13.1.2.1293 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 9; en-US; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/13.1.8.1295 Mobile Safari/537.36
Mozilla/5.0 (Linux; U; Android 9; ru-ru; MI 6 Build/PKQ1.190118.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.4-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Mi MIX 3 Build/PKQ1.180729.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Mi Note 3 Build/PKQ1.181007.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi 6 Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi 7A Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/11.9.3-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi 7A Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi 7 Build/PKQ1.181021.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.2-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi 8A Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/11.4.3-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi 8 Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 6 Pro Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/11.9.3-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 6 Pro Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.2-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.2-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 7 Build/PKQ1.180904.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.2.3-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 8 Build/PKQ1.190616.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/11.9.3-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 8 Build/PKQ1.190616.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 8 Pro Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; ru-ru; Redmi Note 8T Build/PKQ1.190616.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; uk-ua; Redmi 8A Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Linux; U; Android 9; uk-ua; Redmi 8 Build/PKQ1.190319.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.141 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:38.0) Gecko/20100101 Firefox/38.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) QtWebEngine/5.6.0 Chrome/45.0.2454.101 Safari/537.36 Viber
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 YaBrowser/18.11.1.716 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.1.2 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.119 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:58.0) Gecko/20100101 Firefox/58.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/604.5.6 (KHTML, like Gecko) Version/11.0.3 Safari/604.5.6
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko)
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:57.0) Gecko/20100101 Firefox/57.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:77.0) Gecko/20100101 Firefox/77.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.62 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4404.124 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0.3 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.2442 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/605.1.15 (KHTML, like Gecko)
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:75.0) Gecko/20100101 Firefox/75.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 YaBrowser/19.12.0.769 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 YaBrowser/19.3.0.2489 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.2442 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4143.2 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.1 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.1 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.2 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.4 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Safari/605.1.15
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:78.0) Gecko/20100101 Firefox/78.0
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/537.36 (KHTML, like Gecko, Mediapartners-Google) Chrome/81.0.4044.108 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/[WEBKIT_VERSION] (KHTML, like Gecko, Mediapartners-Google) Chrome/[CHROME_VERSION] Safari/[WEBKIT_VERSION]
Mozilla/5.0 (Macintosh; Intel Mac OS X 1084) AppleWebKit/536.28.10 (KHTML like Gecko) Version/6.0.3 Safari/536.28.10
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:24.0) Gecko/20100101 Firefox/24.0 FirePHP/0.7.4
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A
Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16
Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50
Mozilla/5.0 (Mobile; Windows Phone 8.1; Android 4.0; ARM; Trident/7.0; Touch; rv:11.0; IEMobile/11.0; Microsoft; Lumia 430 Dual SIM) like iPhone OS 7_0_3 Mac OS X AppleWebKit/537 (KHTML, like Gecko) Mobile Safari/537
Mozilla/5.0 (S60; SymbOS; Opera Mobi/1209; U; it; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.1
Mozilla/5.0 (SMART-TV; X11; Linux armv7l) AppleWebKit/537.42 (KHTML, like Gecko) Chromium/25.0.1349.2 Chrome/25.0.1349.2 Safari/537.42
Mozilla/5.0 (Unknown; Linux x86_64) AppleWebKit/538.1 (KHTML, like Gecko) PhantomJS/2.1.1 Safari/538.1
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18362
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18363
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.242 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36 OPR/67.0.3575.137 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 Edg/81.0.416.72
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 10.0; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Windows NT 10.0; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36 Edge/15.15063
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.137
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.75 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.119 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/17.17134
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/18.17763
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.167 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36 OPR/53.0.2907.68
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36 OPR/53.0.2907.99
Mozilla/5.0 (Windows NT 10.0; Win64; x64)AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.62 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.51
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.54
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.64
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.71
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36 OPR/55.0.2994.44
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.75 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18362
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18363
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.96 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3750.0 Iron Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36 OPR/60.0.3255.50962 OPRGX/60.0.3255.50962
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.121 Safari/537.36 Vivaldi/2.8.1664.44
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.4000.0 Iron Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36 OPR/65.0.3467.78
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36 OPR/65.0.3467.38
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36 OPR/66.0.3515.75
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.3.211 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36 OPR/67.0.3575.105
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36 OPR/67.0.3575.130
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36 OPR/67.0.3575.130 (Edition Yx GX)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36 OPR/67.0.3575.78
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36 OPR/67.0.3575.79 (Edition Yx 02)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.1.195 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.238 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36 OPR/67.0.3575.115 (Edition Yx 02)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.158 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36 OPR/67.0.3575.137
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.4150.1 Iron Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.223 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.112
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.112 (Edition Yx GX)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118 (Edition Yx GX)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63 (Edition Yx 05)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.132 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 Edg/81.0.416.72
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 Edg/81.0.416.77
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition utorrent)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx 02)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx 03)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx 05)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.129
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36/vT7tE02E-26
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.142 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.1461 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4200.0 Iron Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.56 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.56 Safari/537.36 Edg/83.0.478.33
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4115.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4139.0 Safari/537.36 Edg/84.0.516.1
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4144.0 Safari/537.36 Edg/84.0.520.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4145.3 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Rocket.Chat/2.17.9 Chrome/78.0.3904.130 Electron/7.1.14 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0 Cyberfox/52.4.1
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:55.0) Gecko/20100101 Firefox/55.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0 Waterfox/56.3
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:64.0) Gecko/20100101 Firefox/64.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:67.0) Gecko/20100101 Firefox/67.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:75.0) Gecko/20100101 Firefox/75.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0
Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 YaBrowser/16.9.1.1192 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 YaBrowser/17.1.0.2034 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.69 Freeu/61.0.3163.69 MRCHROME SOC Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 SputnikBrowser/4.1.2802.0 (GOST) Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 SputnikBrowser/4.1.2810.0 (GOST) Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 SputnikBrowser/4.3.2974.2 (GOST) Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 SputnikBrowser/4.3.2974.2 (GOST) Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36 OPR/52.0.2871.64 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 YaBrowser/18.4.1.871 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.117 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36 OPR/53.0.2907.37 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36 OPR/56.0.3051.116
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.81 Safari/537.36 Maxthon/5.3.8.2000
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 YaBrowser/18.11.1.805 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 YaBrowser/19.3.1.828 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 YaBrowser/19.3.2.177 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.36.3729.169 Elements Browser/1.1.36 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.80 Safari/537.36 OPR/62.0.3331.18 (Edition 360-1)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.2.228 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.3.314 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36 Avast/77.2.2152.121
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 YaBrowser/19.10.2.195 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 YaBrowser/19.10.3.281 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 YaBrowser/19.10.3.281 Yowser/2.5 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36 OPR/65.0.3467.62
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36 OPR/65.0.3467.78 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 YaBrowser/19.12.2.252 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 YaBrowser/19.12.3.320 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 YaBrowser/19.12.4.25 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.1.248 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.4.143 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.4.143 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 YaBrowser/20.3.0.1223 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 YaBrowser/20.3.0.1225 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.1.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.1.197 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.242 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.242 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.16 Safari/537.36 Edg/80.0.361.9
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.114 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Atom/7.0.0.78 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.3.145 (beta) Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.3.145 (beta) Yowser/2.5 Yptp/1.11 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.6.1.43 (beta) Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0
Mozilla/5.0 (Windows NT 10.0; WOW64; Rv:50.0) Gecko/20100101 Firefox/50.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:53.0) Gecko/20100101 Firefox/53.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:66.0) Gecko/20100101 Firefox/66.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 10.0; WOW64; rv:68.0) Gecko/20100101 Thunderbird/68.8.0 Lightning/68.8.0
Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; BOIE9;RURU; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; FSJB; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko,gzip(gfe)
Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.91 Safari/534.30
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.187 Safari/535.1
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 YaBrowser/16.3.0.7146 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36 OPR/36.0.2130.75
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36 OPR/36.0.2130.80
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36 OPR/36.0.2130.80 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 YaBrowser/17.3.0.1785 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.137 YaBrowser/17.4.1.1026 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.137 YaBrowser/17.4.1.758 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.137 YaBrowser/17.4.1.919 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko Firefox/11.0 (via ggpht.com GoogleImageProxy)
Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20120405 Firefox/14.0a1
Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0
Mozilla/5.0 (Windows NT 5.1; rv:47.0) Gecko/20100101 Firefox/47.0
Mozilla/5.0 (Windows NT 5.1; rv:48.0) Gecko/20100101 Firefox/48.0
Mozilla/5.0 (Windows NT 5.1; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 5.1; rv:68.9) Gecko/20100101 Goanna/4.5 Firefox/68.9 Mypal/28.9.1
Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1
Mozilla/5.0 (Windows NT 5.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.137 YaBrowser/17.4.1.1026 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 5.2; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 5.2; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.36 (KHTML like Gecko) Chrome/43.0.2357.81 Safari/537.36
Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.137 YaBrowser/17.4.1.919 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.0; rv:2.0) Gecko/20100101 Firefox/4.0 Opera 12.14
Mozilla/5.0 (Windows NT 6.0; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 YaBrowser/15.9.2403.3043 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36 OPR/37.0.2178.54
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36 OPR/40.0.2308.81 (Edition Yx)
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 SputnikBrowser/4.1.2801.0 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3288.14 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36 Kinza/4.7.2
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36 Kinza/4.8.2
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36 Kinza/4.9.1
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.81 Safari/537.36 Maxthon/5.3.8.2000
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36 OPR/58.0.3135.132
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.90 Atom/4.0.0.141 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36 OPR/63.0.3368.107
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.2.177 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36 OPR/67.0.3575.31 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36 OPR/67.0.3575.130
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 YaBrowser/20.3.0.1223 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36 OPR/67.0.3575.97
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.1.253 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.242 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36 OPR/67.0.3575.115
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Atom/7.0.0.78 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.112
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.112 (Edition Yx GX)
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118 (Edition Yx GX)
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.1458 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Colibri/1.17.0 Chrome/78.0.3904.99 Electron/7.1.1 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; rv:28.0) Gecko/20100101 Firefox/28.0
Mozilla/5.0 (Windows NT 6.1; rv:43.0) Gecko/20100101 Firefox/43.0
Mozilla/5.0 (Windows NT 6.1; rv:48.0) Gecko/20100101 Firefox/48.0
Mozilla/5.0 (Windows NT 6.1; rv:50.0) Gecko/20100101 Firefox/50.0
Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 6.1; rv:53.0) Gecko/20100101 Firefox/53.0
Mozilla/5.0 (Windows NT 6.1; rv:58.0) Gecko/20100101 Firefox/58.0
Mozilla/5.0 (Windows NT 6.1; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (Windows NT 6.1; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 6.1; rv:71.0) Gecko/20100101 Firefox/71.0
Mozilla/5.0 (Windows NT 6.1; rv:74.0) Gecko/20100101 Firefox/74.0
Mozilla/5.0 (Windows NT 6.1; rv:75.0) Gecko/20100101 Firefox/75.0
Mozilla/5.0 (Windows NT 6.1; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 OPR/49.0.2725.64
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36 OPR/53.0.2907.106
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36 OPR/53.0.2907.68
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36 OPR/53.0.2907.99
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.79 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.51
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.54
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.64
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.92 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3650.1 Iron Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.120 Safari/537.36 Vivaldi/2.3.1440.57
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.108 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 OPR/62.0.3331.116
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.80 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.75 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.78 Safari/537.36 Vivaldi/2.8.1664.32
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.92 Safari/537.36 Vivaldi/2.9.1705.38
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36 OPR/67.0.3575.130
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.121 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36 OPR/67.0.3575.97
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36 OPR/67.0.3575.115 (Edition Yx 05)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.158 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36 OPR/67.0.3575.137
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.223 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.223 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.112
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.112 (Edition Yx GX)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63 (Edition Yx 02)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 Edg/81.0.416.77
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx 02)
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.129
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.142 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.1461 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4143.2 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/29.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:61.0) Gecko/20100101 Firefox/61.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:68.9) Gecko/20100101 Goanna/4.5 Firefox/68.9 PaleMoon/28.9.3
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:69.0) Gecko/20100101 Firefox/69.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:75.0) Gecko/20100101 Firefox/75.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0,gzip(gfe)
Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0
Mozilla/5.0 (Windows NT 6.1; Win64; x64; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.1; WOW64; APCPMS=^N201302070257035267484A37ACF0A41BE63F_2702^; Trident/7.0; rv:11.0) like Gecko,gzip(gfe)
Mozilla/5.0 (Windows NT 6.1; WOW64; APCPMS=^N201302070257035267484A37ACF0A41BE63F_2704^; Trident/7.0; rv:11.0) like Gecko,gzip(gfe)
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534+ (KHTML, like Gecko) BingPreview/1.0b
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.59 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 YaBrowser/14.8.1985.12018 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.4.2661.102 Safari/537.36; 360Spider
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36 OPR/40.0.2308.81 (Edition Yx)
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.137
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.98 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 OPR/49.0.2725.64
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 YaBrowser/17.11.0.2191 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/479B76
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.26 Safari/537.36 Core/1.63.4737.400 QQBrowser/10.0.654.400
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36 OPR/52.0.2871.40
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36 OPR/52.0.2871.64
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.52 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.117 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36 OPR/53.0.2907.57 (Edition Yx)
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36 OPR/53.0.2907.99
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.79 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3416.0 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3423.2 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.7 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.25 Safari/537.36 Core/1.70.3722.400 QQBrowser/10.5.3751.400
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 YaBrowser/19.3.0.2855 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 YaBrowser/19.4.0.2134 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.134 Safari/537.36 Vivaldi/2.5.1525.40
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.158 Safari/537.36 Vivaldi/2.5.1525.43
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.143 YaBrowser/19.7.2.455 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 YaBrowser/19.9.0.1343 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 YaBrowser/19.10.1.238 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 YaBrowser/19.12.2.252 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 YaBrowser/19.12.4.25 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.136 YaBrowser/20.2.4.143 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 YaBrowser/20.3.0.1223 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 YaBrowser/20.3.0.1225 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.1.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.242 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.242 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36 OPR/67.0.3575.115 (Edition Campaign 29)
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.4150.1 Iron Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Atom/7.0.0.78 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Campaign 34)
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Campaign 67)
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Yptp/1.21 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.1458 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.3) Gecko/20100101 Firefox/42.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0,gzip(gfe)
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:59.0) Gecko/20100101 Firefox/59.0,gzip(gfe)
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:60.0) Gecko/20100101 Firefox/60.0,gzip(gfe)
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0
Mozilla/5.0 (Windows NT 6.1; WOW64) SkypeUriPreview Preview/0.5
Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; CTL_IE11; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; IEeleven; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko 4tds562
Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko Core/1.53.4620.400 QQBrowser/9.7.13014.400
Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/{engine_version}; rv:{browser_version}) like Gecko
Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2878.86 Safari/537.36
Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; ARM; Trident/7.0; Touch; rv:11.0; WPDesktop; NOKIA; Lumia 530) like Gecko
Mozilla/5.0 (Windows NT 6.2; rv:38.0) Gecko/20100101 Firefox/38.0
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0
Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0
Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/536.5 (KHTML, like Gecko) YaBrowser/1.0.1084.5402 Chrome/19.0.1084.5409 Safari/536.5
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 UBrowser/6.0.1308.1016 Safari/420815
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 UBrowser/6.2.4091.2 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 SE 2.X MetaSr 1.0
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 YaBrowser/18.1.1.839 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36
Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0
Mozilla/5.0 (Windows NT 6.2; WOW64; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36 OPR/52.0.2871.40
Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/19.7.3.172 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; rv:38.0) Gecko/20100101 Firefox/38.0
Mozilla/5.0 (Windows NT 6.3; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.119 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36 OPR/52.0.2871.99
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36 Romash218922romash
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.79 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36 OPR/58.0.3135.68
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36 OPR/67.0.3575.130
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63 (Edition Yx)
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 Edg/81.0.416.77
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36,gzip(gfe)
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4149.2 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:52.9) Gecko/20100101 Goanna/3.4 Firefox/52.9 PaleMoon/27.9.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:64.0) Gecko/20100101 Firefox/64.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:67.0) Gecko/20100101 Firefox/67.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:75.0) Gecko/20100101 Firefox/75.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0
Mozilla/5.0 (Windows NT 6.3; Win64; x64; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/41.0.2272.16 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.104 Safari/537.36 Core/1.53.4620.400 QQBrowser/9.7.13014.400
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.21 Safari/537.36 MMS/1.0.2531.0
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 UBrowser/6.2.3964.2 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.90 Safari/537.36 2345Explorer/9.3.2.17331
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 SputnikBrowser/4.1.2802.0 (GOST) Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36 OPR/52.0.2871.64
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.62 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 YaBrowser/19.12.4.25 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36 OPR/66.0.3515.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.1.253 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.225 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 YaBrowser/20.4.1.304 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 KOOProxyPlugin/1.0.3
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 KOOProxyPlugin/1.0.3,gzip(gfe)
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Yx)
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.197 Yowser/2.5 Yptp/1.23 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.96 YaBrowser/20.4.0.1458 Yowser/2.5 Safari/537.36
Mozilla/5.0 (Windows NT 6.3; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0
Mozilla/5.0 (Windows NT 6.3; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (Windows NT 6.3; WOW64; rv:58.0) Gecko/20100101 Firefox/58.0
Mozilla/5.0 (Windows NT 6.3; WOW64; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; LCJB; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko,gzip(gfe)
Mozilla/5.0 (Windows NT 8.1; Trident/7.0; rv:11.0) like Gecko
Mozilla/5.0 (Windows; U; Windows NT 10.0; en-US; Valve Steam GameOverlay/1589513816; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13
Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-PT; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)
Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208
Mozilla/5.0 (Windows; U; Windows NT 6.1; de; rv:1.9.1.16) Gecko/20101130 AskTbMYC/3.9.1.14019 Firefox/3.5.16
Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; Valve Steam GameOverlay/1589513816; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (Windows; U; Windows NT 6.3; en-US; Valve Steam GameOverlay/1589513816; ) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36
Mozilla/5.0 (X11; CrOS x86_64 12871.102.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.141 Safari/537.36
Mozilla/5.0 (X11; Fedora; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (X11; Fedora; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36
Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0
Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:74.0) Gecko/20100101 Firefox/74.0
Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.66 Safari/537.36
Mozilla/5.0 (X11; Linux i686; rv:2.0.1) Gecko/20100101 Firefox/4.0.1
Mozilla/5.0 (X11; Linux i686; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (X11; Linux i686; rv:68.9) Gecko/20100101 Goanna/4.5 Firefox/68.9 PaleMoon/28.9.3
Mozilla/5.0 (X11; Linux i686 (x86_64)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.0 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.34 Safari/534.24
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/71.0.3578.141 Safari/534.24 XiaoMi/MiuiBrowser/12.1.5-g
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/71.0.3578.141 Safari/534.24 XiaoMi/MiuiBrowser/12.2.1-g
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.75 Safari/537.36 Google Favicon
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.119 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.81 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36 Chrome-Lighthouse
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.5.90.01 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.141 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/58.1.2878.53288
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.56 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/80.0.3987.149 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) QtWebEngine/5.14.2 Chrome/77.0.3865.129 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) snap Chromium/81.0.4044.138 Chrome/81.0.4044.138 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/80.0.3987.163 Chrome/80.0.3987.163 Safari/537.36
Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0
Mozilla/5.0 (X11; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0
Mozilla/5.0 (X11; Linux x86_64; rv:58.0) Gecko/20100101 Firefox/58.0
Mozilla/5.0 (X11; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/60.0
Mozilla/5.0 (X11; Linux x86_64; rv:68.0) Gecko/20100101 Firefox/68.0
Mozilla/5.0 (X11; Linux x86_64; rv:68.9) Gecko/20100101 Goanna/4.5 Firefox/68.9 PaleMoon/28.9.2
Mozilla/5.0 (X11; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (X11; Linux x86_64; rv:77.0) Gecko/20100101 Firefox/77.0
Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0
Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:65.0) Gecko/20100101 Firefox/65.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:66.0) Gecko/20100101 Firefox/66.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:72.0) Gecko/20100101 Firefox/72.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:74.0) Gecko/20100101 Firefox/74.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:75.0) Gecko/20100101 Firefox/75.0
Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0
Mozilla/5.0 (X11; U; Linux i686; cs-CZ; rv:1.9.1.6) Gecko/20100107 Fedora/3.5.6-1.fc12 Firefox/52.7.0
Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.11) Gecko/20101013 Ubuntu/10.04 (lucid) Firefox/3.6.11
Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.0.3) Gecko/20060523 Ubuntu/dapper Firefox/52.4.1
Mozilla/5.0 (X11; U; U; Linux x86_64; ru-ru) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36 Puffin/8.3.0.41446AP
Mozilla/5.0 zgrab/0.x
NokiaC3-00/5.0 (08.70) Profile/MIDP-2.1 Configuration/CLDC-1.1 UCWEB/2.0 (Java; U; MIDP-2.0; ru; nokiac3-00) U2/1.0.0 UCBrowser/8.9.0.251 U2/1.0.0 Mobile UNTRUSTED/1.0
Opera/12.02 (Android 4.1; Linux; Opera Mobi/ADR-1111101157; U; en-US) Presto/2.9.201 Version/12.02
Opera/8.75 (Windows NT 6.0) Presto/2.12.322 Version/12.14
Opera/9.80 (Android 2.2.1; Linux; Opera Mobi/ADR-1107051709; U; pl) Presto/2.8.149 Version/11.10
Opera/9.80 (Android 2.2; Linux; Opera Mobi/8745; U; en) Presto/2.7.60 Version/10.5
Opera/9.80 (Android 2.2;;; Linux; Opera Mobi/ADR-1012291359; U; en) Presto/2.7.60 Version/10.5
Opera/9.80 (Android 2.2; Opera Mobi/-2118645896; U; pl) Presto/2.7.60 Version/10.5
Opera/9.80 (Android 2.3.4; Linux; Opera Mobi/build-1107180945; U; en-GB) Presto/2.8.149 Version/11.10
Opera/9.80 (Android; Linux; Opera Mobi/27; U; en) Presto/2.4.18 Version/10.00
Opera/9.80 (Android; Linux; Opera Mobi/ADR-1011151731; U; de) Presto/2.5.28 Version/10.1
Opera/9.80 (Android; Linux; Opera Mobi/ADR-1012211514; U; en) Presto/2.6.35 Version/10.1
Opera/9.80 (Android; Linux; Opera Mobi/ADR-1012221546; U; pl) Presto/2.7.60 Version/10.5
Opera/9.80 (Android; Linux; Opera Mobi/ADR-1012272315; U; pl) Presto/2.7.60 Version/10.5
Opera/9.80 (Android; Opera Mini/27.0.2254/174.96; U; ru) Presto/2.12.423 Version/12.16
Opera/9.80 (Android; Opera Mini/47.2.2254/174.96; U; ru) Presto/2.12.423 Version/12.16
Opera/9.80 (Linux i686; Opera Mobi/1038; U; en) Presto/2.5.24 Version/10.00
Opera/9.80 (Linux i686; Opera Mobi/1040; U; en) Presto/2.5.24 Version/10.00
Opera/9.80 (Macintosh; Intel Mac OS X; Opera Mobi/27; U; en) Presto/2.4.18 Version/10.00
Opera/9.80 (Macintosh; Intel Mac OS X; Opera Mobi/3730; U; en) Presto/2.4.18 Version/10.00
Opera/9.80 (S60; SymbOS; Opera Mobi/1181; U; en-GB) Presto/2.5.28 Version/10.1
Opera/9.80 (S60; SymbOS; Opera Mobi/1209; U; fr) Presto/2.5.28 Version/10.1
Opera/9.80 (S60; SymbOS; Opera Mobi/447; U; en) Presto/2.4.18 Version/10.00
Opera/9.80 (S60; SymbOS; Opera Mobi/498; U; sv) Presto/2.4.18 Version/10.00
Opera/9.80 (S60; SymbOS; Opera Mobi/SYB-1103211396; U; es-LA) Presto/2.7.81 Version/11.00
Opera/9.80 (S60; SymbOS; Opera Mobi/SYB-1104061449; U; da) Presto/2.7.81 Version/11.00
Opera/9.80 (Windows NT 6.0; Opera Mobi/49; U; en) Presto/2.4.18 Version/10.00
Opera/9.80 (Windows NT 6.1) Presto/2.12.388 Version/12.15
Opera/9.80 (Windows NT 6.2; WOW64) Presto/2.12.388 Version/12.16";

        $list = explode("\n", $list);
        return array_map(function($val) { return trim($val); }, $list);
    }
}
