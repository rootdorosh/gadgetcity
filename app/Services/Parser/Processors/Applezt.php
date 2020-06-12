<?php

namespace App\Services\Parser\Processors;

class Applezt implements IProcessor
{
    /*
     * @param string $post
     * @return array
     */
    public function parse(string $post): array
    {
        if (!substr_count($post, 'грн')) {
            //return [];
        }

        $products = [];
        $lines = explode("\n", $post);
        //dd($lines);


        foreach ($lines as $line) {
            if (substr_count($line, 'грн') && !substr_count($line, '$')) {
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
            }
        }

        dump($products);

        return $products;
    }
}
