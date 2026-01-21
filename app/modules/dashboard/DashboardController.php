<?php

require_once __DIR__ . "/DashboardModel.php";

class DashboardController extends Controller
{
  private DashboardModel $model;

  public function __construct()
  {
    $this->model = new DashboardModel();
  }

  public function index()
  {
    RoleMiddleware::requireAdminOrVendedor();

    // Obtener datos para el dashboard
    $ventasHoy = $this->model->obtenerVentasHoy();
    $efectivoCaja = $this->model->obtenerEfectivoEnCaja();
    $gastosHoy = $this->model->obtenerGastosHoy();
    $retirosHoy = $this->model->obtenerRetirosHoy();
    $margenGanancia = $this->model->obtenerMargenGanancia();
    $alertas = $this->model->obtenerAlertasRiesgos();

    $this->viewWithLayout("dashboard/views/dashboard", [
      "title" => "Dashboard Ejecutivo",
      "user" => $_SESSION["user"],
      "ventasHoy" => $ventasHoy,
      "efectivoCaja" => $efectivoCaja,
      "gastosHoy" => $gastosHoy,
      "retirosHoy" => $retirosHoy,
      "margenGanancia" => $margenGanancia,
      "alertas" => $alertas
    ]);
  }
}
