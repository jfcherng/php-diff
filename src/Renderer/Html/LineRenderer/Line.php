<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Diff\Renderer\RendererConstant;
use Jfcherng\Utility\MbString;

final class Line extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(MbString $mbFrom, MbString $mbTo): LineRendererInterface
    {
        [$start, $end] = $this->getChangedExtentRegion($mbFrom, $mbTo);

        // two strings are the same
        if ($end === 0) {
            return $this;
        }

        // two strings are different, we do rendering
        $mbFrom->str_enclose_i(
            RendererConstant::HTML_CLOSURES,
            $start,
            $end + $mbFrom->strlen() - $start + 1
        );
        $mbTo->str_enclose_i(
            RendererConstant::HTML_CLOSURES,
            $start,
            $end + $mbTo->strlen() - $start + 1
        );

        return $this;
    }

    /**
     * Given two strings, determine where the changes in the two strings begin,
     * and where the changes in the two strings end.
     *
     * @param MbString $mbFrom the megabytes from line
     * @param MbString $mbTo   the megabytes to line
     *
     * @return array Array containing the starting position (non-negative) and the ending position (negative)
     *               [0, 0] if two strings are the same
     */
    protected function getChangedExtentRegion(MbString $mbFrom, MbString $mbTo): array
    {
        // two strings are the same
        // most lines should be this cases, an early return could save many function calls
        if ($mbFrom->getRaw() === $mbTo->getRaw()) {
            return [0, 0];
        }

        // calculate $start
        $start = 0;
        $startMax = \min($mbFrom->strlen(), $mbTo->strlen());
        while (
            $start < $startMax && // index out of range
            $mbFrom->getAtRaw($start) === $mbTo->getAtRaw($start)
        ) {
            ++$start;
        }

        // calculate $end
        $end = -1; // trick
        $endMin = $startMax - $start;
        while (
            -$end <= $endMin && // index out of range
            $mbFrom->getAtRaw($end) === $mbTo->getAtRaw($end)
        ) {
            --$end;
        }

        return [$start, $end];
    }
}
