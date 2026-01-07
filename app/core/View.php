<?php
class View {
  public static function render($path, $data = []) {
    extract($data);
    require __DIR__ . "/../" . $path . ".php";
  }
}
