<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Renderer\AbstractRenderer;

abstract class AbstractText extends AbstractRenderer
{
    /**
     * @var bool Is this template pure text?
     */
    const IS_HTML_TEMPLATE = false;
}
