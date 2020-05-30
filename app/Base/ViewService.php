<?php

namespace App\Base;

use Illuminate\Support\Facades\Cookie;

/**
 * Class ViewService
 */
class ViewService
{
    const TIME_YEAR = 60 * 60 * 24 * 31 * 365;
    const COOKIE_KEY = '__VIEWS_';
    
    /*
     * @return array
     */
    public function getData(): array
    {
        $data = Cookie::get(self::COOKIE_KEY);
        
        return $data === null ? [] : unserialize($data);
    }

    /*
     * @params string $key
     * @return bool
     */
    public function hasView(string $key): bool
    {
        $data = $this->getData(); 
        
        return array_key_exists($key, $data);
    }

    /*
     * @params string $key
     * @return void
     */
    public function setView(string $key)
    {
        $data = $this->getData();
        $data[$key] = 1;
        
        Cookie::queue(self::COOKIE_KEY, serialize($data), self::TIME_YEAR);
    }
}