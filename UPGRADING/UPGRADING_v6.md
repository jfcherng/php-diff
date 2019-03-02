## Upgrading to v6

- `Diff` has been renamed to `Differ`.
- Now a `Renderer` has `render()` API, but a `Differ` does not.
  If you are only using the `DiffHelper`, there should be nothing changed for you.
  But if you combine those classes by yourself, it should be wrote like below.

  ```php
  use Jfcherng\Diff\Differ;
  use Jfcherng\Diff\Factory\RendererFactory;
  
  $differ = new Differ(explode("\n", $old), explode("\n", $new), $diffOptions);
  $renderer = RendererFactory::make($template, $templateOptions);
  $result = $renderer->render($differ); // <-- this has been changed
  ```
