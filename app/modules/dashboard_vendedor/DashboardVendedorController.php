<?php

require_once __DIR__ . "/DashboardVendedorModel.php";

class DashboardVendedorController extends Controller
{
  private DashboardVendedorModel $model;

  public function __construct()
  {
    $this->model = new DashboardVendedorModel();
  }

  public function index()
  {
    RoleMiddleware::requireVendedor();

    $usuarioId = $_SESSION['user']['id'];

    // Obtener datos para el dashboard del vendedor
    $ventasHoy = $this->model->obtenerVentasHoy();
    $misVentas = $this->model->obtenerMisVentas($usuarioId);
    $efectivoCaja = $this->model->obtenerEfectivoEnCaja();
    $alertas = $this->model->obtenerAlertas();

    $this->viewWithLayout("dashboard_vendedor/views/index", [
      "title" => "Panel Vendedor",
      "user" => $_SESSION["user"],
      "ventasHoy" => $ventasHoy,
      "misVentas" => $misVentas,
      "efectivoCaja" => $efectivoCaja,
      "alertas" => $alertas
    ]);
  }
}
