<?php
declare(strict_types=1);
namespace Arches\Crontab\Command;

class CommandProgress
{
    /** @var array  */
    private $options = [
        'length' => 50,
        //'fill' => '▓',
        'fill' => '#',
        'prefix' => '',
        'suffix' => '',
        'run_time' => 0
    ];

    /** @var int  */
    private $total = 0;

    /** @var int  */
    private $current = 0;

    /** @var string  */
    private $prefix = '';

    /** @var float  */
    private $begin_time = 0;

    private $prev_cost_time = 0;

    private $times = 0;

    public function __construct(int $total, array $options = [])
    {
        $this->total = $total;
        $this->begin_time = $this->prev_cost_time = floatval($options['run_time'] ?? 0) ?: microtime(true);
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param int $current
     * @return void
     */
    public function progress(int $current = 0) {
        $this->times++;
        if ($current < 1) {
            $this->current++;
        } else {
            $this->current = $current;
        }
        $percent = intval(($this->current / $this->total) * 100);
        $barLength = intval($this->options['length'] * $percent / 100);
        $bar = str_repeat($this->options['fill'], $barLength) . str_repeat('-', $this->options['length'] - $barLength);

        printf("\r%s [%s] [%d/%d]%d%% %s time[micro]:%s", $this->getPrefix(), $bar, $this->current, $this->total, $percent, $this->options['suffix'], $this->getConsuming());
        if ($this->current == $this->total) {
            echo PHP_EOL;
        }
        flush();
    }

    /**
     * @param int $current
     * @return string
     */
    public function getPrefix() {
        if ($this->options['prefix'] === false) return '';

        if (!empty($this->options['prefix'])) {
            $this->prefix = $this->options['prefix'];
        }

        return $this->prefix;
    }

    /**
     * @return float
     */
    public function getConsuming() {
        $cost = microtime(true) - $this->prev_cost_time;
        $this->prev_cost_time = microtime(true);

        return $cost;
    }

    /**
     * @return bool
     */
    public function hasOver() {
        return $this->current >= $this->total;
    }
}