<?php

namespace App\Services\Parser\Processors;

class Appteka implements IProcessor
{
    /*
     * @param string $post
     * @return array
     */
    public function parse(string $post): array
    {
        $products = [];
        $lines = explode("\n", $post);

        //dd($lines);

        $groupTitle = null;


        // title 1510$
        foreach ($lines as $line) {
            if (preg_match('/\s([0-9]{1,10}\$)/', $line, $match)) {
                $price = (int)$match[0];
                $title = trim(str_replace($match[0], '', $line));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            } elseif (preg_match('/\s([0-9]{2,10})/', $line, $match)) {
                $price = (int)$match[0];
                $title = trim(str_replace($match[0], '', $line));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            }
        }

        dump($products);

        return $products;
    }
}
