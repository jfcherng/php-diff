<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;

/**
 * Context diff generator.
 *
 * @see https://en.wikipedia.org/wiki/Diff#Context_format
 */
final class Context extends AbstractText
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Context',
        'type' => 'Text',
    ];

    /**
     * @var string[] array of the different opcodes and their context diff equivalents
     */
    const TAG_MAP = [
        SequenceMatcher::OP_DEL => '-',
        SequenceMatcher::OP_EQ => ' ',
        SequenceMatcher::OP_INS => '+',
        SequenceMatcher::OP_REP => '!',
    ];

    /**
     * {@inheritdoc}
     */
    protected function renderWorker(Differ $differ): string
    {
        $ret = '';

        foreach ($differ->getGroupedOpcodesGnu() as $hunk) {
            $lastBlockIdx = \count($hunk) - 1;

            // note that these line number variables are 0-based
            $i1 = $hunk[0][1];
            $i2 = $hunk[$lastBlockIdx][2];
            $j1 = $hunk[0][3];
            $j2 = $hunk[$lastBlockIdx][4];

            $ret .=
                "***************\n" .
                $this->renderHunkHeader('*', $i1, $i2) .
                $this->renderHunkOld($differ, $hunk) .
                $this->renderHunkHeader('-', $j1, $j2) .
                $this->renderHunkNew($differ, $hunk);
        }

        return $ret;
    }

    /**
     * Render the hunk header.
     *
     * @param string $symbol the symbol
     * @param int    $a1     the begin index
     * @param int    $a2     the end index
     */
    protected function renderHunkHeader(string $symbol, int $a1, int $a2): string
    {
        return
            "{$symbol}{$symbol}{$symbol} " .
            ($a2 - $a1 >= 2 ? ($a1 + 1) . ',' . $a2 : $a2) .
            " {$symbol}{$symbol}{$symbol}{$symbol}\n";
    }

    /**
     * Render the old hunk.
     *
     * @param Differ  $differ the differ object
     * @param int[][] $hunk   the hunk
     */
    protected function renderHunkOld(Differ $differ, array $hunk): string
    {
        $ret = '';
        $hasChangeInHunk = false;

        foreach ($hunk as [$op, $i1, $i2, $j1, $j2]) {
            if ($op === SequenceMatcher::OP_INS) {
                continue;
            }

            if ($op !== SequenceMatcher::OP_EQ) {
                $hasChangeInHunk = true;
            }

            $ret .= $this->renderContext(self::TAG_MAP[$op], $differ, self::OLD_AS_SOURCE, $i1, $i2);
        }

        return $hasChangeInHunk ? $ret : '';
    }

    /**
     * Render the new hunk.
     *
     * @param Differ  $differ the differ object
     * @param int[][] $hunk   the hunk
     */
    protected function renderHunkNew(Differ $differ, array $hunk): string
    {
        $ret = '';
        $hasChangeInHunk = false;

        foreach ($hunk as [$op, $i1, $i2, $j1, $j2]) {
            if ($op === SequenceMatcher::OP_DEL) {
                continue;
            }

            if ($op !== SequenceMatcher::OP_EQ) {
                $hasChangeInHunk = true;
            }

            $ret .= $this->renderContext(self::TAG_MAP[$op], $differ, self::NEW_AS_SOURCE, $j1, $j2);
        }

        return $hasChangeInHunk ? $ret : '';
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

        $ret = "{$symbol} " . \implode("\n{$symbol} ", $context) . "\n";

        if (
            ($source === self::OLD_AS_SOURCE && $a2 === $differ->getOldNoEolAtEofIdx()) ||
            ($source === self::NEW_AS_SOURCE && $a2 === $differ->getNewNoEolAtEofIdx())
        ) {
            $ret .= self::GNU_OUTPUT_NO_EOL_AT_EOF . "\n";
        }

        return $ret;
    }
}
