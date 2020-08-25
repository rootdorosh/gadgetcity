<?php

namespace App\Services\Parser\Processors;

trait GradeTrait
{
    // 8 64 space/gold/silver (A/A-) 255$/245$
    // 8 64 space/gold/silver (A/A-) 255/245$
    // 8 64 space/gold/silver 255$/245$ (A/A-)
    // 8 64 space/gold/silver 255/245$ (A/A-)

    /**
     * @param string $line
     * @return array|null
     */
    public function getSplitGradePrice(string $line):? array
    {
        //$line = 'Xr 64gb red/space/coral/blue/red 500$/480$ (А/A-)';
        //$line = 'Xr 64gb red/space/coral/blue/red 500/480$ (А/A-)';

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
            '/ А)' => '/А)',
            '/ А+)' => '/А+)',
            '/ А-)' => '/А-)',
            '(А /' => '(А/',
            '(А+ /' => '(А+/',
            '(А- /' => '(А-/',
            'А )' => 'А)',
            'А+ )' => 'А+)',
            'А- )' => 'А-)',
        ];
        $line = str_replace(array_keys($replaceGrade), array_values($replaceGrade), $line);
        $gradeValues = ['А', 'А+', 'А-', 'A', 'A+', 'A-'];
        $gradeVariants = [];
        foreach ($gradeValues as $one) {
            foreach ($gradeValues as $two) {
                $gradeVariants[] = sprintf('(%s/%s)', $one, $two);
            }
        }

        if (preg_match('/(\)|\s|\/|\-)([0-9]{1,10})\/([0-9]{1,10}\$)/', $line, $match) ||
            preg_match('/(\)|\s|\/|\-)([0-9]{1,10}\$)\/([0-9]{1,10}\$)/', $line, $match)
        ) {
            $products = [];

            $price1 = (int)$match[2];
            $price2 = (int)$match[3];
            $line = trim(str_replace($match[0], '', $line));

            foreach ($gradeVariants as $gradeVariant) {
                if (substr_count($line, $gradeVariant)) {
                    $gradeVariantFormatted = str_replace(['(', ')'], '', $gradeVariant);
                    $grades = explode('/', $gradeVariantFormatted);

                    $products[] = [
                        'price' => $price1,
                        'title' => str_replace($gradeVariant, '('. $grades[0] .')', $line),
                    ];

                    $products[] = [
                        'price' => $price2,
                        'title' => str_replace($gradeVariant, '('. $grades[1] .')', $line),
                    ];

                    return $products;
                }
            }

            $products[] = [
                'title' => $line,
                'price' => $price1,
            ];

            return $products;
        }

        return null;
    }
}
