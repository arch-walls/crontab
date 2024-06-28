<?php
namespace Arch\Crontab;

class Register
{
    public function boot($service) {
        $service = boolval($service);

        if ($service) {
            app()->bootService(\Arch\Crontab\Service\Service::class);
        } else {
            $this->bootLtTP6();
        }
    }

    /**
     * Version number less than TP6.0.0
     * @return void
     */
    private function bootLtTP6() {
        \think\Console::addDefaultCommands([\Arch\Crontab\Crontab::class]);
    }
}