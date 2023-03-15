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
        $result = \htmlspecialchars_decode($result);

        /** @todo PHPUnit 9, static::assertStringNotContainsString() */
        static::assertThat($result, static::logicalNot(static::stringContains(';')));
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
            'Combined'
        );

        /** @todo PHPUnit 9, static::assertStringNotContainsString() */
        static::assertThat($result, static::logicalNot(static::stringContains('<tag>')));
        static::assertThat($result, static::logicalNot(static::stringContains('</tag>')));
    }

    /**
     * Test unmerge-able block.
     *
     * @see https://github.com/jfcherng/php-diff/issues/69
     */
    public function testSimpleUnmergeableBlock(): void
    {
        $result = DiffHelper::calculate("111\n222\n333\n", "444\n555\n666\n", 'Combined');

        static::assertSame(
            [1, 1, 1, 1, 1, 1],
            [
                \substr_count($result, '111'),
                \substr_count($result, '222'),
                \substr_count($result, '333'),
                \substr_count($result, '444'),
                \substr_count($result, '555'),
                \substr_count($result, '666'),
            ],
            "Unmerge-able block shouldn't be repeated."
        );
    }
}
