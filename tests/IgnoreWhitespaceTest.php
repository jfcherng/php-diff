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
    public static function provideIgnoreWhitespacesCases(): iterable
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

OLD
                ,
                <<<'NEW'
<?php

function foo(\DateTimeImmutable $date)
{
    echo 'foo';
}

NEW
                ,
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
            [
                <<<'OLD'
<?php

class Foo
{
	function foo()
	{
		echo 'haha';
		return;

		echo 'blabla';
		if (false) {

		}
	}

}

OLD
                ,
                <<<'NEW'
<?php

class Foo
{
	function foo()
	{
		echo 'haha';
		return;
	}

}

NEW
                ,
                <<<'DIFF'
@@ -6,11 +6,6 @@
 	{
 		echo 'haha';
 		return;
-
-		echo 'blabla';
-		if (false) {
-
-		}
 	}
 
 }

DIFF
            ],
            [
                file_get_contents(__DIR__ . '/data/ignore_whitespace/old_1.php'),
                file_get_contents(__DIR__ . '/data/ignore_whitespace/new_1.php'),
                <<<'DIFF'
@@ -215,11 +215,6 @@
 	{
 		echo 'haha';
 		return;
-
-		echo 'blabla';
-		if (false) {
-
-		}
 	}
 
 }

DIFF
            ],
        ];
    }

    /**
     * @dataProvider provideIgnoreWhitespacesCases
     */
    public function testIgnoreWhitespaces(string $old, string $new, string $expectedDiff): void
    {
        $diff = DiffHelper::calculate($old, $new, 'Unified', [
            'ignoreWhitespace' => true,
        ], [
            'cliColorization' => RendererConstant::CLI_COLOR_DISABLE,
        ]);

        self::assertSame($expectedDiff, $diff);
    }
}
