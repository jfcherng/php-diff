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
     * @var int the union of OPs that indicate there is a change
     */
    const OP_BLOCK_CHANGED =
        SequenceMatcher::OP_INS |
        SequenceMatcher::OP_DEL |
        SequenceMatcher::OP_REP;

    /**
     * {@inheritdoc}
     */
    protected function renderWorker(Differ $differ): string
    {
        $ret = '';

        foreach ($differ->getGroupedOpcodes() as $hunk) {
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
     * @param string $delimiter the delimiter
     * @param int    $a1        the a1
     * @param int    $a2        the a2
     */
    protected function renderHunkHeader(string $delimiter, int $a1, int $a2): string
    {
        return
            "{$delimiter}{$delimiter}{$delimiter} " .
            ($a2 - $a1 >= 2 ? ($a1 + 1) . ',' . $a2 : $a2) .
            " {$delimiter}{$delimiter}{$delimiter}{$delimiter}\n";
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

            if ($op & self::OP_BLOCK_CHANGED) {
                $hasChangeInHunk = true;
            }

            $ret .= $this->renderContext(self::TAG_MAP[$op], $differ->getOld($i1, $i2));
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

            if ($op & self::OP_BLOCK_CHANGED) {
                $hasChangeInHunk = true;
            }

            $ret .= $this->renderContext(self::TAG_MAP[$op], $differ->getNew($j1, $j2));
        }

        return $hasChangeInHunk ? $ret : '';
    }

    /**
     * Render the context array with the symbol.
     *
     * @param string   $symbol  the leading symbol
     * @param string[] $context the context
     */
    protected function renderContext(string $symbol, array $context): string
    {
        return empty($context)
            ? ''
            : "{$symbol} " . \implode("\n{$symbol} ", $context) . "\n";
    }
}
