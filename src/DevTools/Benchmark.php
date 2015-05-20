<?php

namespace DevTools;

class Benchmark
{

    /**
     * @var float
     */
    private $startTimestamp = 0.0;
    /**
     * @var int
     */
    private $highestDelta;
    /**
     * @var array
     */
    private $markedTimestamps;

    public function __construct()
    {
        $this->startTimestamp = microtime(true);
        $this->mark('Start', $this->startTimestamp);

    }

    /**
     * @param string $message
     * @param float $timestamp
     */
    public function mark($message, $timestamp = null)
    {
        $timestamp = $timestamp ?: microtime(true);
        $delta = $this->markedTimestamps ? ($timestamp - ($this->startTimestamp + $this->getTotalTime())) : 0;
        $this->markedTimestamps[] = [
            'message' => $message,
            'delta' => $delta,
            'totalTime' => $timestamp - $this->startTimestamp,
        ];
        $this->highestDelta = max($this->highestDelta, $delta);
    }

    private function getTotalTime()
    {
        return $this->markedTimestamps[count($this->markedTimestamps) - 1]['totalTime'];
    }


    public function getAllInHtml()
    {
        $this->mark('Benchmark::getAllInHtml()');
        $html = '<table><tr><th>Message</th><th>Delta</th><th>Percentage</th><th>Total time</th></tr>';

        foreach ($this->markedTimestamps as $mark) {
            $deltaColor = '#ff' . str_repeat(str_pad(dechex(255 * (1 - $mark['delta'] / $this->highestDelta)), 2, '0', STR_PAD_LEFT), 2);
            $percentageColor = '#ff' . str_repeat(str_pad(dechex(255 * (1 - $mark['delta'] / $this->getTotalTime())), 2, '0', STR_PAD_LEFT), 2);
            $html .= '<tr>'
                . '<td>' . $mark['message'] . '</td>'
                . '<td style="background-color:' . $deltaColor . '">' . number_format($mark['delta'], 6) . '</td>'
                . '<td style="background-color:' . $percentageColor . '">' . number_format($mark['delta'] / $this->getTotalTime() * 100, 2) . '%</td>'
                . '<td>' . number_format($mark['totalTime'], 6) . '</td>'
                . '</tr>';
        }
        $html .= '</table>';

        array_pop($this->markedTimestamps);

        return $html;
    }

}