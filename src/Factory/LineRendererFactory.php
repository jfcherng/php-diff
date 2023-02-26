<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Factory;

use Jfcherng\Diff\Contract\LineRenderer\AbstractLineRenderer;

final class LineRendererFactory
{
    /**
     * The base namespace of line renderers.
     *
     * @var string
     */
    public const BASE_NAMESPACE = '\\Jfcherng\\Diff\\LineRenderer';

    /**
     * Instances of line renderers.
     *
     * @var array<str,AbstractLineRenderer>
     */
    private static array $singletons = [];

    /**
     * The constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get the singleton of a line renderer.
     *
     * @param string $renderer    the renderer
     * @param mixed  ...$ctorArgs the constructor arguments
     */
    public static function getInstance(string $renderer, mixed ...$ctorArgs): AbstractLineRenderer
    {
        return self::$singletons[$renderer] ??= self::make($renderer, ...$ctorArgs);
    }

    /**
     * Make a new instance of a line renderer.
     *
     * @param string $renderer    the renderer
     * @param mixed  ...$ctorArgs the constructor arguments
     *
     * @throws \InvalidArgumentException
     */
    public static function make(string $renderer, mixed ...$ctorArgs): AbstractLineRenderer
    {
        $className = self::resolveFqcn($renderer);

        if (!class_exists($className)) {
            throw new \InvalidArgumentException("LineRenderer not found: {$renderer}");
        }

        return new $className(...$ctorArgs);
    }

    /**
     * Resolve the line renderer name into a FQCN.
     *
     * @param string $renderer the renderer
     */
    public static function resolveFqcn(string $renderer): string
    {
        return self::BASE_NAMESPACE . '\\' . ucfirst($renderer);
    }
}
