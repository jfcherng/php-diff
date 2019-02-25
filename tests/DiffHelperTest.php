<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test;

use Jfcherng\Diff\DiffHelper;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class DiffHelperTest extends TestCase
{
    /**
     * Data provider for DiffHelper::calculate.
     *
     * @return array the data provider
     */
    public function calculateDataProvider(): array
    {
        return [
            [
                <<<'EOT'
apples
oranges
kiwis
carrots
EOT
                ,
                <<<'EOT'
apples
kiwis
carrots
grapefruits
EOT
                ,
                [
                    'Unified' => <<<'EOT'
@@ -1,4 +1,4 @@
 apples
-oranges
 kiwis
 carrots
+grapefruits

EOT
                    ,
                    'Context' => <<<'EOT'
***************
*** 1,4 ****
  apples
- oranges
  kiwis
  carrots
--- 1,4 ----
  apples
  kiwis
  carrots
+ grapefruits

EOT
                    ,
                ],
            ],
        ];
    }

    /**
     * Test the DiffHelper::calculate with the 'Unified' template.
     *
     * @covers       \Jfcherng\Diff\DiffHelper::calculate
     * @dataProvider calculateDataProvider
     *
     * @param string $old       the old
     * @param string $new       the new
     * @param array  $expecteds the expecteds
     */
    public function testCalculateUnified(string $old, string $new, array $expecteds): void
    {
        $this->assertSame(
            $expecteds['Unified'],
            DiffHelper::calculate($old, $new, 'Unified')
        );
    }

    /**
     * Test the DiffHelper::calculate with the 'Context' template.
     *
     * @covers       \Jfcherng\Diff\DiffHelper::calculate
     * @dataProvider calculateDataProvider
     *
     * @param string $old       the old
     * @param string $new       the new
     * @param array  $expecteds the expecteds
     */
    public function testCalculateContext(string $old, string $new, array $expecteds): void
    {
        $this->assertSame(
            $expecteds['Context'],
            DiffHelper::calculate($old, $new, 'Context')
        );
    }

    /**
     * Test the DiffHelper::getStyleSheet.
     *
     * @covers \Jfcherng\Diff\DiffHelper::getStyleSheet
     */
    public function testGetStyleSheet(): void
    {
        $this->assertIsString(DiffHelper::getStyleSheet());
    }
}
