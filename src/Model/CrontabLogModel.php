<?php
namespace Arches\Crontab\Model;

class CrontabLogModel extends BaseModel
{
    protected $table = 'crontab_log';

    protected $name = 'crontab_log';

    protected function createTable()
    {
        $sql = <<<SQL
            CREATE TABLE `crontab_log` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `cron_id` int(11) unsigned NOT NULL DEFAULT '0',
              `command` varchar(255) NOT NULL DEFAULT '',
              `result` varchar(255) NOT NULL DEFAULT '',
              `task_no` varchar(255) NOT NULL DEFAULT '',
              `create_time` int(11) unsigned NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`) USING BTREE,
              KEY `idx_cron` (`cron_id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='定时任务配置表';
        SQL;

        $this->query($sql);
    }
}