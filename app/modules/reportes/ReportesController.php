<?php

require_once __DIR__ . "/ReportesModel.php";

class ReportesController extends Controller
{
    private ReportesModel $model;

    public function __construct()
    {
        $this->model = new ReportesModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdmin();

        // Obtener resumen general
        $resumenVentas = $this->model->obtenerResumenVentas();
        $resumenCompras = $this->model->obtenerResumenCompras();
        $resumenInventario = $this->model->obtenerResumenInventario();
        $productosMasVendidos = $this->model->obtenerProductosMasVendidos(10);

        $this->viewWithLayout("reportes/views/index", [
            "title" => "Reportes y Estadísticas",
            "user" => $_SESSION["user"],
            "resumenVentas" => $resumenVentas,
            "resumenCompras" => $resumenCompras,
            "resumenInventario" => $resumenInventario,
            "productosMasVendidos" => $productosMasVendidos,
        ]);
    }

    public function ventas()
    {
        RoleMiddleware::requireAdmin();

        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $ventas = $this->model->obtenerReporteVentas($fechaInicio, $fechaFin);
        $ventasPorDia = $this->model->obtenerVentasPorDia($fechaInicio, $fechaFin);
        $resumen = $this->model->obtenerResumenVentasPeriodo($fechaInicio, $fechaFin);

        // Manejo de exportación a Excel
        if (isset($_GET['exportar']) && $_GET['exportar'] === 'excel') {
            $this->exportarVentasExcel($fechaInicio, $fechaFin, $ventas, $resumen);
            return;
        }

        $this->viewWithLayout("reportes/views/ventas", [
            "title" => "Reporte de Ventas",
            "user" => $_SESSION["user"],
            "ventas" => $ventas,
            "ventasPorDia" => $ventasPorDia,
            "resumenPeriodo" => $resumen,
            "fechaInicio" => $fechaInicio,
            "fechaFin" => $fechaFin,
        ]);
    }

    /**
     * Exportación a "Excel" vía HTML (.xls)
     * Reglas:
     * - NO anteponer "Q" (exportar números)
     * - "Total Ventas" = CANTIDAD de ventas realizadas (transacciones)
     * - Quitar "Promedio por venta"
     * - Subtotal = total de todas las ventas según el filtro (monto)
     */
    private function exportarVentasExcel($fechaInicio, $fechaFin, $ventas, $resumen)
    {
        // Cantidad de ventas según filtro (transacciones)
        $cantidadVentas = is_array($ventas) ? count($ventas) : 0;

        // Totales (monto) según filtro (fuente de verdad: listado de ventas)
        $totalFiltro = 0.0;
        $subtotalFiltro = 0.0;

        foreach ($ventas as $v) {
            $totalFiltro += (float)($v['total'] ?? 0);
            $subtotalFiltro += (float)($v['subtotal'] ?? 0);
        }

        // Usted pidió que "Subtotal" muestre el total de todas las ventas según el filtro
        $subtotalParaMostrar = $totalFiltro;

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Ventas_' . $fechaInicio . '_' . $fechaFin . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF"; // BOM para UTF-8

        echo "<html><head><meta charset='UTF-8'></head><body>";
        echo "<h2>Reporte de Ventas del {$fechaInicio} al {$fechaFin}</h2>";

        // ===== Resumen =====
        echo "<h3>Resumen General</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Concepto</th><th>Valor</th></tr>";

        // Total Ventas = CANTIDAD (no dinero)
        echo "<tr>";
        echo "<td>Total Ventas (Cantidad)</td>";
        echo "<td style=\"mso-number-format:'0'; text-align:right;\">" . (int)$cantidadVentas . "</td>";
        echo "</tr>";

        // Subtotal = monto total según filtro
        echo "<tr>";
        echo "<td>Subtotal</td>";
        echo "<td style=\"mso-number-format:'0.00'; text-align:right;\">" . number_format($subtotalParaMostrar, 2, '.', '') . "</td>";
        echo "</tr>";

        echo "</table><br><br>";

        // ===== Detalle de Ventas =====
        echo "<h3>Detalle de Ventas (" . $cantidadVentas . " transacciones)</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Fecha</th><th>Cliente</th><th>NIT</th><th>Vendedor</th><th>Subtotal</th><th>Total</th></tr>";

        foreach ($ventas as $venta) {
            $id = $venta['id'] ?? '';
            $fecha = $venta['fecha'] ?? ($venta['fecha_venta'] ?? '');
            $cliente = $venta['cliente_nombre'] ?? 'Cliente General';
            $nit = $venta['cliente_nit'] ?? 'C/F';
            $vendedor = $venta['vendedor'] ?? 'N/A';
            $sub = (float)($venta['subtotal'] ?? 0);
            $tot = (float)($venta['total'] ?? 0);

            echo "<tr>";
            echo "<td>" . htmlspecialchars((string)$id) . "</td>";
            echo "<td>" . htmlspecialchars((string)$fecha) . "</td>";
            echo "<td>" . htmlspecialchars((string)$cliente) . "</td>";
            echo "<td>" . htmlspecialchars((string)$nit) . "</td>";
            echo "<td>" . htmlspecialchars((string)$vendedor) . "</td>";

            // NUMÉRICO (sin Q)
            echo "<td style=\"mso-number-format:'0.00'; text-align:right;\">" . number_format($sub, 2, '.', '') . "</td>";
            echo "<td style=\"mso-number-format:'0.00'; text-align:right;\">" . number_format($tot, 2, '.', '') . "</td>";
            echo "</tr>";
        }

        // Fila final TOTAL (monto total)
        echo "<tr style='font-weight:bold; background:#f1f5f9;'>";
        echo "<td colspan='6' style='text-align:right;'>TOTAL:</td>";
        echo "<td style=\"mso-number-format:'0.00'; text-align:right;\">" . number_format($totalFiltro, 2, '.', '') . "</td>";
        echo "</tr>";

        echo "</table>";
        echo "</body></html>";
        exit;
    }

