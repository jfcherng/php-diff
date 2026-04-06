<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Options;

/**
 * Value object holding all options for the Differ.
 */
readonly class DifferOptions
{
    public function __construct(
        /** Show how many neighbour lines of context. Use Differ::CONTEXT_ALL to show the whole file. */
        public int $context = 3,
        /** Ignore case differences. */
        public bool $ignoreCase = false,
        /** Ignore line-ending differences. */
        public bool $ignoreLineEnding = false,
        /** Ignore whitespace differences. */
        public bool $ignoreWhitespace = false,
        /** If the input sequence is too long, give up (especially for char-level diff). */
        public int $lengthLimit = 2000,
        /** When inputs are identical, render the whole content rather than an empty result. */
        public bool $fullContextIfIdentical = false,
    ) {
    }

    /**
     * Create a DifferOptions from a legacy associative array, filling missing keys with defaults.
     */
    public static function fromArray(array $options): self
    {
        return new self(
            context: $options['context'] ?? 3,
            ignoreCase: $options['ignoreCase'] ?? false,
            ignoreLineEnding: $options['ignoreLineEnding'] ?? false,
            ignoreWhitespace: $options['ignoreWhitespace'] ?? false,
            lengthLimit: $options['lengthLimit'] ?? 2000,
            fullContextIfIdentical: $options['fullContextIfIdentical'] ?? false,
        );
    }

    /**
     * Convert to associative array for external consumers that still expect arrays (e.g. SequenceMatcher).
     *
     * @return array{context:int,ignoreCase:bool,ignoreLineEnding:bool,ignoreWhitespace:bool,lengthLimit:int,fullContextIfIdentical:bool}
     */
    public function toArray(): array
    {
        return [
            'context' => $this->context,
            'ignoreCase' => $this->ignoreCase,
            'ignoreLineEnding' => $this->ignoreLineEnding,
            'ignoreWhitespace' => $this->ignoreWhitespace,
            'lengthLimit' => $this->lengthLimit,
            'fullContextIfIdentical' => $this->fullContextIfIdentical,
        ];
    }
}
