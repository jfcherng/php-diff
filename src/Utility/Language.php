<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

use InvalidArgumentException;
use Jfcherng\Diff\Exception\FileNotFoundException;

class Language
{
    /**
     * @var string[] the translations
     */
    protected $translations = [];

    protected $language = '_custom_';

    /**
     * The constructor.
     *
     * @param string|string[] $langOrTrans the language string or translations array
     *
     * @throws InvalidArgumentException
     * @throws FileNotFoundException    language file not found
     */
    public function __construct($langOrTrans = 'eng')
    {
        if (\is_string($langOrTrans)) {
            $this->setTranslations($langOrTrans);

            return;
        }

        if (\is_array($langOrTrans)) {
            $this->setTranslationsFromArray($langOrTrans);

            return;
        }

        throw new InvalidArgumentException('$langOrTrans must be either string or array');
    }

    /**
     * Set the translations by language name.
     *
     * @param string $language the language name
     *
     * @throws FileNotFoundException language file not found
     *
     * @return self
     */
    public function setTranslations(string $language): self
    {
        $this->language = $language;
        $this->translations = $this->getTranslationsByLanguage($language);

        return $this;
    }

    /**
     * Set the translations from an array.
     *
     * @param string[] $translations the language translations array
     *
     * @return self
     */
    public function setTranslationsFromArray(array $translations): self
    {
        $this->language = '_custom_';
        $this->translations = $translations;

        return $this;
    }

    /**
     * Get the language.
     *
     * @return string the language
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Get the translations.
     *
     * @return array the translations
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * Get the translations from the language file.
     *
     * @param string $language the language
     *
     * @throws FileNotFoundException language file not found
     *
     * @return array
     */
    public function getTranslationsByLanguage(string $language): array
    {
        $file = __DIR__ . "/../languages/{$language}.php";

        if (!\is_file($file)) {
            throw new FileNotFoundException($file);
        }

        return require $file;
    }

    /**
     * Translation the text.
     *
     * @param string $text the text
     *
     * @return string
     */
    public function translate(string $text): string
    {
        return $this->translations[$text] ?? "![${text}]";
    }
}
