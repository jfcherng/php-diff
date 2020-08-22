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
    public function testIgnoreWhitespaces(): void
    {
        $old = <<<'PHP'
<?php

function foo(\DateTimeImmutable $date)
{
    if ($date) {
        echo 'foo';
    } else {
        echo 'bar';
    }
}

PHP;
        $new = <<<'PHP'
<?php

function foo(\DateTimeImmutable $date)
{
    echo 'foo';
}

PHP;

        $expected = <<<'DIFF'
@@ -2,9 +2,5 @@
 
 function foo(\DateTimeImmutable $date)
 {
-    if ($date) {
         echo 'foo';
-    } else {
-        echo 'bar';
-    }
 }

DIFF;

        $diff = DiffHelper::calculate($old, $new, 'Unified', [
            'ignoreWhitespace' => true,
        ], [
            'cliColorization' => RendererConstant::CLI_COLOR_DISABLE,
        ]);

        static::assertSame($expected, $diff);
    }
}
