<?php

use Illuminate\Support\Facades\Lang;

if (!function_exists('admin_path')) {

    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_path($path = '')
    {
        return ucfirst(config('admin.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('admin_url')) {
    /**
     * Get admin url.
     *
     * @param string $path
     * @param mixed  $parameters
     * @param bool   $secure
     *
     * @return string
     */
    function admin_url($path = '', $parameters = [], $secure = null)
    {
        if (\Illuminate\Support\Facades\URL::isValidUrl($path)) {
            return $path;
        }

        $secure = $secure ?: config('admin.secure');

        return url(admin_base_path($path), $parameters, $secure);
    }
}

if (!function_exists('admin_base_path')) {
    /**
     * Get admin url.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_base_path($path = '')
    {
        $prefix = '/'.trim(config('admin.route.prefix'), '/');

        $prefix = ($prefix == '/') ? '' : $prefix;

        return $prefix.'/'.trim($path, '/');
    }
}

if (!function_exists('admin_toastr')) {

    /**
     * Flash a toastr message bag to session.
     *
     * @param string $message
     * @param string $type
     * @param array  $options
     *
     * @return string
     */
    function admin_toastr($message = '', $type = 'success', $options = [])
    {
        $toastr = new \Illuminate\Support\MessageBag(get_defined_vars());

        \Illuminate\Support\Facades\Session::flash('toastr', $toastr);
    }
}

if (!function_exists('admin_asset')) {

    /**
     * @param $path
     *
     * @return string
     */
    function admin_asset($path)
    {
        return asset($path, config('admin.secure'));
    }
}

if (!function_exists('admin_translate')) {
    /**
     * Now you can add your own translate files for your project.
     * The "laravel-admin" will search for the translations in these sequence:
     * A.) admin.modelName.columnName
     * B.) admin.columnName
     * C.) Column name with spaces (dots and underscore replaced with spaces)
     * D.) Fallback
     * If you have translation A, that will be used, if not then B.
     * If there is no translation at all:
     * if exists the fallback D else the C will be the output.
     *
     * @param      $modelPath
     * @param      $column
     * @param null $fallback
     * @return string
     */
    function admin_translate($column, $modelPath = "", $fallback = null)
    {
        $modelName = "";
        if ($modelPath) {

            $nameList = explode('\\', $modelPath);
            /*
             * CamelCase model name converted to underscore name version.
             * ExampleString => example_strinig
             */
            $modelName = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', end($nameList))), '_');
        }

        /*
         * ExampleString with banana => example_string_with_banana
         */
        $columnLower = ltrim(strtolower(preg_replace('/[A-Z ]([A-Z](?![a-z]))*/', '_$0', $column)), '_');
        $columnLower = str_replace(' ', '', $columnLower);
        /*
         * The possible translate keys in priority order.
         */
        $transLateKeys = [
            'admin.'.$modelName.'.'.$columnLower,
            'admin.'.$columnLower,
            'validation.attributes.'.$columnLower,
        ];

        $label = null;
        foreach ($transLateKeys as $key) {
            if (Lang::has($key) && is_string(trans($key))) {
                $label = trans($key);
                break;
            }
        }
        if (!$label) {
            $label = str_replace(['.', '_'], ' ', $fallback ? $fallback : ucfirst($column));
        }

        return (string) $label;
    }
}


if (!function_exists('admin_translate_arr')) {
    function admin_translate_arr($arr, $modelPath = "")
    {
        foreach ($arr as $key => $value) {
            $arr[admin_translate($key, $modelPath)] = $value;
            unset($arr[$key]);
        }

        return $arr;
    }
}
