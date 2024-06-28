<?php
namespace Arch\Crontab\Service;

use Arch\Crontab\Crontab;

class Service extends \think\Service
{
    public function boot() {
        $this->commands([
            Crontab::class
        ]);
    }
}