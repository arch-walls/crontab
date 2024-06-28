<?php
namespace Arch\Crontab\Model;

class BaseModel extends \think\Model
{
    protected $table = '';
    protected $name = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (!$this->tableExists()) {
            $this->createTable();
        }
    }

    protected function createTable() { }

    /**
     * @param mixed $table
     * @return bool
     */
    public function tableExists($table = ''): bool
    {
        $table = empty($table) ?: $this->name ?: $this->table;

        return !empty($this->query('show tables like "' . $table . '"'));
    }
}