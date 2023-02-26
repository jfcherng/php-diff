<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

final class Language
{
    /**
     * @var array<string,string> the translation dict
     */
    private array $translations = [];

    /**
     * The language name.
     */
    private string $language = '_custom_';

    /**
     * The constructor.
     *
     * @param array<int,string|string[]>|string|string[] $target the language ID or translations dict
     */
    public function __construct($target = 'eng')
    {
        $this->load($target);
    }

    /**
     * Gets the language.
     *
     * @return string the language
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Gets the translations.
     *
     * @return array<string,string> the translations
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * Loads the target language.
     *
     * @param array<int,string|string[]>|string|string[] $target the language ID or translations dict
     */
    public function load($target): void
    {
        $this->translations = $this->resolve($target);
        $this->language = \is_string($target) ? $target : '_custom_';
    }

    /**
     * Translates the text.
     *
     * @param string $text the text
     */
    public function translate(string $text): string
    {
        return $this->translations[$text] ?? "![{$text}]";
    }

    /**
     * Get the translations from the language file.
     *
     * @param string $language the language
     *
     * @return array<string,string>
     */
    private static function getTranslationsByLanguage(string $language): array
    {
        static $cache = [];

        if (!ctype_alpha($language)) {
            throw new \Exception('Language ID should contain only letters.');
        }

        return $cache[$language] ??= (array) require __DIR__ . "/../languages/{$language}.php";
    }

    /**
     * Resolves the target language.
     *
     * @param array<int,string|string[]>|string|string[] $target the language ID or translations array
     *
     * @throws \InvalidArgumentException
     *
     * @return array<string,string> the resolved translations
     */
    private function resolve($target): array
    {
        if (\is_string($target)) {
            return self::getTranslationsByLanguage($target);
        }

        if (\is_array($target)) {
            // $target is an associative array
            if (!array_is_list($target)) {
                /** @phan-suppress-next-line PhanTypeMismatchReturn */
                return $target;
            }

            // $target is a list of "key-value pairs or language ID"
            return array_reduce(
                $target,
                fn (array $carry, $translation): array => [...$carry, ...$this->resolve($translation)],
                [],
            );
        }

        throw new \InvalidArgumentException('$target is not in valid form');
    }
}
