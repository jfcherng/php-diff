<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test\Renderer\Html;

use Jfcherng\Diff\DiffHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test Combined.
 *
 * @covers \Jfcherng\Diff\Renderer\Html\Combined
 *
 * @internal
 */
final class CombinedTest extends TestCase
{
    /**
     * Test the internal HTML output formatting.
     *
     * @see https://github.com/jfcherng/php-diff/issues/30
     */
    public function testHtmlFormatting(): void
    {
        $result = DiffHelper::calculate('<', " \nA<B", 'Combined', ['detailLevel' => 'word']);
        $result = htmlspecialchars_decode($result);

        static::assertStringNotContainsString(';', $result);
    }

    /**
     * Test HTML escape for diff output.
     *
     * @see https://github.com/jfcherng/php-diff/issues/33
     */
    public function testHtmlEscapeForOpEq(): void
    {
        $result = DiffHelper::calculate(
            "<tag>three</tag>\n<tag>four</tag>\n",
            "one\n<tag>two</tag>\n<tag>three</tag>\n",
            'Combined',
        );

        static::assertStringNotContainsString('<tag>', $result);
        static::assertStringNotContainsString('</tag>', $result);
    }
}
