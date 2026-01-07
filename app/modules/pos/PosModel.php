<?php

class PosModel extends Model
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // ==================== VENTAS PENDIENTES ====================

    /**
     * Obtener ventas confirmadas con saldo pendiente
     */
    public function obtenerVentasPendientesCobro(): array
    {
        $sql = "SELECT
                    v.id,
                    v.fecha_venta,
                    v.total,
                    v.total_pagado,
                    (v.total - v.total_pagado) as saldo_pendiente,
                    v.metodo_pago,
                    CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.nit as cliente_nit
                FROM venta v
                INNER JOIN clientes c ON v.cliente_id = c.id
                WHERE v.estado = 'CONFIRMADA'
                AND v.total > v.total_pagado
                ORDER BY v.fecha_venta ASC, v.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener venta por ID con detalles del cliente
     */
    public function obtenerVentaPorId(int $id): ?array
    {
        $sql = "SELECT
                    v.*,
                    CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.direccion as cliente_direccion,
                    c.nit as cliente_nit,
                    (v.total - v.total_pagado) as saldo_pendiente
                FROM venta v
                INNER JOIN clientes c ON v.cliente_id = c.id
                WHERE v.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Registrar cobro de venta
     */
    public function registrarCobro(int $ventaId, float $monto, string $metodoPago, string $observaciones = ''): bool
    {
        try {
            $this->db->beginTransaction();

            // Actualizar total_pagado en venta
            $sql = "UPDATE venta
                    SET total_pagado = total_pagado + :monto
                    WHERE id = :venta_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':monto' => $monto,
                ':venta_id' => $ventaId
            ]);

            // Registrar movimiento de caja (ingreso)
            $sqlMovimiento = "INSERT INTO movimientos_caja
                            (tipo, concepto, monto, metodo_pago, observaciones, venta_id, usuario_id, fecha)
                            VALUES ('ingreso', 'Cobro de venta', :monto, :metodo_pago, :observaciones, :venta_id, :usuario_id, NOW())";

            $stmtMovimiento = $this->db->prepare($sqlMovimiento);
            $stmtMovimiento->execute([
                ':monto' => $monto,
                ':metodo_pago' => $metodoPago,
                ':observaciones' => $observaciones,
                ':venta_id' => $ventaId,
                ':usuario_id' => $_SESSION['user']['id']
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ==================== GASTOS Y MOVIMIENTOS ====================

    /**
     * Registrar movimiento de caja (gasto o retiro)
     */
    public function registrarMovimiento(array $data): bool
    {
        $sql = "INSERT INTO movimientos_caja
                (tipo, concepto, monto, metodo_pago, observaciones, usuario_id, fecha)
                VALUES (:tipo, :concepto, :monto, :metodo_pago, :observaciones, :usuario_id, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tipo' => $data['tipo'],
            ':concepto' => $data['concepto'],
            ':monto' => $data['monto'],
            ':metodo_pago' => $data['metodo_pago'],
            ':observaciones' => $data['observaciones'] ?? '',
            ':usuario_id' => $data['usuario_id']
        ]);
    }

    /**
     * Obtener todos los gastos y movimientos
     */
    public function obtenerGastos(): array
    {
        $sql = "SELECT
                    mc.*,
                    u.nombre as usuario_nombre,
                    v.id as venta_numero
                FROM movimientos_caja mc
                LEFT JOIN usuarios u ON mc.usuario_id = u.id
                LEFT JOIN venta v ON mc.venta_id = v.id
                WHERE mc.tipo IN ('gasto', 'retiro')
                ORDER BY mc.fecha DESC, mc.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener últimos movimientos de caja
     */
    public function obtenerUltimosMovimientos(int $limite = 10): array
    {
        $sql = "SELECT
                    mc.*,
                    u.nombre as usuario_nombre,
                    v.id as venta_numero
                FROM movimientos_caja mc
                LEFT JOIN usuarios u ON mc.usuario_id = u.id
                LEFT JOIN venta v ON mc.venta_id = v.id
                ORDER BY mc.fecha DESC, mc.id DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar movimiento de caja
     */
    public function eliminarMovimiento(int $id): bool
    {
        $sql = "DELETE FROM movimientos_caja WHERE id = :id AND tipo IN ('gasto', 'retiro')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ==================== RESUMEN DE CAJA ====================

    /**
     * Obtener resumen de caja (ingresos, gastos, saldo)
     * Diferencia entre efectivo físico en caja y ganancias totales
     */
    public function obtenerResumenCaja(): array
    {
        // Movimientos de caja registrados hoy
        $sqlMovimientos = "SELECT
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' AND metodo_pago = 'Efectivo' THEN monto ELSE 0 END), 0) as ingresos_efectivo,
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' AND metodo_pago != 'Efectivo' THEN monto ELSE 0 END), 0) as ingresos_otros,
                    COALESCE(SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END), 0) as total_gastos,
                    COALESCE(SUM(CASE WHEN tipo = 'retiro' THEN monto ELSE 0 END), 0) as total_retiros
                FROM movimientos_caja
                WHERE DATE(fecha) = CURDATE()";

        $stmt = $this->db->prepare($sqlMovimientos);
        $stmt->execute();
        $movimientos = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcular totales
        $ingresosEfectivo = (float)($movimientos['ingresos_efectivo'] ?? 0);
        $ingresosOtros = (float)($movimientos['ingresos_otros'] ?? 0);
        $totalGastos = (float)($movimientos['total_gastos'] ?? 0);
        $totalRetiros = (float)($movimientos['total_retiros'] ?? 0);

        $gananciasTotales = $ingresosEfectivo + $ingresosOtros;
        $efectivoEnCaja = $ingresosEfectivo - $totalGastos - $totalRetiros;

        return [
            'ingresos_efectivo' => $ingresosEfectivo,
            'ingresos_otros' => $ingresosOtros,
            'ganancias_totales' => $gananciasTotales,
            'total_gastos' => $totalGastos,
            'total_retiros' => $totalRetiros,
            'efectivo_en_caja' => $efectivoEnCaja
        ];
    }
}
