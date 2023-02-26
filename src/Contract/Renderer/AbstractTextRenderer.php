<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Contract\Renderer;

use Jfcherng\Diff\Exception\UnsupportedFunctionException;

/**
 * Base renderer for rendering text-based diffs.
 */
abstract class AbstractTextRenderer extends AbstractRenderer
{
    /**
     * Controls whether cliColoredString() is enabled or not.
     */
    protected bool $isCliColorEnabled = false;

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        $this->isCliColorEnabled = (
            $this->options['cliColorization'] === CliColorEnum::Enabled
            || (
                $this->options['cliColorization'] !== CliColorEnum::Disabled
                && \PHP_SAPI === 'cli' && $this->hasColorSupport(\STDOUT)
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultForIdenticalsDefault(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderArrayWorker(array $differArray): string
    {
        throw new UnsupportedFunctionException(__METHOD__);

        return ''; // make IDE not complain
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Colorization is disabled if not supported by the stream:
     *
     * This is tricky on Windows, because Cygwin, Msys2 etc emulate pseudo
     * terminals via named pipes, so we can only check the environment.
     *
     * Reference: Composer\XdebugHandler\Process::supportsColor
     * https://github.com/composer/xdebug-handler
     *
     * @see https://github.com/symfony/console/blob/647c51ff073300a432a4a504e29323cf0d5e0571/Output/StreamOutput.php#L81-L124
     *
     * @param resource $stream
     *
     * @return bool true if the stream supports colorization, false otherwise
     *
     * @suppress PhanUndeclaredFunction
     */
    protected function hasColorSupport($stream): bool
    {
        // Follow https://no-color.org/
        if (isset($_SERVER['NO_COLOR']) || false !== getenv('NO_COLOR')) {
            return false;
        }

        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }

        if (\DIRECTORY_SEPARATOR === '\\') {
            return (\function_exists('sapi_windows_vt100_support')
                && @sapi_windows_vt100_support($stream))
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        if (\function_exists('stream_isatty')) {
            return @stream_isatty($stream);
        }

        if (\function_exists('posix_isatty')) {
            return @posix_isatty($stream);
        }

        $stat = @fstat($stream);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }
}
