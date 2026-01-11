<?php

class DashboardVendedorModel extends Model
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtener ventas totales del día
     */
    public function obtenerVentasHoy(): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad_ventas,
                    COALESCE(SUM(total), 0) as total_ventas
                FROM venta
                WHERE DATE(fecha_venta) = CURDATE()
                AND estado = 'CONFIRMADA'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad_ventas' => 0,
            'total_ventas' => 0
        ];
    }

    /**
     * Obtener mis ventas del día
     */
    public function obtenerMisVentas(int $usuarioId): array
    {
        $sql = "SELECT
                    COUNT(*) as cantidad,
                    COALESCE(SUM(total), 0) as total
                FROM venta
                WHERE DATE(fecha_venta) = CURDATE()
                AND estado = 'CONFIRMADA'
                AND usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'cantidad' => 0,
            'total' => 0
        ];
    }

    /**
     * Obtener efectivo en caja (acumulativo histórico)
     */
    public function obtenerEfectivoEnCaja(): float
    {
        $sql = "SELECT
                    COALESCE(SUM(CASE
                        WHEN tipo = 'ingreso' AND metodo_pago = 'Efectivo' THEN monto
                        WHEN tipo IN ('gasto', 'retiro') THEN -monto
                        ELSE 0
                    END), 0) as efectivo_caja
                FROM movimientos_caja";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['efectivo_caja'] ?? 0);
    }

    /**
     * Obtener alertas para el vendedor
     */
    public function obtenerAlertas(): array
    {
        // Productos con stock bajo
        $sqlBajoStock = "SELECT COUNT(*) as total
                         FROM productos
                         WHERE estado = 'ACTIVO'
                         AND stock <= stock_minimo
                         AND stock > 0";

        $stmt = $this->db->prepare($sqlBajoStock);
        $stmt->execute();
        $bajoStock = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Ventas pendientes de cobro
        $sqlPendientes = "SELECT COUNT(*) as cantidad
                          FROM venta
                          WHERE estado = 'CONFIRMADA'
                          AND total > total_pagado";

        $stmt = $this->db->prepare($sqlPendientes);
        $stmt->execute();
        $pendientesCobro = $stmt->fetch(PDO::FETCH_ASSOC)['cantidad'] ?? 0;

        return [
            'productos_bajo_stock' => $bajoStock,
            'ventas_por_cobrar_cantidad' => $pendientesCobro
        ];
    }
}
