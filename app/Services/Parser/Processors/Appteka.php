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
            $line = 'X 256 space silver (A/A-) 495$/470$<br />';
            $line = strip_tags($line);
            $line = trim($line);

            if (substr($line, -2) === ' $') {
                $line = rtrim($line, ' $') . '$';
            }

            if (substr_count($line, 'X 256 space silver')) {
                dd($line);
            }

            if ($itemsByGradePrice = $this->getSplitGradePrice($line)) {
                $products = $itemsByGradePrice;

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
            } else {
                $params['content'] = $line;
                ProviderLog::add($params);
            }

        }

        //dd($products);

        return $products;
    }
}
