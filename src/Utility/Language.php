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
        $this->setTranlations($langOrTrans);
    }

    /**
     * Set the tranlations.
     *
     * @param string|string[] $langOrTrans The language string or translations array
     *
     * @throws InvalidArgumentException
     *
     * @return array the tranlations
     */
    public function setTranlations($langOrTrans): self
    {
        if (is_string($langOrTrans)) {
            $this->translations = $this->getTranlationsByLanguage($langOrTrans);
        } elseif (is_array($langOrTrans)) {
            $this->translations = $langOrTrans;
        } else {
            throw new InvalidArgumentException('Expect $langOrTrans to be string or array.');
        }

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
     * Get the tranlations from the language file.
     *
     * @param string $language the language
     *
     * @throws Exception Language file not found
     *
     * @return array
     */
    public function getTranlationsByLanguage(string $language): array
    {
        $file = __DIR__ . "/../languages/{$language}.php";

        if (!is_file($file)) {
            throw new Exception("Language `{$language}` not found.");
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
