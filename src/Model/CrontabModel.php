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