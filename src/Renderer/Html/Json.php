<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

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
    public static function getIdenticalResult(): string
    {
        return '[]';
    }

    /**
     * {@inheritdoc}
     */
    protected function formatStringFromLines(string $string): string
    {
        return $this->htmlSafe($string);
    }
}
