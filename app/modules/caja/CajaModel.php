<?php

class CajaModel extends Model
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // ==================== MOVIMIENTOS DE CAJA ====================

    /**
     * Registrar ingreso de caja (cobros de ventas)
     */
    public function registrarIngreso(array $data): bool
    {
        $sql = "INSERT INTO movimientos_caja
                (tipo, concepto, monto, metodo_pago, observaciones, venta_id, usuario_id, fecha)
                VALUES ('ingreso', :concepto, :monto, :metodo_pago, :observaciones, :venta_id, :usuario_id, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':concepto' => $data['concepto'],
            ':monto' => $data['monto'],
            ':metodo_pago' => $data['metodo_pago'],
            ':observaciones' => $data['observaciones'] ?? '',
            ':venta_id' => $data['venta_id'] ?? null,
            ':usuario_id' => $data['usuario_id']
        ]);
    }

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
     * Obtener todos los movimientos de caja
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
        // Movimientos de caja registrados hoy (para KPIs diarios)
        $sqlMovimientosHoy = "SELECT
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' AND metodo_pago = 'Efectivo' THEN monto ELSE 0 END), 0) as ingresos_efectivo,
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' AND metodo_pago != 'Efectivo' THEN monto ELSE 0 END), 0) as ingresos_otros,
                    COALESCE(SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END), 0) as total_gastos,
                    COALESCE(SUM(CASE WHEN tipo = 'retiro' THEN monto ELSE 0 END), 0) as total_retiros
                FROM movimientos_caja
                WHERE DATE(fecha) = CURDATE()";

        $stmt = $this->db->prepare($sqlMovimientosHoy);
        $stmt->execute();
        $movimientosHoy = $stmt->fetch(PDO::FETCH_ASSOC);

        // Efectivo acumulado histórico (desde el inicio)
        $sqlEfectivoTotal = "SELECT
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' AND metodo_pago = 'Efectivo' THEN monto ELSE 0 END), 0) as total_ingresos_efectivo,
                    COALESCE(SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END), 0) as total_gastos_historico,
                    COALESCE(SUM(CASE WHEN tipo = 'retiro' THEN monto ELSE 0 END), 0) as total_retiros_historico
                FROM movimientos_caja";

        $stmt = $this->db->prepare($sqlEfectivoTotal);
        $stmt->execute();
        $movimientosTotal = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcular totales del día
        $ingresosEfectivoHoy = (float)($movimientosHoy['ingresos_efectivo'] ?? 0);
        $ingresosOtrosHoy = (float)($movimientosHoy['ingresos_otros'] ?? 0);
        $totalGastosHoy = (float)($movimientosHoy['total_gastos'] ?? 0);
        $totalRetirosHoy = (float)($movimientosHoy['total_retiros'] ?? 0);

        // Calcular efectivo acumulado histórico
        $totalIngresosEfectivo = (float)($movimientosTotal['total_ingresos_efectivo'] ?? 0);
        $totalGastosHistorico = (float)($movimientosTotal['total_gastos_historico'] ?? 0);
        $totalRetirosHistorico = (float)($movimientosTotal['total_retiros_historico'] ?? 0);

        $gananciasTotales = $ingresosEfectivoHoy + $ingresosOtrosHoy;
        $efectivoEnCaja = $totalIngresosEfectivo - $totalGastosHistorico - $totalRetirosHistorico; // Acumulativo

        return [
            'ingresos_efectivo' => $ingresosEfectivoHoy,
            'ingresos_otros' => $ingresosOtrosHoy,
            'ganancias_totales' => $gananciasTotales, // Solo del día
            'total_gastos' => $totalGastosHoy, // Solo del día
            'total_retiros' => $totalRetirosHoy, // Solo del día
            'efectivo_en_caja' => $efectivoEnCaja // Acumulativo histórico
        ];
    }

    /**
     * Obtener balance por rango de fechas
     */
    public function obtenerBalance(string $fechaInicio = null, string $fechaFin = null): array
    {
        $whereClause = '';
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $whereClause = 'WHERE DATE(fecha) BETWEEN :fecha_inicio AND :fecha_fin';
            $params[':fecha_inicio'] = $fechaInicio;
            $params[':fecha_fin'] = $fechaFin;
        } elseif ($fechaInicio) {
            $whereClause = 'WHERE DATE(fecha) >= :fecha_inicio';
            $params[':fecha_inicio'] = $fechaInicio;
        }

        $sql = "SELECT
                    tipo,
                    metodo_pago,
                    SUM(monto) as total
                FROM movimientos_caja
                {$whereClause}
                GROUP BY tipo, metodo_pago
                ORDER BY tipo, metodo_pago";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
