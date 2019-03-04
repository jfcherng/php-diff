## Upgrading to v6


### For Simple User

If you only use the `DiffHelper` and built-in `Renderer`s,
there is no breaking change for you so you do not have to do anything.


### External Breaking Changes

- The `Diff` class has been renamed to `Differ`.
  It's easy to adapt to this by changing the class name.

- The term `template` has been renamed to `renderer`. Some examples are:

  - Method `DiffHelper::getRenderersInfo()`
  - Method `DiffHelper::getAvailableRenderers()`
  - Constant `RendererConstant::RENDERER_TYPES`
  - Constant `AbstractRenderer::IS_TEXT_RENDERER`

- Now a `Renderer` has a `render()` method, but a `Differ` does not.
  If you use those classes by yourself, it should be written like below.

  ```php
  use Jfcherng\Diff\Differ;
  use Jfcherng\Diff\Factory\RendererFactory;
  
  $differ = new Differ(explode("\n", $old), explode("\n", $new), $diffOptions);
  $renderer = RendererFactory::make($renderer, $rendererOptions);
  $result = $renderer->render($differ); // <-- this has been changed
  ```

- Add `RendererInterface::getResultForIdenticals()`.
  `AbstractRenderer::getResultForIdenticals()` returns an empty string by default.

- Remove the deprecated `AbstractRenderer::getIdenticalResult()`.
  Use/implement the `AbstractRenderer::getResultForIdenticals()` instead.


### Internal Breaking Changes

- Now a `Renderer` should implement `protected function renderWoker(Differ $differ): string`
  rather than the previous `public function render(): string`. Note that `$this->diff` no longer
  works in `Renderer`s as it is now injected as the parameter to `Renderer::renderWoker()`.
