<?php

require_once __DIR__ . "/InventarioAvanzadoModel.php";

class InventarioAvanzadoController extends Controller
{
    private InventarioAvanzadoModel $model;

    public function __construct()
    {
        $this->model = new InventarioAvanzadoModel();
    }

    // Vista principal (filtros + tabla + modal kardex)
    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        // Filtros (opcionales)
        $q = trim($_GET['q'] ?? '');
        $categoriaId = (int)($_GET['categoria_id'] ?? 0);
        $marcaId = (int)($_GET['marca_id'] ?? 0);
        $stockEstado = $_GET['stock'] ?? 'ALL'; // ALL | BAJO | AGOTADO

        $filtros = [
            'q' => $q,
            'categoria_id' => $categoriaId,
            'marca_id' => $marcaId,
            'stock' => $stockEstado,
        ];

        // Data
        $productos = $this->model->listarProductosConStock($filtros);
        $kpis      = $this->model->kpisInventario($filtros);

        $this->viewWithLayout("inventario_avanzado/views/index", [
            "title"    => "Inventario Avanzado",
            "user"     => $_SESSION["user"],
            "productos"=> $productos,
            "kpis"     => $kpis,
            "filtros"  => $filtros,
        ]);
    }

    // Endpoint JSON para el Kardex (movimientos_inventario)
    public function kardex()
    {
        RoleMiddleware::requireAdminOrVendedor();

        header('Content-Type: application/json; charset=utf-8');

        $productoId = (int)($_GET['producto_id'] ?? 0);
        if ($productoId <= 0) {
            http_response_code(400);
            echo json_encode(["ok" => false, "message" => "producto_id inválido"]);
            return;
        }

        $desde = $_GET['desde'] ?? null; // YYYY-MM-DD (opcional)
        $hasta = $_GET['hasta'] ?? null; // YYYY-MM-DD (opcional)
        $limit = (int)($_GET['limit'] ?? 200);

        $movs = $this->model->kardex($productoId, $desde, $hasta, $limit);

        // Obtener stock actual del producto
        $stockActual = $this->model->obtenerStockActual($productoId);

        // Calcular totales del rango
        $totales = $this->model->calcularTotalesKardex($productoId, $desde, $hasta);

        // Calcular saldo acumulado para cada movimiento
        $saldo = 0;
        foreach ($movs as &$mov) {
            $cantidad = (float)($mov['cantidad'] ?? 0);

            if ($mov['tipo'] === 'ENTRADA') {
                $saldo += $cantidad;
            } elseif ($mov['tipo'] === 'SALIDA') {
                $saldo -= $cantidad;
            } else { // AJUSTE
                // Ajuste puede ser positivo o negativo
                $saldo += $cantidad;
            }

            $mov['saldo'] = $saldo;
            $mov['valor'] = $cantidad * (float)($mov['costo_unitario'] ?? 0);
            $mov['fecha'] = date('Y-m-d H:i:s', strtotime($mov['created_at']));
        }

        // Revertir orden para mostrar del más antiguo al más nuevo
        $movs = array_reverse($movs);

        // Recalcular saldo acumulado en orden correcto
        $saldo = 0;
        foreach ($movs as &$mov) {
            $cantidad = (float)($mov['cantidad'] ?? 0);

            if ($mov['tipo'] === 'ENTRADA') {
                $saldo += $cantidad;
            } elseif ($mov['tipo'] === 'SALIDA') {
                $saldo -= $cantidad;
            } else {
                $saldo += $cantidad;
            }

            $mov['saldo'] = $saldo;
        }

        echo json_encode([
            "ok" => true,
            "producto_id" => $productoId,
            "stock_actual" => $stockActual,
            "totales" => $totales,
            "movimientos" => $movs
        ]);
    }
}
