<?php

require_once __DIR__ . '/../caja/CajaModel.php';

class PosModel extends Model
{
    private PDO $db;
    private CajaModel $cajaModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->cajaModel = new CajaModel();
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

            // Preparar campos adicionales
            $numeroCheque = $_POST['numero_cheque'] ?? null;
            $numeroBoleta = $_POST['numero_boleta'] ?? null;

            // Actualizar total_pagado y método de pago y números en venta
            $sql = "UPDATE venta
                    SET total_pagado = total_pagado + :monto,
                        metodo_pago = :metodo_pago,
                        numero_cheque = :numero_cheque,
                        numero_boleta = :numero_boleta
                    WHERE id = :venta_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':monto' => $monto,
                ':metodo_pago' => $metodoPago,
                ':numero_cheque' => $numeroCheque,
                ':numero_boleta' => $numeroBoleta,
                ':venta_id' => $ventaId
            ]);

            // Registrar movimiento de caja (ingreso) usando CajaModel
            $this->cajaModel->registrarIngreso([
                'concepto' => 'Cobro de venta',
                'monto' => $monto,
                'metodo_pago' => $metodoPago,
                'observaciones' => $observaciones,
                'venta_id' => $ventaId,
                'usuario_id' => $_SESSION['user']['id']
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ==================== DELEGACION A CAJAMODEL ====================

    /**
     * Obtener resumen de caja usando CajaModel
     */
    public function obtenerResumenCaja(): array
    {
        return $this->cajaModel->obtenerResumenCaja();
    }

    /**
     * Obtener últimos movimientos usando CajaModel
     */
    public function obtenerUltimosMovimientos(int $limite = 10): array
    {
        return $this->cajaModel->obtenerUltimosMovimientos($limite);
    }

    /**
     * Registrar movimiento de caja usando CajaModel
     */
    public function registrarMovimiento(array $data): bool
    {
        return $this->cajaModel->registrarMovimiento($data);
    }
}
