<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test\Renderer\Html;

use Jfcherng\Diff\DiffHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test Combined.
 *
 * @coversNothing
 *
 * @internal
 */
final class CombinedTest extends TestCase
{
    /**
     * Test the internal HTML output formatting.
     *
     * @covers \Jfcherng\Diff\Renderer\Html\Combined
     *
     * @see https://github.com/jfcherng/php-diff/issues/30
     */
    public function testHtmlFormatting(): void
    {
        $result = DiffHelper::calculate('<', " \nA<B", 'Combined', ['detailLevel' => 'word']);
        $result = \htmlspecialchars_decode($result);

        static::assertThat($result, static::logicalNot(static::stringContains(';')));
    }
}
