<?php

declare( strict_types = 1 );

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Settings\Container\SettingStorageContract;
use App\Base\Traits\Cacheable;
use Cache;

/**
 * App\Modules\Settings\Models\Settings
 *
 * @property string $key
 * @property string|null $value
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Settings\Models\Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Settings\Models\Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Settings\Models\Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Settings\Models\Settings whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Settings\Models\Settings whereValue($value)
 * @mixin \Eloquent
 */
class Settings extends Model implements SettingStorageContract
{
    use Cacheable;

    /**
     * @var  bool
     */
    public $timestamps = false;

    /*
     * @var  string
     */
    public $table = 'settings';

    /**
     * The attributes that are mass assignable.

     * @var  array
     */
    public $fillable = [
        'key',
        'value',
    ];

    /*
     * @return array
     */
    public static function getAll(): array
    {
        $key = (new self)->getTag() . 'getAll';
        $tags = [
            (new self)->getTag(),
        ];

        return Cache::tags($tags)->remember($key, 60 * 60 * 24, function() {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    public function retrieve($key)
    {
        $data = self::getAll();

        return isset($data[$key]) ? $data[$key] : null;
    }

    public function store($key, $value)
    {
        $setting = ['key' => $key, 'value' => $value];

        static::create($setting);
    }

    public function modify($key, $value)
    {
        $setting = new static();

        $setting->where('key', $key)->update(['value' => $value]);
    }

    public function forget($key)
    {
        $setting = static::where('key', $key);
        $setting->delete();
    }

    public function getLyceumAttribute()
    {
        return old('lyceum') ? old('lyceum') : conf('lyceum');
    }
}
