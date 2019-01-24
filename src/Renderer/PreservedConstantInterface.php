<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

interface PreservedConstantInterface
{
    /**
     * Closures that are used to enclose partial strings.
     *
     * - a different part in string (class internal)
     * - a inserted char in output HTML
     * - a deleted char in output HTML
     *
     * @var string[]
     */
    const HTML_CLOSURES = ["\u{fcffc}\u{ff2fb}", "\u{fff41}\u{fcffc}"];
    const HTML_CLOSURES_INS = ['<ins>', '</ins>'];
    const HTML_CLOSURES_DEL = ['<del>', '</del>'];

    /**
     * The delimiter to be used as the glue in string/array functions.
     *
     * this delimiter contains chars from the Unicode reserved areas
     * hopefully, it won't appear in our lines
     *
     * @var string
     */
    const IMPLODE_DELIMITER = "\u{ff2fa}\u{fcffc}\u{fff42}";
}