    public function compras()
    {
        RoleMiddleware::requireAdmin();

        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $compras = $this->model->obtenerReporteCompras($fechaInicio, $fechaFin);
        $resumen = $this->model->obtenerResumenComprasPeriodo($fechaInicio, $fechaFin);

        $this->viewWithLayout("reportes/views/compras", [
            "title" => "Reporte de Compras",
            "user" => $_SESSION["user"],
            "compras" => $compras,
            "resumen" => $resumen,
            "fechaInicio" => $fechaInicio,
            "fechaFin" => $fechaFin,
        ]);
    }

    public function inventario()
    {
        RoleMiddleware::requireAdmin();

        $filtro = $_GET['filtro'] ?? 'todos';
        $productos = $this->model->obtenerReporteInventario($filtro);
        $resumen = $this->model->obtenerResumenInventario();

        $this->viewWithLayout("reportes/views/inventario", [
            "title" => "Reporte de Inventario",
            "user" => $_SESSION["user"],
            "productos" => $productos,
            "resumen" => $resumen,
            "filtro" => $filtro,
        ]);
    }

    public function productos()
    {
        RoleMiddleware::requireAdmin();

        $limite = $_GET['limite'] ?? 20;
        $orden = $_GET['orden'] ?? 'mas_vendidos';

        if ($orden === 'mas_vendidos') {
            $productos = $this->model->obtenerProductosMasVendidos($limite);
        } else {
            $productos = $this->model->obtenerProductosMenosVendidos($limite);
        }

        $this->viewWithLayout("reportes/views/productos", [
            "title" => "Reporte de Productos",
            "user" => $_SESSION["user"],
            "productos" => $productos,
            "orden" => $orden,
            "limite" => $limite,
        ]);
    }

    public function balance()
    {
        RoleMiddleware::requireAdmin();

        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

        // Manejo de exportación a Excel
        if (isset($_GET['exportar']) && $_GET['exportar'] === 'excel') {
            $this->exportarBalanceExcel($fechaInicio, $fechaFin);
            return;
        }

        $balance = $this->model->obtenerBalanceFinanciero($fechaInicio, $fechaFin);
        $balancePorDia = $this->model->obtenerBalancePorDia($fechaInicio, $fechaFin);
        $detalleVentas = $this->model->obtenerDetalleVentas($fechaInicio, $fechaFin);
        $detalleCompras = $this->model->obtenerDetalleCompras($fechaInicio, $fechaFin);

        $this->viewWithLayout("reportes/views/balance", [
            "title" => "Balance Financiero",
            "user" => $_SESSION["user"],
            "balance" => $balance,
            "balancePorDia" => $balancePorDia,
            "detalleVentas" => $detalleVentas,
            "detalleCompras" => $detalleCompras,
            "fechaInicio" => $fechaInicio,
            "fechaFin" => $fechaFin,
        ]);
    }

    private function exportarBalanceExcel($fechaInicio, $fechaFin)
    {
        $balance = $this->model->obtenerBalanceFinanciero($fechaInicio, $fechaFin);
        $detalleVentas = $this->model->obtenerDetalleVentas($fechaInicio, $fechaFin);
        $detalleCompras = $this->model->obtenerDetalleCompras($fechaInicio, $fechaFin);

        // Headers para descarga de Excel
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Balance_' . $fechaInicio . '_' . $fechaFin . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF"; // BOM para UTF-8

        echo "<html><head><meta charset='UTF-8'></head><body>";
        echo "<h2>Balance Financiero del $fechaInicio al $fechaFin</h2>";

        // Resumen
        echo "<h3>Resumen General</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Concepto</th><th>Monto</th></tr>";
        echo "<tr><td>Ingresos (Ventas)</td><td>Q " . number_format($balance['ingresos'], 2) . "</td></tr>";
        echo "<tr><td>Egresos (Compras)</td><td>Q " . number_format($balance['egresos'], 2) . "</td></tr>";
        echo "<tr><td><b>Balance Neto</b></td><td><b>Q " . number_format($balance['balance'], 2) . "</b></td></tr>";
        echo "</table><br><br>";

        // Detalle de Ventas
        echo "<h3>Detalle de Ventas (" . count($detalleVentas) . " transacciones)</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th>Productos</th><th>Total</th></tr>";
        foreach ($detalleVentas as $venta) {
            echo "<tr>";
            echo "<td>" . $venta['id'] . "</td>";
            echo "<td>" . $venta['fecha'] . "</td>";
            echo "<td>" . ($venta['cliente'] ?? 'N/A') . "</td>";
            echo "<td>" . ($venta['vendedor'] ?? 'N/A') . "</td>";
            echo "<td>" . $venta['cantidad_productos'] . "</td>";
            echo "<td>Q " . number_format($venta['total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table><br><br>";

        // Detalle de Compras
        echo "<h3>Detalle de Compras (" . count($detalleCompras) . " transacciones)</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Fecha</th><th>Proveedor</th><th>Productos</th><th>Total</th></tr>";
        foreach ($detalleCompras as $compra) {
            echo "<tr>";
            echo "<td>" . $compra['id'] . "</td>";
            echo "<td>" . $compra['fecha'] . "</td>";
            echo "<td>" . ($compra['proveedor'] ?? 'N/A') . "</td>";
            echo "<td>" . $compra['cantidad_productos'] . "</td>";
            echo "<td>Q " . number_format($compra['total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "</body></html>";
        exit;
    }
}
