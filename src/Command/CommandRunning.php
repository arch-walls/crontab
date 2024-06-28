<?php
namespace Arch\Crontab\Command;

class CommandRunning
{
    /** @var CommandGenerate */
    private $command;
    private $running = [];
    private $is_win;

    public function __construct(CommandGenerate $command = null)
    {
        $this->is_win = is_win();
        if (null !== $command) $this->command = $command;

        if (!empty($this->command)) {
            $this->running = $this->listen();
        }
    }

    protected function init() {
        $this->command = null;
        $this->running = [];
    }

    /**
     * @param CommandGenerate $command
     * @return $this
     */
    public function check(CommandGenerate $command): CommandRunning
    {
        $this->command = $command;
        $this->running = $this->listen();

        return $this;
    }

    /**
     * @return array
     */
    private function listen(): array
    {
        if (empty($this->command)) return [];

        if ($this->is_win) {
            return $this->listenWin();
        }

        $command = 'pgrep -f "' . $this->command->getBody() . '" -a';

        $lines = explode("\r\n", strval(shell_exec($command)));

        $lines = array_filter($lines);
        $result = [];
        foreach ($lines as $line) {
            $line = trim($line, ' ');
            $line = array_filter(explode(' ', trim($line, ' ')));

            $pid = array_shift($line);
            $result[] = [
                'command' => join(' ', $line),
                'pid' => $pid
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    private function listenWin(): array
    {
        $command = <<<cmd
            wmic process where "commandline like '%php%' and commandline like '%{$this->command->getBody()}%' and not commandline like '%wmic%'" get processid,commandline 2>NUL
        cmd;

        $lines = explode("\r\n", shell_exec($command));
        array_shift($lines);

        $lines = array_filter($lines);
        $result = [];
        foreach ($lines as $line) {
            $line = trim($line, ' ');
            $line = array_filter(explode(' ', trim($line, ' ')));

            $pid = array_pop($line);
            $result[] = [
                'command' => join(' ', $line),
                'pid' => $pid
            ];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function taskNo(): string
    {
        return $this->running[$this->total() - 1]['pid'] ?? '';
    }

    /**
     * @return int
     */
    public function total(): int
    {
        return count($this->running);
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->running;
    }

    /**
     * @param int $max_process
     * @return bool
     */
    public function isOutRun(int $max_process): bool
    {
        return $this->total() >= $max_process;
    }
}