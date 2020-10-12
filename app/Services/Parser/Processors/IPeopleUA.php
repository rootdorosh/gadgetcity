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
            $line = str_replace('@ipeopleDima (Діма/0962099009)', '', $line);
            if (preg_match('/\s([0-9]{1,10}\$)/', $line, $match)) {
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
                if ($price > 10000) {
                    continue;
                }

                $title = str_replace(' - ' . $match[0], '', $line);
                $title = trim(str_replace($match[0], '', $title));
                $title = trim($title, ' -');
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
