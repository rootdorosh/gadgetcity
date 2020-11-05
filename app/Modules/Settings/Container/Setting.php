<?php

namespace App\Modules\Settings\Container;

use Illuminate\Contracts\Cache\Factory as CacheContract;
use Illuminate\Support\Arr;

class Setting
{
    protected $storage = null;
    protected $cache = null;

    /*
     * @param SettingStorageContract $storage
     * @param CacheContract $cache
     */
    public function __construct(SettingStorageContract $storage, CacheContract $cache)
    {
        $this->storage = $storage;
        $this->cache = $cache;
    }

    public function all()
    {
        return $this->storage->getAll();
    }

    /**
     * Return setting value or default value by key.
     *
     * @param string $key
     * @param string $value
     *
     * @return string|null
     */
    public function get($key, $default_value = null)
    {
        if (strpos($key, '.') !== false) {
            $setting = $this->getSubValue($key);
        } else {
            if ($this->hasByKey($key)) {
                $setting = $this->getByKey($key);
            } else {
                $setting = $default_value;
            }
        }

        if (is_null($setting)) {
            $setting = $default_value;
        }

        return $setting;
    }

    /**
     * Set the setting by key and value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        if (strpos($key, '.') !== false) {
            $this->setSubValue($key, $value);
        } else {
            $this->setByKey($key, $value);
        }
    }

    /**
     * Check if the setting exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $exists = $this->hasByKey($key);

        return $exists;
    }

    /**
     * Delete a setting.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget($key)
    {
        if (strpos($key, '.') !== false) {
            $this->forgetSubKey($key);
        } else {
            $this->forgetByKey($key);
        }
    }

    protected function getByKey($key)
    {
        if (strpos($key, '.') !== false) {
            $main_key = explode('.', $key)[0];
        } else {
            $main_key = $key;
        }

        return $this->storage->retrieve($main_key);

        if ($this->cache->has($main_key)) {
            $setting = $this->cache->get($main_key);
        } else {
            $setting = $this->storage->retrieve($main_key);

            if (!is_null($setting)) {
                $setting = $setting;
            }

            $setting_array = json_decode($setting, true);

            if (is_array($setting_array)) {
                $setting = $setting_array;
            }

            $this->cache->add($main_key, $setting, 1);
        }

        return $setting;
    }

    protected function setByKey($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $main_key = explode('.', $key)[0];

        if ($this->hasByKey($main_key)) {
            $this->storage->modify($main_key, $value);
        } else {
            $this->storage->store($main_key, $value);
        }

        if ($this->cache->has($main_key)) {
            $this->cache->forget($main_key);
        }
    }

    protected function hasByKey($key)
    {
        if (strpos($key, '.') !== false) {
            $setting = $this->getSubValue($key);
        } else {
            if ($this->cache->has($key)) {
                $setting = $this->cache->get($key);
            } else {
                $setting = $this->storage->retrieve($key);
            }
        }
        return ($setting === null) ? false : true;
    }

    protected function forgetByKey($key)
    {
        $this->storage->forget($key);

        $this->cache->forget($key);
    }

    protected function getSubValue($key)
    {
        $setting = $this->getByKey($key);

        $subkey = $this->removeMainKey($key);

        $setting = Arr::get($setting, $subkey);

        return $setting;
    }

    protected function setSubValue($key, $new_value)
    {
        $setting = $this->getByKey($key);

        $subkey = $this->removeMainKey($key);

        array_set($setting, $subkey, $new_value);

        $this->setByKey($key, $setting);
    }

    protected function forgetSubKey($key)
    {
        $setting = $this->getByKey($key);

        $subkey = $this->removeMainKey($key);

        array_forget($setting, $subkey);

        $this->setByKey($key, $setting);
    }

    protected function removeMainKey($key)
    {
        $pos = strpos($key, '.');
        $subkey = substr($key, $pos + 1);

        return $subkey;
    }
}
