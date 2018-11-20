<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Exception;

use Exception;
use Throwable;

class FileNotFoundException extends Exception
{
    public function __construct(string $filepath = '', int $code = 0, Throwable $previous = null)
    {
        $this->message = "File not found: {$filepath}";
        $this->code = $code;
    }
}
