<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Exception\UnsupportedFunctionException;
use Jfcherng\Diff\Renderer\AbstractRenderer;

/**
 * Base renderer for rendering text-based diffs.
 */
abstract class AbstractText extends AbstractRenderer
{
    /**
     * @var bool is this renderer pure text?
     */
    const IS_TEXT_RENDERER = true;

    /**
     * @var string the diff output representing there is no EOL at EOF in the GNU diff tool
     */
    const GNU_OUTPUT_NO_EOL_AT_EOF = '\ No newline at end of file';

    /**
     * {@inheritdoc}
     */
    public function getResultForIdenticalsDefault(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderArrayWorker(array $differArray): string
    {
        throw new UnsupportedFunctionException(__METHOD__);

        return ''; // make IDE not complain
    }
}
