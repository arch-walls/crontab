<?php
namespace Arches\Crontab;

use Arches\Crontab\Service\Service;

class Register
{
    /**
     * @param $service
     * @return void
     */
    public function boot($service) {
        $service = boolval($service);

        if ($service) {
            app()->invokeClass(Service::class)->boot();
        } else {
            $this->bootLtTP6();
        }
    }

    /**
     * Version number less than TP6.0.0
     * @return void
     */
    private function bootLtTP6() {
        \think\Console::addDefaultCommands([Crontab::class]);
    }
}