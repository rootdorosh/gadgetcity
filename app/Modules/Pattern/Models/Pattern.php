<?php

declare( strict_types = 1 );

namespace App\Modules\Pattern\Models;

use Illuminate\Database\Eloquent\Model;

class Pattern extends Model
{

    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'pattern';

    /**
     * The attributes that are mass assignable.

     * @var  array
     */
    public $fillable = [
        'id',
        'example',
        'value',
        'rank',
    ];

    /**
     * @return string
     */
    public function getPatternAttribute(): string
    {
        return self::convertToPattern($this->value);
    }

    /**
     * @param string $value
     * @return string
     */
    public static function convertToPattern(string $value): string
    {
        $replace = [
            '!' => '\!',
            '/' => '\/',
            '-' => '\-',
            '$' => '\$',
            '[space]' => '\s',
            '[price]' => '(([0-9]{1}\s[0-9]{1,10}\,00)|([0-9]{1}\s[0-9]{1,10}\.00)|([0-9]{1}\s[0-9]{1,10})|([0-9]{1,10}\.00)|([0-9]{1,10}))',
            '[price_end]' => '(([0-9]{1}\s[0-9]{1,10}\,00)|([0-9]{1}\s[0-9]{1,10}\.00)|([0-9]{1}\s[0-9]{1,10})|([0-9]{1,10}\.00)|([0-9]{1,10}))$',
        ];

        return '/' . str_replace(array_keys($replace), $replace, $value) . '/';
    }
}
