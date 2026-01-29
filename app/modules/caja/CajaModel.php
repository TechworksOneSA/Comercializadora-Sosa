<?php

class CajaModel extends Model
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /* =========================================================
     * Helpers
     * ========================================================= */

    private function ventaEsValidaParaCaja(?int $ventaId): bool
    {
        if (!$ventaId) return true; // movimientos manuales sin venta

        $sql = "SELECT estado FROM venta WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $ventaId]);
        $estado = $stmt->fetchColumn();

        // Si no existe, lo tratamos como no válido
        if ($estado === false) return false;

        // Excluir ventas anuladas
        return ((string)$estado !== 'ANULADA');
    }

    private function ingresoYaExisteParaPago(int $ventaId, float $monto, string $metodoPago): bool
    {
        // Evita duplicados si el frontend reintenta o se hace doble submit.
        // Regla simple: mismo tipo=ingreso, misma venta_id, mismo monto, mismo método.
        $sql = "SELECT id
                FROM movimientos_caja
                WHERE tipo = 'ingreso'
                  AND venta_id = :venta_id
                  AND monto = :monto
                  AND metodo_pago = :metodo
                ORDER BY id DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':venta_id' => $ventaId,
            ':monto'    => $monto,
            ':metodo'   => $metodoPago,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    /* =========================================================
     * MOVIMIENTOS DE CAJA
     * ========================================================= */

    /**
     * ✅ Registrar ingreso de caja (cobros de ventas / pagos)
     * - Se usa cuando "registrar pago" confirma un cobro.
     * - Si la venta está ANULADA, NO se registra nada.
     * - Incluye venta_id (trazabilidad).
     */
    public function registrarIngreso(array $data): bool
    {
        $ventaId    = isset($data['venta_id']) ? (int)$data['venta_id'] : null;
        $monto      = (float)($data['monto'] ?? 0);
        $metodoPago = (string)($data['metodo_pago'] ?? 'Efectivo');

        if ($monto <= 0) {
            throw new Exception("Monto inválido para ingreso de caja.");
        }

        // No contabilizar ventas anuladas
        if (!$this->ventaEsValidaParaCaja($ventaId)) {
            return false;
        }

        // Idempotencia básica para evitar duplicados
        if ($ventaId && $this->ingresoYaExisteParaPago($ventaId, $monto, $metodoPago)) {
            return true;
        }

        $sql = "INSERT INTO movimientos_caja
                (tipo, concepto, monto, metodo_pago, observaciones, venta_id, usuario_id, fecha)
                VALUES ('ingreso', :concepto, :monto, :metodo_pago, :observaciones, :venta_id, :usuario_id, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':concepto'      => (string)($data['concepto'] ?? 'Cobro de venta'),
            ':monto'         => $monto,
            ':metodo_pago'   => $metodoPago,
            ':observaciones' => (string)($data['observaciones'] ?? ''),
            ':venta_id'      => $ventaId,
            ':usuario_id'    => (int)($data['usuario_id'] ?? 0),
        ]);
    }

    /**
     * ✅ Atajo recomendado desde "registrar pago"
     * (llámelo desde PagosModel/Controller luego de guardar el pago)
     */
    public function registrarIngresoPorPago(int $ventaId, float $monto, string $metodoPago, int $usuarioId, string $observaciones = ''): bool
    {
        return $this->registrarIngreso([
            'concepto'      => "Cobro de venta #{$ventaId}",
            'monto'         => $monto,
            'metodo_pago'   => $metodoPago,
            'observaciones' => $observaciones,
            'venta_id'      => $ventaId,
            'usuario_id'    => $usuarioId,
        ]);
    }

    /**
     * Registrar movimiento de caja (gasto o retiro)
     * - Este método NO guarda venta_id (se mantiene como usted lo tenía).
     */
    public function registrarMovimiento(array $data): bool
    {
        $fecha = isset($data['fecha']) ? $data['fecha'] . ' ' . date('H:i:s') : null;

        $sql = "INSERT INTO movimientos_caja
                (tipo, concepto, monto, metodo_pago, observaciones, usuario_id, fecha)
                VALUES (:tipo, :concepto, :monto, :metodo_pago, :observaciones, :usuario_id, " .
                ($fecha ? ":fecha" : "NOW()") . ")";

        $params = [
            ':tipo'          => $data['tipo'],
            ':concepto'      => $data['concepto'],
            ':monto'         => $data['monto'],
            ':metodo_pago'   => $data['metodo_pago'],
            ':observaciones' => $data['observaciones'] ?? '',
            ':usuario_id'    => $data['usuario_id'],
        ];

        if ($fecha) {
            $params[':fecha'] = $fecha;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * ✅ Obtener todos los movimientos de caja
     * ✅ Excluye ingresos ligados a ventas ANULADAS
     */
    public function obtenerMovimientos(): array
    {
        $sql = "SELECT
                    mc.*,
                    u.nombre as usuario_nombre,
                    v.id as venta_numero
                FROM movimientos_caja mc
                LEFT JOIN usuarios u ON mc.usuario_id = u.id
                LEFT JOIN venta v ON mc.venta_id = v.id
                WHERE (mc.venta_id IS NULL OR v.estado <> 'ANULADA')
                ORDER BY mc.fecha DESC, mc.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ✅ Obtener últimos movimientos de caja
     * ✅ Excluye ingresos ligados a ventas ANULADAS
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
                WHERE (mc.venta_id IS NULL OR v.estado <> 'ANULADA')
                ORDER BY mc.fecha DESC, mc.id DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar movimiento de caja
     * (solo gastos/retiros, como usted lo definió)
     */
    public function eliminarMovimiento(int $id): bool
    {
        $sql = "DELETE FROM movimientos_caja WHERE id = :id AND tipo IN ('gasto', 'retiro')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /* =========================================================
     * RESUMEN DE CAJA
     * ========================================================= */

    /**
     * ✅ Obtener resumen de caja (hoy + acumulado)
     * ✅ No toma en cuenta movimientos ligados a ventas ANULADAS
     */
    public function obtenerResumenCaja(): array
    {
        // HOY (excluye ventas anuladas)
        $sqlMovimientosHoy = "SELECT
                COALESCE(SUM(CASE WHEN mc.tipo = 'ingreso' AND mc.metodo_pago = 'Efectivo' THEN mc.monto ELSE 0 END), 0) as ingresos_efectivo,
                COALESCE(SUM(CASE WHEN mc.tipo = 'ingreso' AND mc.metodo_pago != 'Efectivo' THEN mc.monto ELSE 0 END), 0) as ingresos_otros,
                COALESCE(SUM(CASE WHEN mc.tipo = 'gasto'  AND mc.metodo_pago = 'Efectivo' THEN mc.monto ELSE 0 END), 0) as total_gastos,
                COALESCE(SUM(CASE WHEN mc.tipo = 'retiro' AND mc.metodo_pago = 'Efectivo' THEN mc.monto ELSE 0 END), 0) as total_retiros
            FROM movimientos_caja mc
            LEFT JOIN venta v ON mc.venta_id = v.id
            WHERE DATE(mc.fecha) = CURDATE()
              AND (mc.venta_id IS NULL OR v.estado <> 'ANULADA')";

        $stmt = $this->db->prepare($sqlMovimientosHoy);
        $stmt->execute();
        $movimientosHoy = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // HISTÓRICO (excluye ventas anuladas)
        $sqlEfectivoTotal = "SELECT
                COALESCE(SUM(CASE WHEN mc.tipo = 'ingreso' AND mc.metodo_pago = 'Efectivo' THEN mc.monto ELSE 0 END), 0) as total_ingresos_efectivo,
                COALESCE(SUM(CASE WHEN mc.tipo = 'gasto'  AND mc.metodo_pago = 'Efectivo' THEN mc.monto ELSE 0 END), 0) as total_gastos_historico,
                COALESCE(SUM(CASE WHEN mc.tipo = 'retiro' AND mc.metodo_pago = 'Efectivo' THEN mc.monto ELSE 0 END), 0) as total_retiros_historico
            FROM movimientos_caja mc
            LEFT JOIN venta v ON mc.venta_id = v.id
            WHERE (mc.venta_id IS NULL OR v.estado <> 'ANULADA')";

        $stmt = $this->db->prepare($sqlEfectivoTotal);
        $stmt->execute();
        $movimientosTotal = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $ingresosEfectivoHoy = (float)($movimientosHoy['ingresos_efectivo'] ?? 0);
        $ingresosOtrosHoy    = (float)($movimientosHoy['ingresos_otros'] ?? 0);
        $totalGastosHoy      = (float)($movimientosHoy['total_gastos'] ?? 0);
        $totalRetirosHoy     = (float)($movimientosHoy['total_retiros'] ?? 0);

        $totalIngresosEfectivo = (float)($movimientosTotal['total_ingresos_efectivo'] ?? 0);
        $totalGastosHistorico  = (float)($movimientosTotal['total_gastos_historico'] ?? 0);
        $totalRetirosHistorico = (float)($movimientosTotal['total_retiros_historico'] ?? 0);

        // “ganancias_totales” aquí es KPI diario de ingresos (no utilidades).
        $gananciasTotales = $ingresosEfectivoHoy + $ingresosOtrosHoy;

        // efectivo físico acumulado = ingresos efectivo - gastos efectivo - retiros efectivo
        $efectivoEnCaja = $totalIngresosEfectivo - $totalGastosHistorico - $totalRetirosHistorico;

        return [
            'ingresos_efectivo'   => $ingresosEfectivoHoy,
            'ingresos_otros'      => $ingresosOtrosHoy,
            'ganancias_totales'   => $gananciasTotales,
            'total_gastos'        => $totalGastosHoy,
            'total_retiros'       => $totalRetirosHoy,
            'efectivo_en_caja'    => $efectivoEnCaja,
        ];
    }

    /**
     * ✅ Obtener balance por rango de fechas
     * ✅ Excluye ingresos ligados a ventas ANULADAS
     */
    public function obtenerBalance(string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = [];
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $where[] = "DATE(mc.fecha) BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $fechaInicio;
            $params[':fecha_fin'] = $fechaFin;
        } elseif ($fechaInicio) {
            $where[] = "DATE(mc.fecha) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        // excluir ventas anuladas
        $where[] = "(mc.venta_id IS NULL OR v.estado <> 'ANULADA')";

        $sql = "SELECT
                    mc.tipo,
                    mc.metodo_pago,
                    SUM(mc.monto) as total
                FROM movimientos_caja mc
                LEFT JOIN venta v ON mc.venta_id = v.id
                " . (!empty($where) ? "WHERE " . implode(" AND ", $where) : "") . "
                GROUP BY mc.tipo, mc.metodo_pago
                ORDER BY mc.tipo, mc.metodo_pago";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
