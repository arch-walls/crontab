<?php
if (function_exists('is_win')) {
    /**
     * @return bool
     */
    function is_win(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}

if (!function_exists('tp_version')) {
    /**
     * @return string
     */
    function tp_version(): string
    {
        if (defined('THINK_VERSION')) {
            return THINK_VERSION;
        } else if (class_exists('\think\App')) {
            if (!empty(\think\App::VERSION)) {
                return \think\App::VERSION;
            }
        }

        return '0.0.0';
    }
}

if (!function_exists('support_check')) {
    /**
     * @param $version
     * @return bool
     */
    function support_check($version): bool
    {
        //if (version_compare($version, '8.0.0') >= 0) return true;

        //if (version_compare($version, '6.0.0') >= 0) return true;

        if (version_compare($version, '5.0.0')) return true;

        return false;
    }
}

if (function_exists('is_cli')) {
    /**
     * @return bool
     */
    function is_cli(): bool
    {
        return (PHP_SAPI == 'cli' || PHP_SAPI == 'phpdbg');
    }
}

if (!function_exists('register_crontab') && function_exists('request')) {
    function register_crontab($version)
    {
        if (support_check($version)) {
            if (version_compare($version, '6.0.0') >= 0) {
                app()->invokeClass(\Arch\Crontab\Register::class)->boot(true);
            } else {
                \think\App::invokeClass(\Arch\Crontab\Register::class)->boot(false);
            }
        }
    }
}

if (!function_exists('php_path')) {
    /**
     * @return string
     */
    function php_path(): string
    {
        return config('app.php_path') ?: 'php';
    }
}

if (!function_exists('root_dir')) {
    function root_dir(): string
    {
        if (function_exists('root_path')) {
            return root_path();
        }

        if (defined('ROOT_PATH')) {
            return ROOT_PATH;
        }

        if (is_win()) {
            return explode('vendor\\arch', __DIR__)[0] ?? '';
        }

        return explode('vendor/arch', __DIR__)[0] ?? '';
    }
}

if (!function_exists('get_path')) {
    /**
     * @param string $child_path
     * @return string
     */
    function get_path($child_path = ''): string
    {
        return root_dir() . $child_path;
    }
}

if (is_cli() && function_exists('register_crontab')) {
    register_crontab(tp_version());
}
