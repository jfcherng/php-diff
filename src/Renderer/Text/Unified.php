<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;

/**
 * Unified diff generator.
 *
 * @see https://en.wikipedia.org/wiki/Diff#Unified_format
 */
final class Unified extends AbstractText
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Unified',
        'type' => 'Text',
    ];

    /**
     * {@inheritdoc}
     */
    protected function renderWorker(Differ $differ): string
    {
        $ret = '';

        foreach ($differ->getGroupedOpcodes() as $hunk) {
            $ret .= $this->renderHunkHeader($differ, $hunk);
            $ret .= $this->renderHunkBlocks($differ, $hunk);
        }

        return $ret;
    }

    /**
     * Render the hunk header.
     *
     * @param Differ  $differ the differ
     * @param int[][] $hunk   the hunk
     */
    protected function renderHunkHeader(Differ $differ, array $hunk): string
    {
        $lastBlockIdx = \count($hunk) - 1;

        $i1 = $hunk[0][1];
        $i2 = $hunk[$lastBlockIdx][2];
        $j1 = $hunk[0][3];
        $j2 = $hunk[$lastBlockIdx][4];

        if ($i1 === 0 && $i2 === 0) {
            $i1 = $i2 = -1; // trick
        }

        return
            '@@ ' .
            '-' . ($i1 + 1) . ',' . ($i2 - $i1) . ' ' .
            '+' . ($j1 + 1) . ',' . ($j2 - $j1) . ' ' .
            "@@\n";
    }

    /**
     * Render the hunk content.
     *
     * @param Differ  $differ the differ
     * @param int[][] $hunk   the hunk
     */
    protected function renderHunkBlocks(Differ $differ, array $hunk): string
    {
        $html = '';

        foreach ($hunk as [$op, $i1, $i2, $j1, $j2]) {
            // note that although we are in a OP_EQ situation,
            // the old and the new may not be exactly the same
            // because of ignoreCase, ignoreWhitespace, etc
            if ($op === SequenceMatcher::OP_EQ) {
                // we could only pick either the old or the new to show
                $html .= $this->renderContext(' ', $differ->getNew($j1, $j2));

                continue;
            }

            if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_DEL)) {
                $html .= $this->renderContext('-', $differ->getOld($i1, $i2));
            }

            if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_INS)) {
                $html .= $this->renderContext('+', $differ->getNew($j1, $j2));
            }
        }

        return $html;
    }

    /**
     * Render the context array with the symbol.
     *
     * @param string   $symbol  the symbol
     * @param string[] $context the context
     */
    protected function renderContext(string $symbol, array $context): string
    {
        return empty($context)
            ? ''
            : $symbol . \implode("\n{$symbol}", $context) . "\n";
    }
}
