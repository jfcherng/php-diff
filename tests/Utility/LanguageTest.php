<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility\Test;

use Jfcherng\Diff\Utility\Language;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @internal
 */
final class LanguageTest extends TestCase
{
    /**
     * The Language object.
     *
     * @var Language
     */
    protected $languageObj;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->languageObj = new Language();
    }

    /**
     * Test the Language::load.
     *
     * @covers \Jfcherng\Diff\Utility\Language::load
     */
    public function testLoad(): void
    {
        $this->languageObj->load('eng');
        self::assertArrayHasKey('differences', $this->languageObj->getTranslations());

        $this->languageObj->load(['hahaha' => '哈哈哈']);
        self::assertArrayHasKey('hahaha', $this->languageObj->getTranslations());

        $this->languageObj->load([
            'eng',
            ['hahaha_1' => '哈哈哈_1', 'hahaha_2' => '哈哈哈_2'],
            ['hahaha_1' => '哈哈哈_999'],
        ]);
        $translations = $this->languageObj->getTranslations();
        self::assertSame('Differences', $translations['differences']);
        self::assertSame('哈哈哈_999', $translations['hahaha_1']);
        self::assertSame('哈哈哈_2', $translations['hahaha_2']);

        $this->expectException(\InvalidArgumentException::class);
        $this->languageObj->load(5);
    }

    /**
     * Test the Language::translate.
     *
     * @covers \Jfcherng\Diff\Utility\Language::translate
     */
    public function testTranslate(): void
    {
        self::assertSame(
            'Differences',
            $this->languageObj->translate('differences')
        );

        self::assertStringMatchesFormat(
            '![%s]',
            $this->languageObj->translate('a_non_existing_key')
        );
    }
}
