<?php

namespace App\Services\Parser\Processors;

class ICentrUA implements IProcessor
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

            if (substr_count($line, 'XSM 256 Gray/Silver')) {
                dd($line);
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
            // XSM 256 Gray/Silver  950-1030$
            } else if (preg_match('/-{1,10}\$/', $line, $match)) {
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
            }
        }
        //dd($products);
        //die();


        //die();
        return $products;
    }
}
