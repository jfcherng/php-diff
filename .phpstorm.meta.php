<?php

namespace PHPSTORM_META;

override(
    \Jfcherng\Diff\Factory\LineRendererFactory::getInstance(0),
    map(['' => 'Jfcherng\Diff\Renderer\Html\LineRenderer\@'])
);
override(
    \Jfcherng\Diff\Factory\LineRendererFactory::make(0),
    map(['' => 'Jfcherng\Diff\Renderer\Html\LineRenderer\@'])
);

override(
    \Jfcherng\Diff\Factory\RendererFactory::getInstance(0),
    map([
        'Inline' => \Jfcherng\Diff\Renderer\Html\Inline::class,
        'Json' => \Jfcherng\Diff\Renderer\Html\Json::class,
        'SideBySide' => \Jfcherng\Diff\Renderer\Html\SideBySide::class,
        'Context' => \Jfcherng\Diff\Renderer\Text\Context::class,
        'Unified' => \Jfcherng\Diff\Renderer\Text\Unified::class,
    ])
);
override(
    \Jfcherng\Diff\Factory\RendererFactory::make(0),
    map([
        'Inline' => \Jfcherng\Diff\Renderer\Html\Inline::class,
        'Json' => \Jfcherng\Diff\Renderer\Html\Json::class,
        'SideBySide' => \Jfcherng\Diff\Renderer\Html\SideBySide::class,
        'Context' => \Jfcherng\Diff\Renderer\Text\Context::class,
        'Unified' => \Jfcherng\Diff\Renderer\Text\Unified::class,
    ])
);
