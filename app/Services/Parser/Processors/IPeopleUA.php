<?php

namespace App\Services\Parser\Processors;

use App\Modules\Product\Models\ProviderLog;

class IPeopleUA implements IProcessor
{
    /**
     * @param string $post
     * @param array $params
     * @return array
     */
    public function parse(string $post, array $params = []): array
    {
        $products = [];
        $lines = explode("\n", $post);

        //dd($lines);

        $groupTitle = null;

        // title 1510$ || title - 1510$
        foreach ($lines as $line) {
            //$line = "MacBook Pro 15' <strong>2015</strong> (MJLQ2) i7/16/256 - 990$ A/A+<br />";
            //$line = 'SE 32  - 115 <br />';
            //$line = 'XR 64  - 430';
            $line = str_tg_clean($line);

            $stopWords = ['<a href=', 'ipeopleDima', '0962099009'];
            $hasStopWord = false;
            foreach ($stopWords as $stopWord) {
                if (substr_count($line, $stopWord)) {
                    $params['content'] = $line;
                    ProviderLog::add($params);

                    $hasStopWord = true;
                    continue;
                }
            }
            if ($hasStopWord) {
                continue;
            }

            if (preg_match('/(\s|\-)([0-9]{1,10}\$)/', $line, $match)) {
                $price = (int)$match[0];
                $title = str_replace(' - ' . $match[0], '', $line);
                $title = trim(str_replace($match[0], '', $title));
                $title = trim($title, ' -');
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
                // iPad Pro 11 WiFi 64Gb - 670
            } elseif (preg_match('/(\s\-\s)([0-9]{1,10})$/', $line, $match)) {
                //dump($line);
                //dump($match);
                $price = $match[2];
                if ($price > 10000) {
                    $params['content'] = $line;
                    ProviderLog::add($params);
                    continue;
                }

                $title = rtrim($line, $match[0]);
                $title = trim($title);

                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];

                //dump($products);
            } elseif (preg_match('/(\s|\-)([0-9]{1,10})$/', $line, $match)) {

                $price = (int)$match[1];
                if ($price > 10000) {
                    $params['content'] = $line;
                    ProviderLog::add($params);
                    continue;
                }

                $title = rtrim($line, $match[1]);
                $title = trim($title);
                $title = rtrim($title, '-');
                $title = trim($title);

                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];

            // iPad Pro 11 WiFi 64Gb - 670 New MDM
            } elseif (preg_match('/(\-\s)([0-9]{1,10})(\s)/', $line, $match)) {

                $price = (int)$match[2];
                if ($price > 10000) {
                    $params['content'] = $line;
                    ProviderLog::add($params);
                    continue;
                }

                $title = rtrim($line, $match[0]);
                $title = trim($title);

                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            } else {
                $params['content'] = $line;
                ProviderLog::add($params);
            }
        }

        return $products;
    }
}
