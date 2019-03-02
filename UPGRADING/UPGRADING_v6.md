## Upgrading to v6

- Now `Renderer` has `::render()` API, but a `Diff` does not.
  If you are only using the `DiffHelper`, there should be nothing changed for you.
  But if you combine those classes by yourself, it should be wrote like below.

  ```php
  use Jfcherng\Diff\Diff;
  use Jfcherng\Diff\Factory\RendererFactory;
  
  $diff = new Diff(explode("\n", $old), explode("\n", $new), $diffOptions);
  $renderer = RendererFactory::make($template, $templateOptions);
  $result = $renderer->render($diff); // <-- this has been changed
  ```
