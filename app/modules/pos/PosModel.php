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

    // ==================== COBROS ====================

    /**
     * Registrar cobro de venta
     * Reglas:
     * - No se cobra si la venta está ANULADA.
     * - No se cobra si la venta está CERRADA (total_pagado >= total).
     * - No permitir cobrar más del saldo pendiente (cap).
     * - Siempre se registra movimiento de caja "ingreso" (si es válido).
     */
    public function registrarCobro(int $ventaId, float $monto, string $metodoPago, string $observaciones = ''): bool
    {
        try {
            if ($ventaId <= 0) throw new Exception("ventaId inválido");
            if ($monto <= 0) throw new Exception("El monto debe ser mayor a 0");

            $usuarioId = (int)($_SESSION['user']['id'] ?? 0);
            if ($usuarioId <= 0) throw new Exception("Usuario no autenticado");

            $this->db->beginTransaction();

            // 1) Lock de venta para evitar carreras
            $sqlLock = "SELECT id, estado, total, total_pagado
                        FROM venta
                        WHERE id = :id
                        LIMIT 1
                        FOR UPDATE";
            $stmtLock = $this->db->prepare($sqlLock);
            $stmtLock->execute([':id' => $ventaId]);
            $venta = $stmtLock->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                throw new Exception("Venta no encontrada");
            }

            if (($venta['estado'] ?? '') === 'ANULADA') {
                throw new Exception("No se puede cobrar: la venta #{$ventaId} está ANULADA");
            }

            $total = (float)($venta['total'] ?? 0);
            $pagado = (float)($venta['total_pagado'] ?? 0);
            $saldo = $total - $pagado;

            if ($saldo <= 0) {
                throw new Exception("La venta #{$ventaId} ya está totalmente pagada");
            }

            // 2) Cap de monto al saldo pendiente (evita sobrepago)
            if ($monto > $saldo) {
                $monto = $saldo;
            }

            // Preparar campos adicionales
            $numeroCheque = $_POST['numero_cheque'] ?? null;
            $numeroBoleta = $_POST['numero_boleta'] ?? null;

            // 3) Registrar ingreso de caja PRIMERO (si esto falla, no tocamos venta)
            // CajaModel ya excluye ventas anuladas y maneja duplicados básicos
            $okCaja = $this->cajaModel->registrarIngreso([
                'concepto'      => "Cobro de venta #{$ventaId}",
                'monto'         => $monto,
                'metodo_pago'   => $metodoPago,
                'observaciones' => $observaciones,
                'venta_id'      => $ventaId,
                'usuario_id'    => $usuarioId,
            ]);

            if (!$okCaja) {
                // CajaModel retorna false si la venta es inválida para caja (p.ej. anulada)
                throw new Exception("No se pudo registrar el ingreso en caja para la venta #{$ventaId}");
            }

            // 4) Actualizar total_pagado y método de pago en venta
            $sqlUpd = "UPDATE venta
                       SET total_pagado = total_pagado + :monto,
                           metodo_pago = :metodo_pago,
                           numero_cheque = :numero_cheque,
                           numero_boleta = :numero_boleta
                       WHERE id = :venta_id";
            $stmtUpd = $this->db->prepare($sqlUpd);
            $stmtUpd->execute([
                ':monto'         => $monto,
                ':metodo_pago'   => $metodoPago,
                ':numero_cheque' => $numeroCheque,
                ':numero_boleta' => $numeroBoleta,
                ':venta_id'      => $ventaId,
            ]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    // ==================== DELEGACION A CAJAMODEL ====================

    public function obtenerResumenCaja(): array
    {
        return $this->cajaModel->obtenerResumenCaja();
    }

    public function obtenerUltimosMovimientos(int $limite = 10): array
    {
        return $this->cajaModel->obtenerUltimosMovimientos($limite);
    }

    public function registrarMovimiento(array $data): bool
    {
        return $this->cajaModel->registrarMovimiento($data);
    }
}
