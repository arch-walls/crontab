<?php
namespace Arches\Crontab;

use Arches\Crontab\Install\Database;
use think\App;

class Register
{
    public static $version;

    public static function invokeClass() {
        require_once get_path('thinkphp') . 'base.php';
        App::initCommon();

        Database::install();
        \think\Console::addDefaultCommands([Crontab::class]);
    }

    public static function invokeClass51() {
        Database::install();
        \think\Console::addDefaultCommands([Crontab::class]);
    }
}