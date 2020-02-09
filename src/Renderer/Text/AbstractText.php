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
