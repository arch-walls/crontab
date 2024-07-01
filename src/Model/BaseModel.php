<?php
namespace Arches\Crontab\Model;

use Arches\Crontab\Register;

class BaseModel
{
    protected static $db;

    protected $table = '';
    protected $name = '';

    public function __construct()
    {
        self::$db = $this->db();
    }

    private function db() {
        if (version_compare(Register::$version, '6.0.0') >= 0) {
            return \think\facade\Db::class;
        }

        return \think\Db::class;
    }

    public function table($table = '') {
        $table = $table ? $table : $this->table;

        return (self::$db)::table($table);
    }

    public function insertAll($data) {
        return $this->table()->insertAll($data);
    }
}