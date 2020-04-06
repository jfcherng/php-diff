<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Exception\UnsupportedFunctionException;
use Jfcherng\Diff\Renderer\AbstractRenderer;
use Jfcherng\Diff\Renderer\RendererConstant;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base renderer for rendering text-based diffs.
 */
abstract class AbstractText extends AbstractRenderer
{
    /**
     * @var bool is this renderer pure text?
     */
    const IS_TEXT_RENDERER = true;

    /**
     * @var string the diff output representing there is no EOL at EOF in the GNU diff tool
     */
    const GNU_OUTPUT_NO_EOL_AT_EOF = '\ No newline at end of file';

    /**
     * @var BufferedOutput
     */
    protected $bufferOutput;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        $this->bufferOutput = $this->getCliOutputter();

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): AbstractRenderer
    {
        parent::setOptions($options);

        if ($this->options['cliColorization'] === RendererConstant::CLI_COLOR_ENABLE) {
            $this->bufferOutput->setDecorated(true);
        } elseif ($this->options['cliColorization'] === RendererConstant::CLI_COLOR_DISABLE) {
            $this->bufferOutput->setDecorated(false);
        } else {
            $this->bufferOutput->setDecorated($this->isCliColorSupported());
        }

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
     * Get the cli outputter object.
     */
    protected function getCliOutputter(): OutputInterface
    {
        return new BufferedOutput(
            OutputInterface::VERBOSITY_NORMAL,
            false,
            new OutputFormatter(
                false,
                [
                    'header' => new OutputFormatterStyle('magenta', null, []),
                    'deleted' => new OutputFormatterStyle('red', null, ['bold']),
                    'inserted' => new OutputFormatterStyle('green', null, ['bold']),
                    'replaced' => new OutputFormatterStyle('yellow', null, ['bold']),
                ]
            )
        );
    }

    /**
     * Colorize the string for CLI output.
     *
     * @param string      $str   the string
     * @param null|string $style the style
     *
     * @return string the (maybe) colorized string
     */
    protected function cliColoredString(string $str, ?string $style = null): string
    {
        static $symbolStyleMap = [
            '@' => 'header',
            '-' => 'deleted',
            '+' => 'inserted',
            '!' => 'replaced',
        ];

        // just to reduce the amount of function calls
        if (!$this->bufferOutput->isDecorated()) {
            return $str;
        }

        // convert symbol into style
        if (\is_string($style) && isset($style[0]) && !\ctype_alpha($style[0])) {
            $style = $symbolStyleMap[$style] ?? null;
        }

        $str = OutputFormatter::escape($str);

        $this->bufferOutput->write(isset($style) ? "<{$style}>{$str}</>" : $str);

        return $this->bufferOutput->fetch();
    }

    /**
     * Determine if cli color supported.
     */
    protected function isCliColorSupported(): bool
    {
        static $isSupported;

        if (isset($isSupported)) {
            return $isSupported;
        }

        $stream = \fopen('php://stdout', 'w');
        $isSupported = \PHP_SAPI === 'cli' && $this->hasColorSupport($stream);
        \fclose($stream);

        return $isSupported;
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
     * @see https://github.com/symfony/console/blob/4.4/Output/StreamOutput.php#L94
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
        if (isset($_SERVER['NO_COLOR']) || false !== \getenv('NO_COLOR')) {
            return false;
        }

        if ('Hyper' === \getenv('TERM_PROGRAM')) {
            return true;
        }

        if (\DIRECTORY_SEPARATOR === '\\') {
            return (\function_exists('sapi_windows_vt100_support')
                && @\sapi_windows_vt100_support($stream))
                || false !== \getenv('ANSICON')
                || 'ON' === \getenv('ConEmuANSI')
                || 'xterm' === \getenv('TERM');
        }

        if (\function_exists('stream_isatty')) {
            return @\stream_isatty($stream);
        }

        if (\function_exists('posix_isatty')) {
            return @posix_isatty($stream);
        }

        $stat = @\fstat($stream);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }
}
