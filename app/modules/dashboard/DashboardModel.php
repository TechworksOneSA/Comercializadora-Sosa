<?php

class DashboardModel extends Model
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtener ventas del día
     * (Métrica rápida basada en venta.total)
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
                  AND estado = 'CONFIRMADA'
                  AND anulada_at IS NULL";

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
     * Obtener efectivo real en caja (acumulativo histórico)
     */
    public function obtenerEfectivoEnCaja(): float
    {
        $sql = "SELECT
                    COALESCE(SUM(CASE
                        WHEN tipo = 'ingreso' AND metodo_pago = 'Efectivo' THEN monto
                        WHEN tipo = 'gasto' AND metodo_pago = 'Efectivo' THEN -monto
                        WHEN tipo = 'retiro' AND metodo_pago = 'Efectivo' THEN -monto
                        ELSE 0
                    END), 0) as efectivo_caja
                FROM movimientos_caja";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (float)($result['efectivo_caja'] ?? 0);
    }

    /**
     * Obtener gastos del día (solo en efectivo)
     */
    public function obtenerGastosHoy(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_gastos,
                    COALESCE(SUM(monto), 0) as total_gastos
                FROM movimientos_caja
                WHERE DATE(fecha) = CURDATE()
                  AND tipo = 'gasto'
                  AND metodo_pago = 'Efectivo'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_gastos' => 0,
            'total_gastos' => 0
        ];
    }

    /**
     * Obtener retiros del día
     */
    public function obtenerRetirosHoy(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_retiros,
                    COALESCE(SUM(monto), 0) as total_retiros
                FROM movimientos_caja
                WHERE DATE(fecha) = CURDATE()
                  AND tipo = 'retiro'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_retiros' => 0,
            'total_retiros' => 0
        ];
    }

    /**
     * Ganancia Real del Día (PRO)
     *
     * Fórmula (la que usted definió):
     * ✅ Ganancia = Ventas - Costo - Gastos + Reversas
     *
     * Donde:
     * - Ventas = SUM(vd.subtotal) de ventas NO anuladas (por fecha_venta hoy)
     * - Costo  = SUM(vd.cantidad * costo) de ventas NO anuladas
     * - Gastos = SUM(movimientos tipo 'gasto' del día) (NO incluye retiros)
     * - Reversas = ajuste por anulaciones en el día (por anulada_at hoy) con SIGNO:
     *     reversas = -(ventas_anuladas - costo_anulado)
     *   (normalmente será NEGATIVO para reducir utilidad)
     */
    public function obtenerMargenGanancia(): array
    {
        // 1) Ventas y COGS del día (ventas válidas: no anuladas)
        $sqlVentasCostos = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS ventas_dia,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS cogs_dia
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE DATE(v.fecha_venta) = CURDATE()
              AND v.estado = 'CONFIRMADA'
              AND v.anulada_at IS NULL";

        $stmt = $this->db->prepare($sqlVentasCostos);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['ventas_dia' => 0, 'cogs_dia' => 0];

        $ventasDia = (float)($row['ventas_dia'] ?? 0);
        $cogsDia   = (float)($row['cogs_dia'] ?? 0);

        // 2) Reversas del día (ventas anuladas HOY por anulada_at)
        $sqlReversas = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS ventas_anuladas_dia,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS cogs_anulado_dia
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE v.estado = 'CONFIRMADA'
              AND v.anulada_at IS NOT NULL
              AND DATE(v.anulada_at) = CURDATE()";

        $stmt = $this->db->prepare($sqlReversas);
        $stmt->execute();
        $rev = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['ventas_anuladas_dia' => 0, 'cogs_anulado_dia' => 0];

        $ventasAnuladasDia = (float)($rev['ventas_anuladas_dia'] ?? 0);
        $cogsAnuladoDia    = (float)($rev['cogs_anulado_dia'] ?? 0);

        // impacto del margen anulado = ventas anuladas - costo anulado
        $impactoReversa = ($ventasAnuladasDia - $cogsAnuladoDia);

        // reversas con signo (NEGATIVO) para sumarse en la fórmula
        $reversasDia = -$impactoReversa;

        // 3) Gastos operativos del día (NO retiros)
        // Si quiere SOLO efectivo, agregue: AND metodo_pago = 'Efectivo'
        $sqlGastos = "SELECT COALESCE(SUM(monto), 0) AS gastos_dia
                      FROM movimientos_caja
                      WHERE DATE(fecha) = CURDATE()
                        AND tipo = 'gasto'";

        $stmt = $this->db->prepare($sqlGastos);
        $stmt->execute();
        $g = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['gastos_dia' => 0];

        $gastosDia = (float)($g['gastos_dia'] ?? 0);

        // 4) Fórmula ejecutiva:
        // Ganancia = Ventas - Costo - Gastos + Reversas
        $ganancia = $ventasDia - $cogsDia - $gastosDia + $reversasDia;

        // Si usted quiere mostrar también ganancia bruta (sin gastos):
        $gananciaBruta = $ventasDia - $cogsDia + $reversasDia;

        $porcentajeMargen = $ventasDia > 0 ? ($ganancia / $ventasDia) * 100 : 0;

        return [
            'ventas_dia' => $ventasDia,
            'cogs_dia' => $cogsDia,
            'gastos_dia' => $gastosDia,

            // reversas ya viene con signo
            'reversas_dia' => $reversasDia,

            'ganancia_bruta' => $gananciaBruta,
            'ganancia_real' => $ganancia,
            'porcentaje_margen' => $porcentajeMargen
        ];
    }

    /**
     * Obtener productos con stock bajo
     */
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

    /**
     * Obtener productos sin stock
     */
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

    /**
     * Obtener ventas pendientes de cobro
     */
    public function obtenerVentasPendientesCobro(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_pendientes,
                    COALESCE(SUM(total - total_pagado), 0) as total_por_cobrar
                FROM venta
                WHERE estado = 'CONFIRMADA'
                  AND anulada_at IS NULL
                  AND total > total_pagado";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_pendientes' => 0,
            'total_por_cobrar' => 0
        ];
    }

    /**
     * Obtener resumen de alertas/riesgos
     */
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
     * Ganancias del Mes (PRO)
     *
     * Fórmula:
     * ✅ Ganancia = Ventas - Costo - Gastos + Reversas
     *
     * - Ventas/Costo: ventas NO anuladas por fecha_venta dentro del mes
     * - Reversas: anulaciones por anulada_at dentro del mes (con signo NEGATIVO)
     */
    public function obtenerGananciasMes(): array
    {
        // 1) Ventas y COGS del mes (ventas válidas)
        $sqlVentasCostos = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS ventas_mes,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS cogs_mes
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE v.estado = 'CONFIRMADA'
              AND v.anulada_at IS NULL
              AND YEAR(v.fecha_venta) = YEAR(CURDATE())
              AND MONTH(v.fecha_venta) = MONTH(CURDATE())";

        $stmt = $this->db->prepare($sqlVentasCostos);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['ventas_mes' => 0, 'cogs_mes' => 0];

        $ventasMes = (float)($row['ventas_mes'] ?? 0);
        $cogsMes   = (float)($row['cogs_mes'] ?? 0);

        // 2) Reversas del mes (ventas anuladas en el mes actual, por anulada_at)
        $sqlReversasMes = "SELECT
                COALESCE(SUM(vd.subtotal), 0) AS ventas_anuladas_mes,
                COALESCE(SUM(vd.cantidad * COALESCE(p.costo_actual, p.costo, 0)), 0) AS cogs_anulado_mes
            FROM venta v
            JOIN venta_detalle vd ON vd.venta_id = v.id
            JOIN productos p ON p.id = vd.producto_id
            WHERE v.estado = 'CONFIRMADA'
              AND v.anulada_at IS NOT NULL
              AND YEAR(v.anulada_at) = YEAR(CURDATE())
              AND MONTH(v.anulada_at) = MONTH(CURDATE())";

        $stmt = $this->db->prepare($sqlReversasMes);
        $stmt->execute();
        $rev = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['ventas_anuladas_mes' => 0, 'cogs_anulado_mes' => 0];

        $ventasAnuladasMes = (float)($rev['ventas_anuladas_mes'] ?? 0);
        $cogsAnuladoMes    = (float)($rev['cogs_anulado_mes'] ?? 0);

        $impactoReversaMes = ($ventasAnuladasMes - $cogsAnuladoMes);
        $reversasMes = -$impactoReversaMes;

        // 3) Gastos operativos del mes
        $sqlGastos = "SELECT COALESCE(SUM(monto), 0) AS gastos_mes
                      FROM movimientos_caja
                      WHERE YEAR(fecha) = YEAR(CURDATE())
                        AND MONTH(fecha) = MONTH(CURDATE())
                        AND tipo = 'gasto'";

        $stmt = $this->db->prepare($sqlGastos);
        $stmt->execute();
        $gastos = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['gastos_mes' => 0];

        $gastosMes = (float)($gastos['gastos_mes'] ?? 0);

        // 4) Fórmula ejecutiva
        $gananciaMes = $ventasMes - $cogsMes - $gastosMes + $reversasMes;

        // Bruta sin gastos
        $gananciaBrutaMes = $ventasMes - $cogsMes + $reversasMes;

        return [
            'ventas_mes' => $ventasMes,
            'costo_ventas_mes' => $cogsMes,
            'gastos_mes' => $gastosMes,

            // reversa con signo
            'reversas_mes' => $reversasMes,

            'ganancia_bruta_mes' => $gananciaBrutaMes,

            // key que usa su vista
            'ganancias_mes' => $gananciaMes
        ];
    }
}
