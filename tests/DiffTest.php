<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test;

use Jfcherng\Diff\Diff;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class DiffTest extends TestCase
{
    /**
     * Data provider for Diff::getGroupedOpcodes.
     *
     * @return array the data provider
     */
    public function getGroupedOpcodesDataProvider(): array
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
                    [
                        ['eq', 0, 1, 0, 1],
                        ['del', 1, 2, 1, 1],
                        ['eq', 2, 4, 1, 3],
                        ['ins', 4, 4, 3, 4],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test the Diff::getGroupedOpcodes.
     *
     * @covers       \Jfcherng\Diff\Diff::getGroupedOpcodes
     * @dataProvider getGroupedOpcodesDataProvider
     *
     * @param string $old      the old
     * @param string $new      the new
     * @param array  $expected the expected
     */
    public function testGetGroupedOpcodes(string $old, string $new, array $expected): void
    {
        $old = \explode("\n", $old);
        $new = \explode("\n", $new);

        $this->assertSame(
            $expected,
            (new Diff($old, $new))->getGroupedOpcodes()
        );
    }
}
