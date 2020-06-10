<?php

namespace App\Services\Parser\Processors;

class Imonolit implements IProcessor
{
    /*
     * @param string $post
     * @return array
     */
    public function parse(string $post): array
    {
        $products = [];
        $lines = explode("\n", $post);

        $groupTitle = null;
        foreach ($lines as $line) {
            $line = str_replace('<br />', '', $line);
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // ——-MacBook Pro 13”——-
            if (preg_match('/\—\—\-(.*?)\—\—\-/', $line, $match)) {
                $groupTitle = $match[1];

            // title 850$
            } else if (preg_match('/([0-9]{1,10})\$/', $line, $match)) {
                $title = str_replace($match[0], '', $line);
                if ($groupTitle) {
                    $title  = $groupTitle . ' ' . $title;
                }

                $title = trim($title);
                if ($title !== '') {
                    $products[] = [
                        'title' => $title,
                        'price' => $match[1],
                    ];
                }

                //dd($match);
            // title 1 190,00 USD
            } else if (preg_match('/([0-9]{1,2}\s[0-9]{1,10}\,00\sUSD)/', $line, $match)) {
                $title = str_replace($match[0], '', $line);
                $title = trim($title);

                $price = str_replace(",00 USD", "", $match[0]);
                $price = str_replace(' ', '', $price);

                if ($title !== '') {
                    $products[] = [
                        'title' => $title,
                        'price' => $price,
                    ];
                }
            // title 190,00 USD
            } else if (preg_match('/([0-9]{1,10}\,00\sUSD)/', $line, $match)) {
                $title = str_replace($match[0], '', $line);
                $title = trim($title);

                $price = str_replace(",00 USD", "", $match[0]);

                if ($title !== '') {
                    $products[] = [
                        'title' => $title,
                        'price' => $price,
                    ];
                }
            }
        }
        //dd($products);
        //die();


        //die();
        return $products;
    }
}
