<?php
namespace Arches\Crontab\Model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class CrontabModel extends BaseModel
{
    protected $table = 'crontab';

    protected $name = 'crontab';

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;

    protected function createTable()
    {
        $sql = <<<SQL
            CREATE TABLE `crontab` (
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

        $this->query($sql);
    }

    /**
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getOpens() {
        $crontabList = $this->where('status', self::STATUS_OPEN)->select();
        return is_array($crontabList) ? $crontabList :
            (method_exists($crontabList, 'toArray') ? $crontabList->toArray() : ((array) $crontabList));
    }
}