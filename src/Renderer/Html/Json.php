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
    const IS_TEXT_TEMPLATE = true;

    /**
     * {@inheritdoc}
     */
    const IDENTICAL_RESULT = '[]';

    /**
     * {@inheritdoc}
     */
    const CLOSURES_INS = ["\2", "\3"];
    const CLOSURES_DEL = ["\2", "\4"];

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
}
