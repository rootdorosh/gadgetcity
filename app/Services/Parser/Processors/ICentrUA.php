<?php

namespace App\Services\Parser\Processors;

use App\Modules\Product\Models\ProviderLog;

class ICentrUA implements IProcessor
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
        $lines = explode("\n", $post);

        $groupTitle = null;
        foreach ($lines as $line) {
            //$line = '◾️7 32gb rose/matt 180$ A/A-<br />';
            if (substr_count($line, 'rose/matte')) {
                //echo $line . "\n";
            }

            $line = str_replace('<br />', '', $line);
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if ($itemsByGradePrice = $this->getSplitGradePrice($line)) {
                $products = $itemsByGradePrice;

            // ——-MacBook Pro 13”——-
            } elseif (preg_match('/\—\—\-(.*?)\—\—\-/', $line, $match)) {
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
            } else {
                $params['content'] = $line;
                ProviderLog::add($params);
            }
        }

        //dd($products);

        return $products;
    }
}
