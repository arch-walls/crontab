<?php
namespace Arches\Crontab;

use Arches\Crontab\Crontab;
use Arches\Crontab\Install\Database;

class Service extends \think\Service
{
    public function boot() {
        Database::install();
        $this->commands([
            Crontab::class
        ]);
    }
}