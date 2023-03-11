<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test;

use Jfcherng\Diff\Contract\Renderer\CliColorEnum;
use Jfcherng\Diff\DiffHelper;
use PHPUnit\Framework\Attributes\DataProvider;
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
    public static function provideIgnoreLineEnding(): array
    {
        return [
            [
                file_get_contents(__DIR__ . '/data/ignore_line_ending/old_1.txt'),
                file_get_contents(__DIR__ . '/data/ignore_line_ending/new_1.txt'),
                <<<'DIFF'
DIFF,
                true,
            ],
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
                false,
            ],
        ];
    }

    #[DataProvider('provideIgnoreLineEnding')]
    public function testIgnoreLineEnding(
        string $old,
        string $new,
        string $expectedDiff,
        bool $gnoreLineEnding,
    ): void {
        $diff = DiffHelper::calculate($old, $new, 'Unified', [
            'ignoreLineEnding' => $gnoreLineEnding,
        ], [
            'cliColorization' => CliColorEnum::Disabled,
        ]);

        static::assertSame($expectedDiff, $diff);
    }
}
