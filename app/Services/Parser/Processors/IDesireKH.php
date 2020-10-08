<?php

namespace App\Services\Parser\Processors;

class iDesireKH implements IProcessor
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
            if (preg_match('/\s([0-9]{1,10}\$)/', $line, $match)) {
                $price = (int)$match[0];
                $title = str_replace(' - ' . $match[0], '', $line);
                $title = trim(str_replace($match[0], '', $title));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            }
        }

        return $products;
    }
}
