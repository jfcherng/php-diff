<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test;

use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Renderer\RendererConstant;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class IgnoreLineEndingTest extends TestCase
{
    /**
     * @return string[][]
     */
    public function provideIgnoreLineEndingTrue(): array
    {
        return [
            [
                file_get_contents(__DIR__ . '/data/ignore_line_ending/old_1.txt'),
                file_get_contents(__DIR__ . '/data/ignore_line_ending/new_1.txt'),
                <<<'DIFF'
DIFF,
            ],
        ];
    }

    /**
     * @return string[][]
     */
    public function provideIgnoreLineEndingFalse(): array
    {
        return [
            [
                file_get_contents(__DIR__ . '/data/ignore_line_ending/old_1.txt'),
                file_get_contents(__DIR__ . '/data/ignore_line_ending/new_1.txt'),
                <<<"DIFF"
@@ -1,2 +1,2 @@
-line 1\r
-line 2\r
+line 1
+line 2

DIFF,
            ],
        ];
    }

    /**
     * @dataProvider provideIgnoreLineEndingTrue
     */
    public function testIgnoreLineEndingTrue(string $old, string $new, string $expectedDiff): void
    {
        $diff = DiffHelper::calculate($old, $new, 'Unified', [
            'ignoreLineEnding' => true,
        ], [
            'cliColorization' => RendererConstant::CLI_COLOR_DISABLE,
        ]);

        static::assertSame($expectedDiff, $diff);
    }

    /**
     * @dataProvider provideIgnoreLineEndingFalse
     */
    public function testIgnoreLineEndingFalse(string $old, string $new, string $expectedDiff): void
    {
        $diff = DiffHelper::calculate($old, $new, 'Unified', [
            'ignoreLineEnding' => false,
        ], [
            'cliColorization' => RendererConstant::CLI_COLOR_DISABLE,
        ]);

        static::assertSame($expectedDiff, $diff);
    }
}
