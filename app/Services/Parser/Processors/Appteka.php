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

            //$line = 'Xs max 64 silver (A/ A-) 620$/600$';

            $replaceGrade = [
                '/ A)' => '/A)',
                '/ A+)' => '/A+)',
                '/ A-)' => '/A-)',
                '(A /' => '(A/',
                '(A+ /' => '(A+/',
                '(A- /' => '(A-/',
                'A )' => 'A)',
                'A+ )' => 'A+)',
                'A- )' => 'A-)',
            ];
            dump($line);
            $line = str_replace(array_keys($replaceGrade), array_values($replaceGrade), $line);

            // 8 64 space/gold/silver (A/A-) 255$/245$
            if (preg_match('/(\)|\s|\/|\-)([0-9]{1,10}\$)\/([0-9]{1,10}\$)/', $line, $match)) {
                $price1 = (int) $match[2];
                $price2 = (int) $match[3];
                $line = trim(str_replace($match[0], '', $line));

                preg_match('/(A|A\+|A\-)\/(A|A\+|A\-)/', $line, $matchGrade);

                if (isset($matchGrade[2])) {

                    $titleGrade1 = str_replace($matchGrade[0], '(' . $matchGrade[1] . ')', $line);
                    $titleGrade2 = str_replace($matchGrade[0], '(' . $matchGrade[2] . ')', $line);

                    $products[] = [
                        'price' => $price1,
                        'title' => $titleGrade1,
                    ];

                    $products[] = [
                        'price' => $price2,
                        'title' => $titleGrade2,
                    ];
                } else {
                    $products[] = [
                        'title' => $line,
                        'price' => $price1,
                    ];
                }

                //dd($products);

            } else if (preg_match('/(\)|\s|\/|\-)([0-9]{1,10}\$)/', $line, $match)) {
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
