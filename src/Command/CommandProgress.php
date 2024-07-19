<?php
declare(strict_types=1);
namespace Arches\Crontab\Command;

class CommandProgress
{
    /** @var array  */
    private $options = [
        'length' => 50,
        'fill' => '▓',
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

    private $times = 0;

    public function __construct(int $total, array $options = [])
    {
        $this->total = $total;
        $this->begin_time = floatval($options['run_time'] ?? 0) ?: microtime(true);
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

        printf("\r%s [%s] %d%% %s s:%s", $this->getPrefix(), $bar, $percent, $this->options['suffix'], $this->getConsuming());
        if ($this->current == $this->total) {
            echo PHP_EOL;
        }
        flush();
    }

    /**
     * @param int $current
     * @return string
     */
    public function getPrefix(int $current = 0) {
        $current = $current ?: $this->current;
        if ($this->options['prefix'] === false) return '';

        if (empty($this->options['prefix'])) {
            $this->prefix = sprintf('%d/%d', $current, $this->total);
        } else {
            $this->prefix = $this->options['prefix'];
        }

        return $this->prefix;
    }

    /**
     * @return float
     */
    public function getConsuming() {
        return microtime(true) - $this->begin_time;
    }

    /**
     * @return bool
     */
    public function hasOver() {
        return $this->current >= $this->total;
    }
}