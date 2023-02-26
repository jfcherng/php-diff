<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Contract\Renderer;

enum RendererTypeEnum: string
{
    case Text = 'text';
    case Html = 'html';
}
