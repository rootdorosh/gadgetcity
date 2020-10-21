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


function arr_indexes_value(array $array, string $search) {
    $indexes = [];
    foreach ($array as $i => $val) {
        if ($val == $search) {
            $indexes[] = $i;
        }
    }
    return $indexes;
}

function arr_correct_diff(array $array) {
    $result = false;
    $count = count($array);

    if ($count > 1) {
        $result = true;
        $diff = $array[1] - $array[0];

        for ($i = 1; $i <= $count - 1; $i++) {
            if ($array[$i] - $array[$i-1] !== $diff) {
                return false;
            }
        }
    }

    return $result;
}

function telegram_post_is_inline(array $array) {

    foreach ($array as $val) {
        $val = trim($val);
        if ($val !== '' && !preg_match('/\d+\$/', $val)) {
            return false;
        }
    }

    return true;
}

/**
 * @param string $value
 */
function replate_to_letter_a(string $value) {
    $parts = explode('А', $value);
    return implode('A', $parts);
}


function str_tg_clean($string) {
    $string = strip_tags($string);

    $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clear_string = preg_replace($regex_emoticons, '', $string);

    $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clear_string = preg_replace($regex_symbols, '', $clear_string);

    $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clear_string = preg_replace($regex_transport, '', $clear_string);

    $regex_misc = '/[\x{2600}-\x{26FF}]/u';
    $clear_string = preg_replace($regex_misc, '', $clear_string);

    $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
    $clear_string = preg_replace($regex_dingbats, '', $clear_string);

    return trim($clear_string);
}
