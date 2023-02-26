<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

use Jfcherng\Diff\Contract\Renderer\AbstractTextRenderer;
use Jfcherng\Diff\Contract\Renderer\RendererTypeEnum;
use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;

/**
 * Plain text Json diff generator.
 */
final class JsonText extends AbstractTextRenderer
{
    /**
     * {@inheritdoc}
     */
    public const INFO = [
        'desc' => 'Text JSON',
        'type' => RendererTypeEnum::Text,
    ];

    /**
     * {@inheritdoc}
     */
    protected function renderWorker(Differ $differ): string
    {
        $ret = [];

        foreach ($differ->getGroupedOpcodes() as $hunk) {
            $ret[] = $this->renderHunk($differ, $hunk);
        }

        if ($this->options['outputOpAsString']) {
            $this->convertOpToString($ret);
        }

        return json_encode($ret, $this->options['jsonEncodeFlags']);
    }

    /**
     * Render the hunk.
     *
     * @param Differ  $differ the differ object
     * @param int[][] $hunk   the hunk
     */
    protected function renderHunk(Differ $differ, array $hunk): array
    {
        $ret = [];

        foreach ($hunk as [$op, $i1, $i2, $j1, $j2]) {
            $ret[] = [
                'op' => $op,
                'old' => [
                    'offset' => $i1,
                    'lines' => $differ->getOld($i1, $i2),
                ],
                'new' => [
                    'offset' => $j1,
                    'lines' => $differ->getNew($j1, $j2),
                ],
            ];
        }

        return $ret;
    }

    /**
     * Convert ops of changes to their string form for better readability.
     *
     * @param array[][] $changes the changes
     */
    protected function convertOpToString(array &$changes): void
    {
        foreach ($changes as &$hunks) {
            foreach ($hunks as &$block) {
                $block['op'] = SequenceMatcher::opIntToStr($block['op']);
            }
        }
    }
}
