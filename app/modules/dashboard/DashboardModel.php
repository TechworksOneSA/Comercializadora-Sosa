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
     * Ganancia real = SUM((precio_venta - costo) * cantidad) de productos vendidos hoy
     * Los retiros personales NO afectan la ganancia real
     */
    public function obtenerMargenGanancia(): array
    {
        // Ganancia real del día por producto vendido
        $sql = "SELECT 
                    COALESCE(SUM((dv.precio_unitario - p.costo) * dv.cantidad), 0) AS ganancia_real,
                    COALESCE(SUM(dv.precio_unitario * dv.cantidad), 0) AS ingresos,
                    COALESCE(SUM(p.costo * dv.cantidad), 0) AS costos
                FROM detalle_venta dv
                INNER JOIN venta v ON v.id = dv.venta_id
                INNER JOIN productos p ON p.id = dv.producto_id
                WHERE DATE(v.fecha_venta) = CURDATE()
                  AND v.estado = 'CONFIRMADA'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $ingresos = (float)($row['ingresos'] ?? 0);
        $costos = (float)($row['costos'] ?? 0);
        $gananciaReal = (float)($row['ganancia_real'] ?? 0);
        $porcentajeMargen = $ingresos > 0 ? ($gananciaReal / $ingresos) * 100 : 0;

        return [
            'ingresos' => $ingresos,
            'costos' => $costos,
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
     * Ganancia del mes = SUM((precio_venta - costo) * cantidad) de productos vendidos en el mes
     */
    public function obtenerGananciasMes(): array
    {
        // Ganancia real del mes por producto vendido
        $sql = "SELECT 
                    COALESCE(SUM((dv.precio_unitario - p.costo) * dv.cantidad), 0) AS ganancias_mes,
                    COALESCE(SUM(dv.precio_unitario * dv.cantidad), 0) AS ventas_mes,
                    COALESCE(SUM(p.costo * dv.cantidad), 0) AS costos_mes
                FROM detalle_venta dv
                INNER JOIN venta v ON v.id = dv.venta_id
                INNER JOIN productos p ON p.id = dv.producto_id
                WHERE YEAR(v.fecha_venta) = YEAR(CURDATE())
                  AND MONTH(v.fecha_venta) = MONTH(CURDATE())
                  AND v.estado = 'CONFIRMADA'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $ventasMes = (float)($row['ventas_mes'] ?? 0);
        $costosMes = (float)($row['costos_mes'] ?? 0);
        $gananciasMes = (float)($row['ganancias_mes'] ?? 0);

        return [
            'ventas_mes' => $ventasMes,
            'gastos_mes' => $costosMes, // aquí gastos_mes representa el costo de los productos vendidos
            'ganancias_mes' => $gananciasMes
        ];
    }
}
