<?php
namespace Arches\Crontab\Command;

use think\console\Input;
use think\console\Output;

class BaseCommand extends Command
{
    protected $name = '';

    protected $desc = '';

    protected $params = [];

    protected $task_no = '';

    protected $process_id;

    protected $command = '';

    /** @var CommandRunning */
    protected $running;

    protected function configure()
    {
        $this->setName($this->name)->setDescription($this->desc);
        if (!empty($this->params)) {
            foreach ($this->params as $name => $param) {
                $this->addArgument($name, $param['mode'], $param['desc'] ?? '', $param['default'] ?? null);
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
    protected function createId(): string
    {
        return md5(microtime(true) . $this->task_no . $this->name . rand(1, 1000));
    }


    /**
     * @param ...$message
     * @return void
     */
    public function output(...$message): void
    {
        if (count($message) === 1) $message = $message[0];

        if (is_array($message)) $message = json_encode($message, JSON_UNESCAPED_UNICODE);

        $tmpl = '%s - %s || %s[%s] --> %s';

        $msg = sprintf($tmpl, $this->task_no, $this->process_id, $this->name, static::class, $message);

        try {
            $this->output->writeln($msg);
        } catch (\Throwable $exception) {}
    }

    /**
     * @param ...$message
     * @return void
     */
    public function exception(...$message): void
    {
        if (count($message) === 1) $message = $message[0];

        if (is_array($message)) $message = json_encode($message, JSON_UNESCAPED_UNICODE);

        $tmpl = '%s - %s || %s[%s] => %s --> %s';

        $msg = sprintf($tmpl, $this->task_no, $this->process_id, $this->name, static::class, 'exception', $message);

        try {
            $this->output->writeln($msg);
        } catch (\Throwable $exception) {}
    }

    /**
     * @return CommandGenerate
     */
    protected function getTaskCommand(): CommandGenerate
    {
        $generate = $this->newCommand();
        array_shift($_SERVER['argv']);
        $generate->build('', '', array_shift($_SERVER['argv']) ?: '', $_SERVER['argv'] ?: []);
        return $generate;
    }

    /**
     * @param array $cron
     * @return CommandGenerate
     */
    protected function newCommand(array $cron = []): CommandGenerate
    {
        return new CommandGenerate($cron);
    }

    /**
     * @param CommandGenerate $command
     * @return CommandRunning
     */
    protected function newRunning(CommandGenerate $command): CommandRunning
    {
        return new CommandRunning($command);
    }

    /**
     * @param CommandGenerate $command
     * @return CommandProcess
     */
    protected function newProcess(CommandGenerate $command): CommandProcess
    {
        return new CommandProcess($command);
    }
}