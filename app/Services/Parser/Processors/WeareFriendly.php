<?php

namespace App\Services\Parser\Processors;

class WeareFriendly implements IProcessor
{
    /*
     * @param string $post
     * @return array
     */
    public function parse(string $post): array
    {
        if (!substr_count($post, '5W USB Power Adapter (MD813) - 8$')) {
            //return [];
        }

        $products = [];

        $lines = explode("\n", $post);
        //dump($lines);
        $indexesEmpty = arr_indexes_value($lines, "");
        //dump($indexesEmpty);
        //https://prnt.sc/st793g
        if (arr_correct_diff($indexesEmpty) && !telegram_post_is_inline($lines)) {

            $diff = $indexesEmpty[1] - $indexesEmpty[0];
            $groupData = [];
            $j = 0;
            foreach ($lines as $k => $val) {
                dump($val);
                if (in_array($k, $indexesEmpty)) {
                    $j++;
                }
                if ($val !== '') {
                    $groupData[$j][] = strip_tags($val);
                }
            }
            foreach ($groupData as $attrs) {
                /* $attrs
                array:3 [
                  0 => "�iMac 21 late2015 "
                  1 => "MK442 CUSTOM 870$"
                  2 => "i5 8gb  256ssd"
                ] */
                if (count($attrs) === $diff - 1) {
                    $productString = [];
                    $price = 0;
                    foreach ($attrs as $attr) {
                        if (preg_match('/\d+\$/', $attr, $match)) {
                            $attr = str_replace($match[0], '', $attr);
                            $price = (int)$match[0];
                        }
                        $productString[] = $attr;
                    }
                    $products[] = [
                        'price' => $price,
                        'title' => str_replace('  ', ' ', implode(' / ', $productString)),
                    ];
                }
            }
            return $products;
        }

        //https://prnt.sc/st8mtr
        $parts = explode(' ', strip_tags($lines[0]));
        if ($parts[0] === 'Used') {
            unset($lines[0]);
        }

        foreach ($lines as $line) {
            if (preg_match('/\d+\$/', $line, $match)) {
                $title = str_replace(' - ' . $match[0], '', $line);
                $title = str_replace($match[0], '', $title);
                $title = trim($title);

                $products[] = [
                    'price' => (int) $match[0],
                    'title' => $title,
                ];
            }
        }
        dump($products);

        //die();
        return $products;
    }
}
