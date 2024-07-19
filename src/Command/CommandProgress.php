<?php
declare(strict_types=1);
namespace Arches\Crontab\Command;

class CommandProgress
{
    private $options = [
        'length' => 50,
        'fill' => '▓',
        'prefix' => '',
        'suffix' => ''
    ];

    private $total = 0;

    private $current = 0;

    public function __construct(int $total, array $options = [])
    {
        $this->total = $total;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param int $current
     * @return void
     */
    public function progress(int $current = 0) {
        if ($current < 1) {
            $this->current++;
        } else {
            $this->current = $current;
        }
        $percent = intval(($this->current / $this->total) * 100);
        $barLength = intval($this->options['length'] * $percent / 100);
        $bar = str_repeat($this->options['fill'], $barLength) . str_repeat('-', $this->options['length'] - $barLength);

        printf("\r%s [%s] %d%% %s", $this->getPrefix(), $bar, $percent, $this->options['suffix']);
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

        return $this->options['prefix'];
    }

    /**
     * @return bool
     */
    public function hasOver() {
        return $this->current >= $this->total;
    }
}