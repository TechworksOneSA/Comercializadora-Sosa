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

    // =========================
    // Datos base (con fallback)
    // =========================
    $ventasHoy = $this->model->obtenerVentasHoy() ?: [
      'cantidad_ventas' => 0,
      'total_ventas' => 0,
      'ventas_efectivo' => 0,
      'ventas_otros' => 0
    ];

    $efectivoCaja = (float)($this->model->obtenerEfectivoEnCaja() ?? 0);

    $gastosHoy = $this->model->obtenerGastosHoy() ?: [
      'cantidad_gastos' => 0,
      'total_gastos' => 0
    ];

    $retirosHoy = $this->model->obtenerRetirosHoy() ?: [
      'cantidad_retiros' => 0,
      'total_retiros' => 0
    ];

    // =========================
    // KPIs PRO (día / mes)
    // =========================
    $margenGanancia = $this->model->obtenerMargenGanancia() ?: [
      'ventas_dia' => 0,
      'cogs_dia' => 0,
      'gastos_dia' => 0,
      'reversas_dia' => 0,
      'ganancia_bruta' => 0,
      'ganancia_real' => 0,
      'porcentaje_margen' => 0
    ];

    // Normalizar keys por si su modelo viejo devolvía otra estructura
    $margenGanancia = array_merge([
      'ventas_dia' => 0,
      'cogs_dia' => 0,
      'gastos_dia' => 0,
      'reversas_dia' => 0,
      'ganancia_bruta' => 0,
      'ganancia_real' => 0,
      'porcentaje_margen' => 0
    ], $margenGanancia);

    $gananciasMes = $this->model->obtenerGananciasMes() ?: [
      'ventas_mes' => 0,
      'costo_ventas_mes' => 0,
      'gastos_mes' => 0,
      'reversas_mes' => 0,
      'ganancia_bruta_mes' => 0,
      'ganancias_mes' => 0
    ];

    $gananciasMes = array_merge([
      'ventas_mes' => 0,
      'costo_ventas_mes' => 0,
      'gastos_mes' => 0,
      'reversas_mes' => 0,
      'ganancia_bruta_mes' => 0,
      'ganancias_mes' => 0
    ], $gananciasMes);

    $alertas = $this->model->obtenerAlertasRiesgos() ?: [
      'productos_bajo_stock' => 0,
      'productos_sin_stock' => 0,
      'ventas_por_cobrar_cantidad' => 0,
      'ventas_por_cobrar_monto' => 0
    ];

    // =========================
    // Render
    // =========================
    $this->viewWithLayout("dashboard/views/dashboard", [
      "title" => "Dashboard Ejecutivo",
      "user" => $_SESSION["user"],

      "ventasHoy" => $ventasHoy,
      "efectivoCaja" => $efectivoCaja,
      "gastosHoy" => $gastosHoy,
      "retirosHoy" => $retirosHoy,

      "margenGanancia" => $margenGanancia,
      "gananciasMes" => $gananciasMes,

      "alertas" => $alertas
    ]);
  }
}
