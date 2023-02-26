<?php

declare(strict_types=1);

namespace PHPSTORM_META;

override(
    \Jfcherng\Diff\Factory\LineRendererFactory::getInstance(0),
    map(['' => 'Jfcherng\Diff\LineRenderer\@']),
);
override(
    \Jfcherng\Diff\Factory\LineRendererFactory::make(0),
    map(['' => 'Jfcherng\Diff\LineRenderer\@']),
);
override(
    \Jfcherng\Diff\Factory\RendererFactory::getInstance(0),
    map(['' => 'Jfcherng\Diff\Renderer\@']),
);
override(
    \Jfcherng\Diff\Factory\RendererFactory::make(0),
    map(['' => 'Jfcherng\Diff\Renderer\@']),
);
