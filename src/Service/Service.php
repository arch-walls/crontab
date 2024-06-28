<?php
namespace Arches\Crontab\Service;

use Arches\Crontab\Crontab;

class Service extends \think\Service
{
    public function boot() {
        $this->commands([
            Crontab::class
        ]);
    }
}