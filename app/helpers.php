<?php

if (! function_exists('allow')) {
    /*
     * @param string $permission
     * @return bool
     */
    function allow(string $permission): bool 
    {
        return request()->user()->checkPermission($permission);
    }
}

if (! function_exists('locales')) {
    /*
     * @return array
     */
    function locales(): array 
    {
        return config('translatable.locales');
    }
}

function rmDirRecursive($path) {
    $path = str_replace('//', '/', $path);
    $files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? rmDirRecursive($file) : unlink($file);
	}
	rmdir($path);
	return;
}


if (! function_exists('l')) {
    /*
     * @return string
     */
    function l(): string 
    {
        return \App::getLocale();
    }
}

if (! function_exists('r')) {
    /*
     * @return string
     */
    function r($name, $parameters = [], $absolute = false)
    {
        return route($name, $parameters, $absolute) . '/';
    }
}


if (! function_exists('t')) {
    /*
     * @param string $slug
     * @param array $params
     * @return string $slug
     */
    function t(string $slug, array $params = []): string 
    {
        return (new App\Modules\Translation\Services\Fetch\TranslationFetchService)->get($slug, $params);
    }
}

if (! function_exists('array_list')) {
    /*
     * @return array $data
     */
    function array_list(array $data): array 
    {
        $items = [];
        foreach ($data as $val) {
            $items[$val] = $val;
        }
        
        return $items;
    }
}