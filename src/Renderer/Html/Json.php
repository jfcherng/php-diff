<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

/**
 * Json diff generator.
 */
class Json extends AbstractHtml
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
    const IS_HTML_TEMPLATE = false;

    /**
     * {@inheritdoc}
     */
    const IDENTICAL_RESULT = '[]';

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        $changes = $this->getChanges();

        return \json_encode($changes, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }
}
