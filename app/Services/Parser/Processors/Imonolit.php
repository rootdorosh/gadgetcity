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

        /*
        print('<pre>');
        print_r($lines);
        print('</pre>');
        */

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

            // 2019 128gb (MVFH2) 850$
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
            }
        }
        //dd($products);
        //die();


        //die();
        return $products;
    }
}
