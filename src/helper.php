<?php
namespace Arches\Crontab;

if (!function_exists('is_win')) {
    /**
     * @return bool
     */
    function is_win()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}

if (!function_exists('is_cli')) {
    /**
     * @return bool
     */
    function is_cli()
    {
        return (PHP_SAPI == 'cli' || PHP_SAPI == 'phpdbg');
    }
}

if (!function_exists('tp_version')) {
    /**
     * @return string
     */
    function tp_version()
    {
        try {
            return ltrim(\Composer\InstalledVersions::getPrettyVersion('topthink/framework'), 'v');
        } catch (OutOfBoundsException $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}

\Arches\Crontab\Register::$version = (is_cli() ? tp_version() : '');

if (!function_exists('root_dir')) {
    function root_dir()
    {
        $version = \Arches\Crontab\Register::$version;
        if (version_compare($version, '6.0.0') >= 0) {
            return root_path();
        }

        if (version_compare($version, '5.1.0') >= 0) {
            return app()->getRootPath();
        }

        if (version_compare($version, '5.0.0') >= 0) {
            if (defined('ROOT_PATH')) {
                return ROOT_PATH;
            }
        }

        if (is_win()) {
            $path = explode('vendor\\arch', __DIR__)[0] ?? '';
        } else {
            $path = explode('vendor/arch', __DIR__)[0] ?? '';
        }

        return empty($path) ? '' : ($path . DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('get_path')) {
    /**
     * @param string $child_path
     * @return string
     */
    function get_path($child_path = '', ...$_)
    {
        $child_path = join(DIRECTORY_SEPARATOR, func_get_args()) . DIRECTORY_SEPARATOR;
        return root_dir() . $child_path;
    }
}

if (!function_exists('support_check')) {
    /**
     * @param $version
     * @return bool
     */
    function support_check($version)
    {
        if (version_compare($version, '8.0.0') >= 0) return true;

        if (version_compare($version, '6.0.0') >= 0) return true;

        if (version_compare($version, '5.1.0') >= 0) return true;
        if (version_compare($version, '5.0.0') >= 0) return true;

        return false;
    }
}

if (!function_exists('register_crontab')) {
    function register_crontab($version)
    {
        if (support_check($version)) {
            if (version_compare($version, '6.0.0') < 0) {
                if (version_compare($version, '5.1.0') < 0) {
                    \Arches\Crontab\Register::invokeClass();
                } else {
                    \Arches\Crontab\Register::invokeClass51();
                }
            }
        }
    }
}

if (!function_exists('php_path')) {
    /**
     * @return string
     */
    function php_path()
    {
        return config('app.php_path') ?: 'php';
    }
}

if (function_exists('is_cli') && function_exists('register_crontab')) {
    register_crontab(\Arches\Crontab\Register::$version);
}