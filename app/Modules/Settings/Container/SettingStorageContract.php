<?php

namespace App\Modules\Settings\Container;

interface SettingStorageContract
{
    /**
     * Return all data.
     *
     * @return array
     */
    public static function getAll();

    /**
     * Return setting value or default value by key.
     *
     * @param string $key
     *
     * @return string
     */
    public function retrieve($key);

    /**
     * Set the setting by key and value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function store($key, $value);

    /**
     * Check if the setting exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function modify($key, $value);

    /**
     * Delete a setting.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget($key);
}
