<?php
namespace Arches\Crontab\Command;

use think\console\Input;
use think\console\Output;

class Command extends BaseCommand
{
    protected $options = [];

    protected function __configure()
    {
        if (!empty($this->options)) {
            foreach ($this->options as $name => $option) {
                $this->addOption($name, isset($option['short']) ? $option['short'] : '', isset($option['mode']) ? $option['mode'] : 0, isset($option['desc']) ? $option['desc'] : '', isset($option['default']) ? $option['default'] : '');
            }
        }
    }

    protected function __execute(Input $input, Output $output)
    {
        $this->options = $input->getOptions();
    }
}