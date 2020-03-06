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

        foreach ($differ->getGroupedOpcodesGnu() as $hunk) {
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

        // note that these line number variables are 0-based
        $i1 = $hunk[0][1];
        $i2 = $hunk[$lastBlockIdx][2];
        $j1 = $hunk[0][3];
        $j2 = $hunk[$lastBlockIdx][4];

        $oldLinesCount = $i2 - $i1;
        $newLinesCount = $j2 - $j1;

        return
            '@@' .
            ' -' .
                // the line number in GNU diff is 1-based, so we add 1
                // a special case is when a hunk has only changed blocks,
                // i.e., context is set to 0, we do not need the adding
                ($i1 === $i2 ? $i1 : $i1 + 1) .
                // if the line counts is 1, it can (and mostly) be omitted
                ($oldLinesCount === 1 ? '' : ",{$oldLinesCount}") .
            ' +' .
                ($j1 === $j2 ? $j1 : $j1 + 1) .
                ($newLinesCount === 1 ? '' : ",{$newLinesCount}") .
            " @@\n";
    }

    /**
     * Render the hunk content.
     *
     * @param Differ  $differ the differ
     * @param int[][] $hunk   the hunk
     */
    protected function renderHunkBlocks(Differ $differ, array $hunk): string
    {
        $ret = '';

        foreach ($hunk as [$op, $i1, $i2, $j1, $j2]) {
            // note that although we are in a OP_EQ situation,
            // the old and the new may not be exactly the same
            // because of ignoreCase, ignoreWhitespace, etc
            if ($op === SequenceMatcher::OP_EQ) {
                // we could only pick either the old or the new to show
                $ret .= $this->renderContext(' ', $differ, self::NEW_AS_SOURCE, $j1, $j2);

                continue;
            }

            if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_DEL)) {
                $ret .= $this->renderContext('-', $differ, self::OLD_AS_SOURCE, $i1, $i2);
            }

            if ($op & (SequenceMatcher::OP_REP | SequenceMatcher::OP_INS)) {
                $ret .= $this->renderContext('+', $differ, self::NEW_AS_SOURCE, $j1, $j2);
            }
        }

        return $ret;
    }

    /**
     * Render the context array with the symbol.
     *
     * @param string $symbol the symbol
     * @param Differ $differ the differ
     * @param int    $source the source type
     * @param int    $a1     the begin index
     * @param int    $a2     the end index
     */
    protected function renderContext(string $symbol, Differ $differ, int $source, int $a1, int $a2): string
    {
        $context = $source === self::OLD_AS_SOURCE
            ? $differ->getOld($a1, $a2)
            : $differ->getNew($a1, $a2);

        if (empty($context)) {
            return '';
        }

        $ret = $symbol . \implode("\n{$symbol}", $context) . "\n";

        if (
            ($source === self::OLD_AS_SOURCE && $a2 === $differ->getOldNoEolAtEofIdx()) ||
            ($source === self::NEW_AS_SOURCE && $a2 === $differ->getNewNoEolAtEofIdx())
        ) {
            $ret .= self::GNU_OUTPUT_NO_EOL_AT_EOF . "\n";
        }

        return $ret;
    }
}
