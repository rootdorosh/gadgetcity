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
        //$line = '11 pro max 64 space/silver A/A+ (SM) 880-890$';
        //$line = 'XR 64 black/red/yellow/blue (A/A-) 415/395$';

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
            '/ А)' => '/A)',
            '/ А+)' => '/A+)',
            '/ А-)' => '/A-)',
            '(А /' => '(A/',
            '(А+ /' => '(A+/',
            '(А- /' => '(A-/',
            'А )' => 'A)',
            'А+ )' => 'A+)',
            'А- )' => 'A-)',
            '(A/A- ' => '(A/A-) ',
            '(A/A+ ' => '(A/A+) ',
            '(A-/A ' => '(A-/A) ',
            '(A-/A+ ' => '(A-/A+) ',
            '(A+/A ' => '(A+/A) ',
            '(A+/A- ' => '(A+/A-) ',

            '(А/А- ' => '(A/A-) ',
            '(А/А+ ' => '(A/A+) ',
            '(А-/А ' => '(A-/A) ',
            '(А-/А+ ' => '(A-/A+) ',
            '(А+/А ' => '(A+/A) ',
            '(А+/А- ' => '(A+/A-) ',

            'А/А+' => 'A/A+',
            'А/А-' => 'A/A-',
            'А+/А' => 'A+/A',
            'А+/А-' => 'A+/A-',
            'А-/А' => 'A-/A',
            'А-/А-' => 'A-/A+',
            'A)' => 'A) ',
            'A-)' => 'A+) ',
            'A+)' => 'A-) ',
        ];

        //XR 64 black/coral/white/blue (A/A-)450$/425$

        $line = str_replace(array_keys($replaceGrade), array_values($replaceGrade), $line);
        $line = str_replace('  ', ' ', $line);

        $gradeValues = ['A+', 'A-', 'A'];
        $gradeVariants = [];
        $gradeVariantsTwo = [];
        foreach ($gradeValues as $one) {
            foreach ($gradeValues as $two) {
                $gradeVariants[$one.'_'.$two] = sprintf('(%s/%s)', $one, $two);
                $gradeVariantsTwo[$one.'_'.$two] = sprintf('%s/%s', $one, $two);
            }
        }

        $patterns = [
            [
                'pattern' => '/(\)|\s|\/|\-)([0-9]{1,10}\$)\/([0-9]{1,10}\$)/',
                'example' => 'Xr 64gb red/space/coral/blue/red 500$/480$ (А/A-)',
            ],
            [
                'pattern' => '/(\)|\s|\/|\-)([0-9]{1,10})\/([0-9]{1,10}\$)/',
                'example' => 'Xr 64gb red/space/coral/blue/red 500/480$ (А/A-)',
            ],
            [
                'pattern' => '/(\)|\s|\/|\-)([0-9]{1,10})\-([0-9]{1,10}\$)/',
                'example' => '11 pro max 64 space/silver A/A+ (SM) 880-890$',
            ],
            [
                'pattern' => '/(\)|\s|\/|\-)([0-9]{1,10})\$\/([0-9]{1,10}\$)/',
                'example' => 'Used 11 Pro max 64 space/green (A/A-) 860$/820$ (акб 94+)',
            ],
            [
                'pattern' => '/(\)|\s|\/|\-)([0-9]{1,10})\/([0-9]{1,10}\$)/',
                //'example' => 'XR 64 black/red/yellow/blue (A/A-) 415/395$',
                'example' => 'X 256 space/silver (A/A-) 460/440$',
            ],
        ];

        $pattern = $example = $match = null;
        foreach ($patterns as $item) {
            if (preg_match($item['pattern'], $line, $match)) {
                $example = $item['example'];
                $pattern = $item['pattern'];
            }
        }

        if ($example) {
            if (!isset($match[2]) && !isset($match[3])) {
                return [];
            }

            $products = [];

            $price1 = (int)$match[2];
            $price2 = (int)$match[3];
            $line = trim(str_replace($match[0], '', $line));

            //$gradeVariants[] = '(A/A-)';

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
                }
            }

            if (empty($products)) {
                foreach ($gradeVariantsTwo as $gradeVariant) {
                    if (substr_count($line, $gradeVariant)) {
                        $grades = explode('/', $gradeVariant);

                        $products[] = [
                            'price' => $price1,
                            'title' => str_replace($gradeVariant, $grades[0], $line),
                        ];

                        $products[] = [
                            'price' => $price2,
                            'title' => str_replace($gradeVariant, $grades[1], $line),
                        ];

                        return $products;
                    }
                }
            }

            if (empty($products)) {
                $products[] = [
                    'title' => $line,
                    'price' => $price1,
                ];
            }

            return $products;
        }

        return null;
    }
}
