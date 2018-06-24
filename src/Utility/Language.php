<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

use Exception;
use InvalidArgumentException;

class Language
{
    /**
     * @var string[] the translations
     */
    protected $translations = [];

    /**
     * The constructor.
     *
     * @param string|string[] $langOrTrans The language string or translations array
     *
     * @throws InvalidArgumentException
     */
    public function __construct($langOrTrans = 'eng')
    {
        if (is_string($langOrTrans)) {
            $this->setLangauge($langOrTrans);
        } elseif (is_array($langOrTrans)) {
            $this->translations = $langOrTrans;
        } else {
            throw new InvalidArgumentException('Expect $langOrTrans to be string or array.');
        }
    }

    /**
     * Set the langauge.
     *
     * @param string $language the language
     *
     * @throws Exception Language file not found
     */
    public function setLangauge(string $language): self
    {
        $languageFile = __DIR__ . "/../languages/{$language}.php";

        if (!is_file($languageFile)) {
            throw new Exception("Language `{$language}` not found.");
        }

        $this->translations = require $languageFile;

        return $this;
    }

    /**
     * Get the tranlations.
     *
     * @return array the tranlations
     */
    public function getTranlations(): array
    {
        return $this->translations;
    }

    /**
     * Do the translation.
     *
     * @param string $text the text
     *
     * @return string
     */
    public function translate(string $text): string
    {
        return $this->translations[$text] ?? $text;
    }
}
