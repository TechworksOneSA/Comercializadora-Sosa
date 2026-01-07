<?php
class Controller {
  protected function view($path, $data = []) {
    extract($data);
    require __DIR__ . "/../" . $path . ".php";
  }

  protected function viewOnly($path, $data = []) {
    extract($data);
    require __DIR__ . "/../modules/" . $path . ".php";
  }

  protected function getLayoutByRole() {
    $userRole = $_SESSION['user']['rol'] ?? 'ADMIN';
    return $userRole === 'VENDEDOR'
      ? 'shared/views/layout_vendedor'
      : 'modules/dashboard/views/_admin_layout';
  }

  protected function viewWithLayout($contentPath, $data = []) {
    $layout = $this->getLayoutByRole();

    // Capturar el contenido de la vista
    ob_start();
    extract($data);
    require __DIR__ . "/../modules/" . $contentPath . ".php";
    $content = ob_get_clean();

    // Renderizar el layout con el contenido
    $data['content'] = $content;
    $this->view($layout, $data);
  }
}
