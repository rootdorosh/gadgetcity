<?php

namespace App\Services\Parser\Processors;

use App\Modules\Product\Models\ProviderLog;

class SBSLviv implements IProcessor
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

        foreach ($lines as $line) {
            $line = str_tg_clean($line);

            $stopWords = [];
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

            /*
            if (preg_match('/(\s|\-)([0-9]{1,10})$/', $line, $match)) {

                $price = (int)$match[2];
                $title = str_replace(' - ' . $match[0], '', $line);
                $title = trim(str_replace($match[0], '', $title));
                $title = trim($title, ' -');
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];

            } elseif (preg_match('/([0-9]{1,10})\$/', $line, $match)) {
                $price = (int)$match[1];
                $title = str_replace(' - ' . $match[0], '', $line);
                $title = trim(str_replace($match[0], '', $title));
                $title = trim($title, ' -');
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            */

            if (false) {

            } else {
                $params['content'] = $line;
                ProviderLog::add($params);
            }
        }

        //dd($products);

        return $products;
    }
}
