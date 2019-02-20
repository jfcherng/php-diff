<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Renderer\RendererConstant;

/**
 * Json diff generator.
 */
final class Json extends AbstractHtml
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Json',
    ];

    /**
     * {@inheritdoc}
     */
    const IS_TEXT_TEMPLATE = true;

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        return \json_encode(
            $this->getChanges(),
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function formatLines(array $lines): array
    {
        // glue all lines into a single string to get rid of multiple function calls later
        // unnecessary, but should improve performance if there are many lines
        $string = \implode(RendererConstant::IMPLODE_DELIMITER, $lines);

        $string = $this->htmlSafe($string);

        // split the string back to lines
        return \explode(RendererConstant::IMPLODE_DELIMITER, $string);
    }
}
