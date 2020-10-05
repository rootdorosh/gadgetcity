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
            //$line = 'Apple MacBook Pro 13" 512Gb Touch Bar Space Gray (FR9R2LL/A) 2018  1 400,00<br />';
            //$line = 'Apple iPhone 8 256Gb Gold Used Grade A        300';
            //$line = 'Apple iPhone 11 Pro Max 256Gb Gold Used Grade A-  1 055,00';
            //$line = 'Apple iPhone 11 Pro 64Gb Space Gray Used Grade A  875';
            //$line = 'Apple MacBook Pro 13\" 256Gb Touch Bar Space Gray (MV962/5V962) 2019  1 310,00<br />';
            //$line = 'Apple iPhone XR 128Gb Blue  695<br />';

            $line = str_replace('\\"', '"', $line);
            $line = str_replace(' $<br />', '$<br />', $line);
            $line = strip_tags($line);
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
            // title  1 400,00
            } else if (preg_match('/([0-9]{1,2}\s[0-9]{1,10}\,00)/', $line, $match)) {
                $title = str_replace($match[0], '', $line);
                $title = trim($title);

                $price = str_replace(",00", "", $match[0]);
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
            // title        300
            } else if (
                !substr_count($line, 'USD') &&
                !substr_count($line, '$')
            ) {
                $parts = explode(' ', $line);
                if (count($parts) > 1 && is_numeric(end($parts))) {
                    $price = end($parts);
                    unset($parts[count($parts)-1]);
                    $title = implode(' ', $parts);
                    $title = trim($title);

                    $products[] = [
                        'title' => $title,
                        'price' => $price,
                    ];
                    //dd($products);
                }
            }
        }
        //dd($products);
        //die();


        //die();
        return $products;
    }
}
