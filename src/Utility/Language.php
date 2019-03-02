<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

final class Language
{
    /**
     * @var string[] the translation dict
     */
    private $translations = [];

    /**
     * @var string the language name
     */
    private $language = '_custom_';

    /**
     * The constructor.
     *
     * @param string|string[] $langOrTrans the language string or translations array
     */
    public function __construct($langOrTrans = 'eng')
    {
        $this->setLanguageOrTranslations($langOrTrans);
    }

    /**
     * Set the language name.
     *
     * @param string|string[] $langOrTrans the language string or translations array
     *
     * @throws \InvalidArgumentException
     *
     * @return self
     */
    public function setLanguageOrTranslations($langOrTrans): self
    {
        if (\is_string($langOrTrans)) {
            $this->setLanguage($langOrTrans);

            return $this;
        }

        if (\is_array($langOrTrans)) {
            $this->setTranslations($langOrTrans);

            return $this;
        }

        throw new \InvalidArgumentException('$langOrTrans must be either string or array');
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
     * @throws \Exception        fail to decode the JSON file
     * @throws \LogicException   path is a directory
     * @throws \RuntimeException path cannot be opened
     *
     * @return string[]
     */
    public function getTranslationsByLanguage(string $language): array
    {
        $filePath = __DIR__ . "/../languages/{$language}.json";
        $file = new \SplFileObject($filePath, 'r');
        $fileContent = $file->fread($file->getSize());

        /** @todo PHP ^7.3 JSON_THROW_ON_ERROR */
        if (($decoded = \json_decode($fileContent, true)) === null) {
            throw new \Exception("Fail to decode JSON file: {$filePath}");
        }

        return $decoded;
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
        return $this->translations[$text] ?? "![{$text}]";
    }

    /**
     * Set the language name.
     *
     * @param string $language the language name
     *
     * @return self
     */
    private function setLanguage(string $language): self
    {
        $this->language = $language;
        $this->translations = $this->getTranslationsByLanguage($language);

        return $this;
    }

    /**
     * Set the translations.
     *
     * @param string[] $translations the language translations array
     *
     * @return self
     */
    private function setTranslations(array $translations): self
    {
        $this->language = '_custom_';
        $this->translations = $translations;

        return $this;
    }
}
