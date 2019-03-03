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
     * @var \Jfcherng\Diff\Utility\Language
     */
    protected $languageObj;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->languageObj = new Language('eng');
    }

    /**
     * Test the Language::setLanguageOrTranslations.
     *
     * @covers \Jfcherng\Diff\Utility\Language::setLanguageOrTranslations
     */
    public function testSetLanguageOrTranslations(): void
    {
        $this->languageObj->setLanguageOrTranslations('eng');
        $this->assertArrayHasKey(
            'differences',
            $this->languageObj->getTranslations()
        );

        $this->languageObj->setLanguageOrTranslations(['hahaha' => '哈哈哈']);
        $this->assertArrayHasKey(
            'hahaha',
            $this->languageObj->getTranslations()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->languageObj->setLanguageOrTranslations(5);
    }

    /**
     * Test the Language::getTranslationsByLanguage.
     *
     * @covers \Jfcherng\Diff\Utility\Language::getTranslationsByLanguage
     */
    public function testGetTranslationsByLanguage(): void
    {
        $this->assertArrayHasKey(
            'differences',
            $this->languageObj->getTranslationsByLanguage('eng')
        );

        $this->expectException(\RuntimeException::class);
        $this->languageObj->getTranslationsByLanguage('a_non_existing_language');
    }

    /**
     * Test the Language::translate.
     *
     * @covers \Jfcherng\Diff\Utility\Language::translate
     */
    public function testTranslate(): void
    {
        $this->assertSame(
            'Differences',
            $this->languageObj->translate('differences')
        );

        $this->assertStringMatchesFormat(
            '![%s]',
            $this->languageObj->translate('a_non_existing_translation')
        );
    }
}
