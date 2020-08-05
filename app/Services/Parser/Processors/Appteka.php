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


        // title 1510$ || title 1510 $
        foreach ($lines as $line) {
            $line = trim($line);

            if (substr($line, -2) === ' $') {
                $line = rtrim($line, ' $') . '$';
            }

            //$line = "11 pro max 256 gold green space (A)";
            //dump($line);

            if (preg_match('/(\)|\s|\/|\-)([0-9]{1,10}\$)/', $line, $match)) {
                $price = (int)$match[2];
                $title = trim(str_replace($match[0], '', $line));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            } elseif (preg_match('/(\)|\s|\/|\-)([0-9]{2,10})/', $line, $match)) {
                $price = (int)$match[2];
                $title = trim(str_replace($match[0], '', $line));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            }

        }

        return $products;
    }
}
