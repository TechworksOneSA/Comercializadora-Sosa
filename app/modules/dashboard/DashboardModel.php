<?php

require_once __DIR__ . '/../caja/CajaModel.php';

class DashboardModel extends Model
{
    private PDO $db;
    private CajaModel $cajaModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->cajaModel = new CajaModel();
    }

    /**
     * Obtener ventas del día (métrica rápida basada en venta.total)
     * Fuente: venta.fecha_venta y estado
     */
    public function obtenerVentasHoy(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_ventas,
                    COALESCE(SUM(total), 0) as total_ventas,
                    COALESCE(SUM(CASE WHEN metodo_pago = 'Efectivo' THEN total ELSE 0 END), 0) as ventas_efectivo,
                    COALESCE(SUM(CASE WHEN metodo_pago != 'Efectivo' THEN total ELSE 0 END), 0) as ventas_otros
                FROM venta
                WHERE DATE(fecha_venta) = CURDATE()
                  AND estado = 'CONFIRMADA'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_ventas' => 0,
            'total_ventas' => 0,
            'ventas_efectivo' => 0,
            'ventas_otros' => 0
        ];
    }

    /**
     * ✅ Efectivo real en caja (SINGLE SOURCE OF TRUTH)
     * Usa el mismo cálculo que CajaModel y excluye ventas anuladas.
     */
    public function obtenerEfectivoEnCaja(): float
    {
        $resumen = $this->cajaModel->obtenerResumenCaja();
        return (float)($resumen['efectivo_en_caja'] ?? 0);
    }

    /**
     * Obtener gastos del día (solo efectivo)
     * ✅ Ignora gastos vinculados a ventas anuladas (si venta_id existe)
     */
    public function obtenerGastosHoy(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_gastos,
                    COALESCE(SUM(mc.monto), 0) as total_gastos
                FROM movimientos_caja mc
                LEFT JOIN venta v ON v.id = mc.venta_id
                WHERE DATE(mc.fecha) = CURDATE()
                  AND mc.tipo = 'gasto'
                  AND mc.metodo_pago = 'Efectivo'
                  AND (mc.venta_id IS NULL OR v.estado = 'CONFIRMADA')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_gastos' => 0,
            'total_gastos' => 0
        ];
    }

    /**
     * Obtener retiros del día
     * ✅ Ignora retiros vinculados a ventas anuladas (si venta_id existe)
     */
    public function obtenerRetirosHoy(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_retiros,
                    COALESCE(SUM(mc.monto), 0) as total_retiros
                FROM movimientos_caja mc
                LEFT JOIN venta v ON v.id = mc.venta_id
                WHERE DATE(mc.fecha) = CURDATE()
                  AND mc.tipo = 'retiro'
                  AND (mc.venta_id IS NULL OR v.estado = 'CONFIRMADA')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_retiros' => 0,
            'total_retiros' => 0
        ];
    }

    /**
     * Ganancia real del día (ventas - costo - gastos)
     * Reversas se devuelven SOLO como dato informativo (ventas anuladas del día).
     */
    public function obtenerMargenGanancia(): array
    {
        // 1) Ventas + COGS del día (CONFIRMADAS)
        $sqlVentasCostos = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS ventas_dia,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS cogs_dia
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE DATE(v.fecha_venta) = CURDATE()
              AND v.estado = 'CONFIRMADA'";

        $stmt = $this->db->prepare($sqlVentasCostos);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['ventas_dia' => 0, 'cogs_dia' => 0];

        $ventasDia = (float)($row['ventas_dia'] ?? 0);
        $cogsDia   = (float)($row['cogs_dia'] ?? 0);

        // 2) Gastos del día (NO retiros)
        // ✅ excluir movimientos ligados a venta anulada
        $sqlGastos = "SELECT COALESCE(SUM(mc.monto), 0) AS gastos_dia
                      FROM movimientos_caja mc
                      LEFT JOIN venta v ON v.id = mc.venta_id
                      WHERE DATE(mc.fecha) = CURDATE()
                        AND mc.tipo = 'gasto'
                        AND (mc.venta_id IS NULL OR v.estado = 'CONFIRMADA')";

        $stmt = $this->db->prepare($sqlGastos);
        $stmt->execute();
        $g = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['gastos_dia' => 0];

        $gastosDia = (float)($g['gastos_dia'] ?? 0);

        // 3) Reversas del día (ANULADAS hoy) -> info
        $sqlReversas = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS rev_ventas_dia,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS rev_cogs_dia
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE v.estado = 'ANULADA'
              AND DATE(COALESCE(v.anulada_at, v.updated_at)) = CURDATE()";

        $stmt = $this->db->prepare($sqlReversas);
        $stmt->execute();
        $rev = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['rev_ventas_dia' => 0, 'rev_cogs_dia' => 0];

        $revVentas = (float)($rev['rev_ventas_dia'] ?? 0);
        $revCogs   = (float)($rev['rev_cogs_dia'] ?? 0);

        // 4) Fórmula final (SIN impacto reversas)
        $gananciaReal = ($ventasDia - $cogsDia - $gastosDia);
        $porcentajeMargen = $ventasDia > 0 ? ($gananciaReal / $ventasDia) * 100 : 0;

        return [
            'ventas_dia' => $ventasDia,
            'cogs_dia' => $cogsDia,
            'gastos_dia' => $gastosDia,

            'reversas_ventas_dia' => $revVentas,
            'reversas_cogs_dia' => $revCogs,
            'reversas_dia' => $revVentas,

            'ganancia_bruta' => ($ventasDia - $cogsDia),
            'ganancia_real' => $gananciaReal,
            'porcentaje_margen' => $porcentajeMargen
        ];
    }

    public function obtenerProductosBajoStock(): array
    {
        $sql = "SELECT
                    COUNT(*) as total_bajo_stock,
                    COALESCE(SUM(stock), 0) as unidades_criticas
                FROM productos
                WHERE stock <= stock_minimo
                  AND stock > 0
                  AND activo = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_bajo_stock' => 0,
            'unidades_criticas' => 0
        ];
    }

    public function obtenerProductosSinStock(): int
    {
        $sql = "SELECT COUNT(*) as sin_stock
                FROM productos
                WHERE stock = 0
                  AND activo = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($result['sin_stock'] ?? 0);
    }

    public function obtenerVentasPendientesCobro(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_pendientes,
                    COALESCE(SUM(total - total_pagado), 0) as total_por_cobrar
                FROM venta
                WHERE estado = 'CONFIRMADA'
                  AND total > total_pagado";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_pendientes' => 0,
            'total_por_cobrar' => 0
        ];
    }

    public function obtenerAlertasRiesgos(): array
    {
        $bajoStock = $this->obtenerProductosBajoStock();
        $sinStock = $this->obtenerProductosSinStock();
        $pendientesCobro = $this->obtenerVentasPendientesCobro();

        return [
            'productos_bajo_stock' => $bajoStock['total_bajo_stock'],
            'productos_sin_stock' => $sinStock,
            'ventas_por_cobrar_cantidad' => $pendientesCobro['cantidad_pendientes'],
            'ventas_por_cobrar_monto' => $pendientesCobro['total_por_cobrar']
        ];
    }

    /**
     * Ganancia neta del mes (ventas - costo - gastos)
     * Reversas se devuelven SOLO como dato informativo.
     */
    public function obtenerGananciasMes(): array
    {
        // Ventas + COGS del mes (CONFIRMADAS)
        $sqlVentasCostos = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS ventas_mes,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS cogs_mes
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE v.estado = 'CONFIRMADA'
              AND YEAR(v.fecha_venta) = YEAR(CURDATE())
              AND MONTH(v.fecha_venta) = MONTH(CURDATE())";

        $stmt = $this->db->prepare($sqlVentasCostos);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['ventas_mes' => 0, 'cogs_mes' => 0];

        $ventasMes = (float)($row['ventas_mes'] ?? 0);
        $cogsMes   = (float)($row['cogs_mes'] ?? 0);

        // Gastos del mes
        // ✅ excluir movimientos ligados a venta anulada
        $sqlGastos = "SELECT COALESCE(SUM(mc.monto), 0) AS gastos_mes
                      FROM movimientos_caja mc
                      LEFT JOIN venta v ON v.id = mc.venta_id
                      WHERE YEAR(mc.fecha) = YEAR(CURDATE())
                        AND MONTH(mc.fecha) = MONTH(CURDATE())
                        AND mc.tipo = 'gasto'
                        AND (mc.venta_id IS NULL OR v.estado = 'CONFIRMADA')";

        $stmt = $this->db->prepare($sqlGastos);
        $stmt->execute();
        $g = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['gastos_mes' => 0];

        $gastosMes = (float)($g['gastos_mes'] ?? 0);

        // Reversas del mes (info)
        $sqlReversasMes = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS rev_ventas_mes,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS rev_cogs_mes
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE v.estado = 'ANULADA'
              AND YEAR(COALESCE(v.anulada_at, v.updated_at)) = YEAR(CURDATE())
              AND MONTH(COALESCE(v.anulada_at, v.updated_at)) = MONTH(CURDATE())";

        $stmt = $this->db->prepare($sqlReversasMes);
        $stmt->execute();
        $rev = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['rev_ventas_mes' => 0, 'rev_cogs_mes' => 0];

        $revVentas = (float)($rev['rev_ventas_mes'] ?? 0);
        $revCogs   = (float)($rev['rev_cogs_mes'] ?? 0);

        $gananciaNetaMes = ($ventasMes - $cogsMes - $gastosMes);

        return [
            'ventas_mes' => $ventasMes,
            'costo_ventas_mes' => $cogsMes,
            'gastos_mes' => $gastosMes,

            'reversas_ventas_mes' => $revVentas,
            'reversas_cogs_mes' => $revCogs,
            'reversas_mes' => $revVentas,

            'ganancia_bruta_mes' => ($ventasMes - $cogsMes),
            'ganancias_mes' => $gananciaNetaMes
        ];
    }
}
