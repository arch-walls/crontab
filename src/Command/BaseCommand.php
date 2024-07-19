<?php
namespace Arches\Crontab\Command;

use think\console\Input;
use think\console\Output;

class BaseCommand extends \think\console\Command
{
    protected $name = 'arches-base-crontab';

    protected $desc = '';

    protected $params = [];

    protected $task_no = '';

    protected $process_id;

    protected $command = '';

    /** @var CommandRunning */
    protected $running;

    /** @var CommandProgress */
    protected $progress;

    protected function configure()
    {
        $this->setName($this->name)->setDescription($this->desc);
        if (!empty($this->params)) {
            foreach ($this->params as $name => $param) {
                $this->addArgument($name, $param['mode'], isset($param['desc']) ? $param['desc'] : '', isset($param['default']) ? $param['default'] : null);
            }
        }

        $this->__configure();
    }

    protected function __configure() {}

    protected function execute(Input $input, Output $output)
    {
        $this->params = $input->getArguments();
        $this->__execute($input, $output);
        $this->handle();
    }

    protected function __execute(Input $input, Output $output) {}

    public function handle() {}

    public function run(Input $input, Output $output): int
    {
        $this->process_id = $this->createId();
        $this->command = $this->getTaskCommand();
        $this->running = $this->newRunning($this->command);
        $this->task_no = $this->running->taskNo();

        return parent::run($input, $output);
    }

    /**
     * @return string
     */
    protected function createId()
    {
        return md5(microtime(true) . $this->task_no . $this->name . rand(1, 1000));
    }

    public function output(...$message)
    {
        if (count($message) === 1) $message = $message[0];

        if (is_array($message)) $message = json_encode($message, JSON_UNESCAPED_UNICODE);

        $tmpl = '%s - %s || %s[%s] --> %s';

        $msg = sprintf($tmpl, $this->task_no, $this->process_id, $this->name, static::class, $message);
        if ($this->progress instanceof CommandProgress && !$this->progress->hasOver()) $msg = PHP_EOL . $msg;

        try {
            $this->output->writeln($msg);
        } catch (\Exception $exception) {}

        return $msg;
    }

    public function exception(...$message)
    {
        if (count($message) === 1) $message = $message[0];

        if (is_array($message)) $message = json_encode($message, JSON_UNESCAPED_UNICODE);

        $tmpl = '%s - %s || %s[%s] => %s --> %s';

        $msg = sprintf($tmpl, $this->task_no, $this->process_id, $this->name, static::class, 'exception', $message);
        if ($this->progress instanceof CommandProgress && !$this->progress->hasOver()) $msg = PHP_EOL . $msg;

        try {
            $this->output->writeln($msg);
        } catch (\Exception $exception) {}

        return $msg;
    }

    /**
     * @return CommandGenerate
     */
    protected function getTaskCommand()
    {
        $generate = $this->newCommand();
        array_shift($_SERVER['argv']);
        $command = array_shift($_SERVER['argv']);
        return $generate->build('', '', strval($command), $_SERVER['argv']);
    }

    /**
     * @param array $cron
     * @return CommandGenerate
     */
    protected function newCommand(array $cron = [])
    {
        return new CommandGenerate($cron);
    }

    /**
     * @param CommandGenerate $command
     * @return CommandRunning
     */
    protected function newRunning(CommandGenerate $command)
    {
        return new CommandRunning($command);
    }

    /**
     * @param CommandGenerate $command
     * @return CommandProcess
     */
    protected function newProcess(CommandGenerate $command)
    {
        return new CommandProcess($command);
    }

    /**
     * @param int $total
     * @param array $options
     * @return CommandProgress
     */
    protected function newProgress(int $total, array $options = []) {
        return new CommandProgress($total, $options);
    }
}
