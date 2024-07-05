<?php
namespace Arches\Crontab\Command;

use function Arches\Crontab\is_win;

class CommandProcess
{
    /** @var CommandGenerate  */
    private $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    private function getExecBody()
    {
        return $this->command->addBackExec()->getExecCommand();
    }

    /**
     * @return array
     */
    public function exec(): array
    {
        if (is_win()) {
            return $this->winExec();
        } else {
            return $this->unixExec();
        }
    }

    /**
     * @return array
     */
    protected function unixExec(): array
    {
        $result = shell_exec($this->getExecBody());
        return [
            $this->getExecBody(),
            is_null($result) ? 'success' : $result
        ];
    }

    /**
     * @return array
     */
    protected function winExec(): array
    {
        $descriptorspec = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w']  // stderr
        ];

        $pipes = [];
        $process = proc_open($this->getExecBody(), $descriptorspec, $pipes);

        if (is_resource($process)) {
            // Write input to stdin if provided
            /*if (!empty($input)) {
                fwrite($pipes[0], $input);
                fclose($pipes[0]);
            }

            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // Close process and get return code
            $returnCode = proc_close($process);*/
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $returnCode = proc_close($process);

            return [
                $this->getExecBody(),
                $returnCode == 0 ? 'success' : $returnCode
            ];
        } else {
            return [
                $this->getExecBody(),
                'Failed to execute command.'
            ];
        }
    }
}