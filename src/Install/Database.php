<?php
namespace Arches\Crontab\Install;

use think\DbManager;

class Database
{
    private function __construct()
    {
        $this->doInstall();
    }

    public static function install() {
        return new self;
    }

    private function doInstall() {
        $this->createCrontabTable();
        $this->createCrontabLogTable();
    }

    private function createCrontabTable() {
        if ($this->tableExists('crontab')) return true;

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `crontab` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `country_id` mediumint(5) unsigned NOT NULL DEFAULT '0',
              `names` varchar(255) NOT NULL DEFAULT '' COMMENT '任务名称',
              `schedule` varchar(255) NOT NULL DEFAULT '* * * * *',
              `command` varchar(255) NOT NULL DEFAULT '',
              `params` varchar(255) NOT NULL DEFAULT '',
              `log` varchar(255) NOT NULL DEFAULT '',
              `max_process` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '最大允许进程数',
              `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1=启用 2=禁用',
              `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
              `create_time` int(11) unsigned NOT NULL DEFAULT '0',
              `update_time` int(11) unsigned NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`) USING BTREE,
              KEY `idx_status` (`status`) USING BTREE,
              KEY `idx_names` (`names`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='定时任务配置表';
        SQL;
        return $this->query($sql);
    }

    private function createCrontabLogTable() {
        if ($this->tableExists('crontab_log')) return true;

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `crontab_log` (
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
        return $this->query($sql);
    }

    /**
     * @param $table
     * @return bool
     */
    private function tableExists($table) {
        $sql = 'show tables like "' . $table . '"';

        return !!$this->query($sql);
    }

    private function query($sql) {
        $db = $this->getDb();

        try {
            return $db::query($sql);
        } catch (\Throwable $exception) {
            return false;
        }
    }

    /**
     *
     * @return string
     */
    private function getDb() {
        if (class_exists('think\facade\Db')) {
            return \think\facade\Db::class;
        }

        return \think\Db::class;
    }

    private function __clone() {}
}