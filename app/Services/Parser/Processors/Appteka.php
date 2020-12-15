<?php

namespace App\Services\Parser\Processors;

use App\Modules\Product\Models\ProviderLog;

class Appteka implements IProcessor
{
    use GradeTrait;

    /**
     * @param string $post
     * @param array $params
     * @return array
     */
    public function parse(string $post, array $params = []): array
    {
        $products = [];

        $lines = array_map(function ($value) {
            $value = preg_replace('/[[:^print:]]/', '', $value);
            $value = str_replace(['<br />', '<br/>', '<br>', "\r", "\t"], '', $value);
            $value = trim($value);
            $value = trim($value);
            return $value;
        }, explode("\n", $post));

        $groupTitle = null;


        // title 1510$ || title 1510 $
        foreach ($lines as $line) {
            //$line = '11 Pro max 64 gold space green  (A-) 860-870$';
            //$line = '11 Pro max 256 green space gold (А-) 950-970$';
            //$line = 'Used 11 Pro max 64 space/green (A/A-) 860$/820$ (акб 94+)';
            echo $line . "\n";

            $line = strip_tags($line);
            $line = trim($line);

            if (substr_count($line, 'MMGF2')) {
                //dump($line);
            }

            if (substr($line, -2) === ' $') {
                $line = rtrim($line, ' $') . '$';
            }

            if ($itemsByGradePrice = $this->getSplitGradePrice($line)) {
                if (count($itemsByGradePrice) === 1) {
                    $itemsByGradePrice[0]['title'] = str_replace('(-)', '(A-)', $itemsByGradePrice[0]['title']);
                    $itemsByGradePrice[0]['title'] = str_replace('(+)', '(A+)', $itemsByGradePrice[0]['title']);
                    $itemsByGradePrice[0]['title'] = str_replace('()', '(A)', $itemsByGradePrice[0]['title']);
                }

                $products = $itemsByGradePrice;

                //MacBook Pro 13\" 2015 MF841 /i5/8/512gb, 352ц (A-) 700$
            } else if (preg_match('/(\)|\s|\/|\-)([0-9]{1,10}\$)/', $line, $match)) {
                $price = (int)$match[2];
                $title = trim(str_replace($match[0], '', $line));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            //MacBook Pro 13\" 2015 MF841 /i5/8/512gb, 352ц (A-) 700
            } elseif (preg_match('/(\)|\s|\/|\-)([0-9]{2,10})$/', $line, $match)) {
                $price = (int)$match[2];
                $title = trim(str_replace($match[0], '', $line));
                $products[] = [
                    'title' => $title,
                    'price' => $price,
                ];
            } else {
                $params['content'] = $line;
                ProviderLog::add($params);
            }
        }

        return $products;
    }
}
