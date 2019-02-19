<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Renderer\AbstractRenderer;

/**
 * Base renderer for rendering text-based diffs.
 */
abstract class AbstractText extends AbstractRenderer
{
    /**
     * @var bool is this template pure text?
     */
    const IS_TEXT_TEMPLATE = true;
}
