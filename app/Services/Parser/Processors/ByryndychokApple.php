<?php

namespace App\Services\Parser\Processors;

use App\Modules\Product\Models\ProviderLog;

class ByryndychokApple implements IProcessor
{
    /**
     * @param string $post
     * @param array $params
     * @return array
     */
    public function parse(string $post, array $params = []): array
    {
        if (!substr_count($post, 'грн')) {
            //return [];
        }

        $products = [];
        $lines = explode("\n", $post);
        //dd($lines);


        foreach ($lines as $line) {
            $stopWords = ['пишите', '067 537 62'];
            $continue = false;
            foreach ($stopWords as $stopWord) {
                if (substr_count($line, $stopWord)) {
                    $continue = true;
                }
            }
            if ($continue) {
                continue;
            }

            if (preg_match('/([0-9]{1,10}\$)/', $line, $match)) {
                $price = (int)$match[0];
                $title = str_replace(' - ' . $match[0], '', $line);
                $title = trim(str_replace($match[0], '', $title));
                $title = trim($title, ' -');
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            } elseif (preg_match('/\s([0-9]{1,10})$/', $line, $match)) {
                $price = (int)$match[0];
                $title = trim(trim($line, $match[0]));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];

            // title 194,00
            } elseif (preg_match('/\s([0-9]{1,10}\,[0-9]{2})$/', $line, $match)) {
                $price = (int)str_replace(',', '.', $match[0]);
                $title = trim(trim($line, $match[0]));
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
