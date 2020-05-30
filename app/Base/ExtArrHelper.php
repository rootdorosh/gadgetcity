<?php

namespace App\Base;

/**
 * Class ExtArrHelper.
 */
class ExtArrHelper
{
    /**
     * @param array $array
     * @param string $fromKey
     * @param string $toKey
     * @return array
     */
    public static function keyToItems(array $array, string $fromKey, string $toKey) : array
    {
        $data = $array;
        if (isset($array[$fromKey])) {
            unset($data[$fromKey]);
            
            foreach ($array[$fromKey] as $item) {
                if (isset($item[$toKey])) {
                    $toKeyValue = $item[$toKey];
                    unset($item[$toKey]);
                    $data[$toKeyValue] = $item;
                }
            }
        }
        
        return $data;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function transformModelLang(array $array) : array
    {
        $data = $array;
        if (isset($array['data']['lang']['data'])) {
            unset($data['data']['lang']);
            
            foreach ($array['data']['lang']['data'] as $item) {
                if (isset($item['locale'])) {
                    $localeValue = $item['locale'];
                    unset($item['locale']);
                    $data['data'][$localeValue] = $item;
                }
            }
        }
        
        return $data;
    }
    
    /**
     * @param array $array
     * @return array
     */
    public static function keyValFromValues(array $array) : array
    {
        $data = [];
        foreach ($array as $val) {
            $data[$val] = $val;
        }
        
        return $data;
    }
    
}
