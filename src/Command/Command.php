<?php
namespace Arches\Crontab\Command;

use think\console\Input;
use think\console\Output;

class Command extends \think\console\Command
{
    protected $options = [];

    protected function __configure()
    {
        if (!empty($this->options)) {
            foreach ($this->options as $name => $option) {
                $this->addOption($name, $option['short'] ?? '', $option['mode'] ?? 0, $option['desc'] ?? '', $option['default'] ?? '');
            }
        }
    }

    protected function __execute(Input $input, Output $output)
    {
        $this->options = $input->getOptions();
    }
}