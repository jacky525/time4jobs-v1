<?php

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string $path
     * @return string
     */
    function base_path($path = '')
    {
        return dirname(dirname(__DIR__)).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

function application_settings()
{
    $envSettings = \Noodlehaus\Config::load(APP_ROOT . '/env.php');
    return $envSettings;
}
