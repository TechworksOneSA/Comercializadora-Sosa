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
     * Calcular margen de ganancia del día
     * Los retiros personales NO afectan la ganancia real
     */
    public function obtenerMargenGanancia(): array
    {
        // Ingresos del día
        $sqlIngresos = "SELECT COALESCE(SUM(total), 0) as ingresos
                        FROM venta
                        WHERE DATE(fecha_venta) = CURDATE()
                        AND estado = 'CONFIRMADA'";

        $stmt = $this->db->prepare($sqlIngresos);
        $stmt->execute();
        $ingresos = $stmt->fetch(PDO::FETCH_ASSOC);

        // Gastos del día (solo gastos operativos en EFECTIVO, NO retiros ni transferencias/cheques)
        $sqlGastos = "SELECT COALESCE(SUM(monto), 0) as gastos
                      FROM movimientos_caja
                      WHERE DATE(fecha) = CURDATE()
                      AND tipo = 'gasto'
                      AND metodo_pago = 'Efectivo'";

        $stmt = $this->db->prepare($sqlGastos);
        $stmt->execute();
        $gastos = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalIngresos = (float)($ingresos['ingresos'] ?? 0);
        $totalGastos = (float)($gastos['gastos'] ?? 0);

        // Ganancia Real = Ingresos - Gastos Operativos
        // Las compras NO se restan porque son inversión en inventario, no gastos
        $gananciaReal = $totalIngresos - $totalGastos;
        $porcentajeMargen = $totalIngresos > 0 ? ($gananciaReal / $totalIngresos) * 100 : 0;

        return [
            'ingresos' => $totalIngresos,
            'costos' => $totalGastos,
            'ganancia_real' => $gananciaReal,
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
     * Obtener ganancias del mes
     * Fórmula: Ventas Totales - Gastos - Compras (todo del mes actual)
     */
    public function obtenerGananciasMes(): array
    {
        // Ventas totales del mes (todos los métodos de pago)
        $sqlVentas = "SELECT COALESCE(SUM(total), 0) as ventas_mes
                      FROM venta
                      WHERE YEAR(fecha_venta) = YEAR(CURDATE())
                      AND MONTH(fecha_venta) = MONTH(CURDATE())
                      AND estado = 'CONFIRMADA'";

        $stmt = $this->db->prepare($sqlVentas);
        $stmt->execute();
        $ventas = $stmt->fetch(PDO::FETCH_ASSOC);

        // Gastos del mes (todos los métodos de pago)
        $sqlGastos = "SELECT COALESCE(SUM(monto), 0) as gastos_mes
                      FROM movimientos_caja
                      WHERE YEAR(fecha) = YEAR(CURDATE())
                      AND MONTH(fecha) = MONTH(CURDATE())
                      AND tipo = 'gasto'";

        $stmt = $this->db->prepare($sqlGastos);
        $stmt->execute();
        $gastos = $stmt->fetch(PDO::FETCH_ASSOC);

        $ventasMes = (float)($ventas['ventas_mes'] ?? 0);
        $gastosMes = (float)($gastos['gastos_mes'] ?? 0);

        // Ganancias del Mes = Ventas - Gastos
        $gananciasMes = $ventasMes - $gastosMes;

        return [
            'ventas_mes' => $ventasMes,
            'gastos_mes' => $gastosMes,
            'ganancias_mes' => $gananciasMes
        ];
    }
}
