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
final class IgnoreWhitespaceTest extends TestCase
{

    /**
     * @return string[][]
     */
    public function provideIgnoreWhitespaces(): array
    {
        return [
            [
                <<<'OLD'
<?php

function foo(\DateTimeImmutable $date)
{
    if ($date) {
        echo 'foo';
    } else {
        echo 'bar';
    }
}

OLD,
                <<<'NEW'
<?php

function foo(\DateTimeImmutable $date)
{
    echo 'foo';
}

NEW,
                <<<'DIFF'
@@ -2,9 +2,5 @@
 
 function foo(\DateTimeImmutable $date)
 {
-    if ($date) {
         echo 'foo';
-    } else {
-        echo 'bar';
-    }
 }

DIFF
            ],
        ];
    }

    /**
     * @dataProvider provideIgnoreWhitespaces
     */
    public function testIgnoreWhitespaces(string $old, string $new, string $expectedDiff): void
    {
        $diff = DiffHelper::calculate($old, $new, 'Unified', [
            'ignoreWhitespace' => true,
        ], [
            'cliColorization' => RendererConstant::CLI_COLOR_DISABLE,
        ]);

        static::assertSame($expectedDiff, $diff);
    }
}
