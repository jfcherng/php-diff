<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Factory;

use Jfcherng\Diff\Contract\Renderer\AbstractRenderer;

final class RendererFactory
{
    /**
     * The base namespace of renderers.
     *
     * @var string
     */
    public const BASE_NAMESPACE = '\\Jfcherng\\Diff\\Renderer';

    /**
     * Instances of renderers.
     *
     * @var array<str,AbstractRenderer>
     */
    private static array $singletons = [];

    /**
     * The constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get the singleton of a renderer.
     *
     * @param string $renderer    the renderer
     * @param mixed  ...$ctorArgs the constructor arguments
     */
    public static function getInstance(string $renderer, mixed ...$ctorArgs): AbstractRenderer
    {
        return self::$singletons[$renderer] ??= self::make($renderer, ...$ctorArgs);
    }

    /**
     * Make a new instance of a renderer.
     *
     * @param string $renderer    the renderer
     * @param mixed  ...$ctorArgs the constructor arguments
     *
     * @throws \InvalidArgumentException
     */
    public static function make(string $renderer, mixed ...$ctorArgs): AbstractRenderer
    {
        $className = self::resolveFqcn($renderer);

        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Renderer not found: {$renderer}");
        }

        return new $className(...$ctorArgs);
    }

    /**
     * Resolve the renderer name into a FQCN.
     *
     * @param string $renderer the renderer
     */
    public static function resolveFqcn(string $renderer): string
    {
        return self::BASE_NAMESPACE . '\\' . ucfirst($renderer);
    }
}
