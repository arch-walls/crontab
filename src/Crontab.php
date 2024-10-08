<?php
namespace Arches\Crontab;

use Arches\Crontab\Command\BaseCommand;
use Arches\Crontab\Model\CrontabLogModel;
use Arches\Crontab\Model\CrontabModel;
use Cron\CronExpression;
use DateTime;

class Crontab extends BaseCommand
{
    protected $name = 'crontab';

    protected $desc = '';

    /**
     * @param $country
     * @return string
     */
    protected function getTimezone($country)
    {
        if (!empty($country)) {
            $countries = (array) config('app.countries') ?: config('countries') ?: [];

            if (isset($countries[$country]['timezone'])) return strval($countries[$country]['timezone']);
        }
        return config('app.default_timezone') ?: config('default_timezone') ?: date_default_timezone_get();
    }

    public function handle()
    {
        $crontab_list = (new CrontabModel())->getOpens();
        if (empty($crontab_list)) {
            $this->output('no crontab need to run');
            return;
        }

        $progress = $this->progress = $this->newProgress(count($crontab_list));
        $cron = new CronExpression('* * * * *');
        $crontab_log = [];
        foreach ($crontab_list as $crontab) {
            $progress->progress();
            if (!$cron::isValidExpression($crontab['schedule'])) {
                $this->output('crontab[' . $crontab['id'] . '] schedule error');
                continue;
            }

            if (!$cron->setExpression($crontab['schedule'])->isDue(new DateTime(), $this->getTimezone($crontab['country_id']))) {
                $this->output('crontab[' . $crontab['id'] . '] not due');
                continue;
            }

            $command = $this->newCommand($crontab)->addBackExec();
            $running = $this->newRunning($command);
            if ($running->isOutRun(is_win() ? 1 : $crontab['max_process'])) {
                $this->output('crontab[' . $crontab['id'] . '] is out run-process');
                $this->output($crontab['id'], $running->list());
                continue;
            }

            @list($command_line, $result) = $this->newProcess($command)->exec();
            $crontab_log[] = [
                'cron_id' => $crontab['id'],
                'command' => $crontab['schedule'] . ' ' . $command_line,
                'result' => $result,
                'task_no' => $running->check($command)->taskNo(),
                'create_time' => time()
            ];
        }

        if (!empty($crontab_log)) {
            $logs = array_chunk($crontab_log, 500);
            foreach ($logs as $log) {
                (new CrontabLogModel())->insertAll($log);
            }
        }

        $this->output('crontab run success');
    }
}