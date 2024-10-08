<?php
namespace Arches\Crontab\Install;

use think\DbManager;

class Database
{
    private function __construct()
    {
        if (!$this->hasInstall()) {
            $this->doInstall();
        }
    }

    public static function install() {
        return new self;
    }

    private function getLockFile() {
        $file = __DIR__ . '/install.lock';
        if (!file_exists($file)) file_put_contents($file, 0);

        return $file;
    }

    private function hasInstall() {
        return intval(file_get_contents($this->getLockFile())) > 0;
    }

    private function installSuccess() {
        file_put_contents($this->getLockFile(), 1);
    }

    private function doInstall() {
        $this->createCrontabTable();
        $this->createCrontabLogTable();
        $this->installSuccess();
    }

    private function createCrontabTable() {
        if ($this->tableExists('crontab')) return true;

        $sql = "
            CREATE TABLE IF NOT EXISTS `crontab` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `country_id` mediumint(5) unsigned NOT NULL DEFAULT '0',
              `names` varchar(255) NOT NULL DEFAULT '' COMMENT '',
              `schedule` varchar(255) NOT NULL DEFAULT '* * * * *',
              `command` varchar(255) NOT NULL DEFAULT '',
              `params` varchar(255) NOT NULL DEFAULT '',
              `log` varchar(255) NOT NULL DEFAULT '',
              `max_process` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '',
              `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=enable 2=disable',
              `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '',
              `create_time` int(11) unsigned NOT NULL DEFAULT '0',
              `update_time` int(11) unsigned NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`) USING BTREE,
              KEY `idx_status` (`status`) USING BTREE,
              KEY `idx_names` (`names`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='crontab list';
        ";
        return $this->query($sql);
    }

    private function createCrontabLogTable() {
        if ($this->tableExists('crontab_log')) return true;

        $sql = "
            CREATE TABLE IF NOT EXISTS `crontab_log` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `cron_id` int(11) unsigned NOT NULL DEFAULT '0',
              `command` varchar(255) NOT NULL DEFAULT '',
              `result` varchar(255) NOT NULL DEFAULT '',
              `task_no` varchar(255) NOT NULL DEFAULT '',
              `create_time` int(11) unsigned NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`) USING BTREE,
              KEY `idx_cron` (`cron_id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='crontab log';
        ";
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
        } catch (\Exception $exception) {
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