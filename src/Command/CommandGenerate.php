<?php
namespace Arches\Crontab\Command;

use function Arches\Crontab\get_path;
use function Arches\Crontab\is_win;
use function Arches\Crontab\php_path;

class CommandGenerate
{
    private $php;

    private $cron;

    private $is_win;

    private $command = '';

    private $command_body = '';

    private $back_exec = '';

    public function __construct($cron = [])
    {
        $this->php = php_path();
        $this->cron = $cron;
        $this->is_win = is_win();
        if (!empty($this->cron) && is_array($this->cron)) {
            $body = [$this->php, rtrim(get_path('think'), DIRECTORY_SEPARATOR), $this->cron['command'], [$this->cron['country_id'], $this->cron['params']]];
            $this->build(...$body)->addLog($this->getLogPath(), $this->cron['log'] ?: $cron['command']);
        }
    }

    /**
     * @return string
     */
    protected function getLogPath()
    {
        $path = get_path($this->buildPath('runtime','cron', date('Y-m/d')));
        try {
            !is_dir($path) && mkdir($path, 0777, true);
        } catch (\Exception $exception) {}

        return $path;
    }

    /**
     * @param $path
     * @param string ...$_
     * @return string
     */
    protected function buildPath($path, ...$_)
    {
        if (!is_array($path)) {
            $path = func_get_args();
        }

        return str_replace('/', DIRECTORY_SEPARATOR, join(DIRECTORY_SEPARATOR, $path));
    }

    /**
     * @param string $php
     * @param string $think
     * @param string $command
     * @param $params
     * @return $this
     */
    public function build($php, $think, $command, $params)
    {
        if (!is_array($params)) $params = [$params];

        $params = join(' ', array_filter($params));

        $this->command_body = join(' ', array_filter([$command, $params]));
        $this->command = join(' ', array_filter([$php, $think, $command, $params]));

        return $this;
    }

    /**
     * @param string $path
     * @param string $log
     * @return $this
     */
    public function addLog($path, $log)
    {
        if (!empty($log)) {
            $log = $path . str_replace(' ', '_', $log);
            if (empty(pathinfo($log)['extension'])) {
                $log .= '.log';
            }
            $this->command .= ' >> ' . trim($log, ' ');
            if (!$this->is_win) {
                $this->command .= ' 2>&1 &';
            }
        }

        return $this;
    }

    /**
     * @return CommandGenerate
     */
    public function addBackExec()
    {
        if ($this->is_win) {
            $this->back_exec = 'start /B';
        } else {
            $this->back_exec = 'nohup';
        }

        $this->command = $this->back_exec . ' ' . $this->command;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->command_body;
    }

    /**
     * @return string
     */
    public function getExecCommand()
    {
        return $this->command;
    }
}