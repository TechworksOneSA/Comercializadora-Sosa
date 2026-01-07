<?php

class CotizacionesModel extends Model
{
    private PDO $db;
    private string $tableCotizaciones = "cotizacion";
    private string $tableDetalle = "cotizacion_detalle";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Listar todas las cotizaciones con información del cliente
     */
    public function listar(): array
    {
        $sql = "SELECT
                    c.id,
                    c.cliente_id,
                    CONCAT(cl.nombre, ' ', cl.apellido) as cliente_nombre,
                    cl.telefono as cliente_telefono,
                    cl.nit as cliente_nit,
                    c.fecha,
                    c.fecha_expiracion,
                    c.estado,
                    c.subtotal,
                    c.total,
                    DATEDIFF(c.fecha_expiracion, CURDATE()) as dias_restantes
                FROM {$this->tableCotizaciones} c
                INNER JOIN clientes cl ON c.cliente_id = cl.id
                ORDER BY c.fecha DESC, c.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener cotización por ID con información del cliente
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT
                    c.*,
                    CONCAT(cl.nombre, ' ', cl.apellido) as cliente_nombre,
                    cl.telefono as cliente_telefono,
                    cl.direccion as cliente_direccion,
                    cl.nit as cliente_nit
                FROM {$this->tableCotizaciones} c
                INNER JOIN clientes cl ON c.cliente_id = cl.id
                WHERE c.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtener detalle de productos de una cotización
     */
    public function obtenerDetalle(int $cotizacionId): array
    {
        $sql = "SELECT
                    cd.*,
                    p.nombre as producto_nombre,
                    p.sku as producto_sku
                FROM {$this->tableDetalle} cd
                INNER JOIN productos p ON cd.producto_id = p.id
                WHERE cd.cotizacion_id = :cotizacion_id
                ORDER BY cd.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':cotizacion_id', $cotizacionId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear cotización con su detalle (transacción)
     */
    public function crear(array $cabecera, array $detalles): int
    {
        try {
            $this->db->beginTransaction();

            // Insertar cabecera
            $sql = "INSERT INTO {$this->tableCotizaciones}
                    (cliente_id, fecha, fecha_expiracion, estado, subtotal, total, created_at)
                    VALUES (:cliente_id, :fecha, :fecha_expiracion, :estado, :subtotal, :total, NOW())";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':cliente_id' => $cabecera['cliente_id'],
                ':fecha' => $cabecera['fecha'],
                ':fecha_expiracion' => $cabecera['fecha_expiracion'],
                ':estado' => $cabecera['estado'] ?? 'ACTIVA',
                ':subtotal' => $cabecera['subtotal'],
                ':total' => $cabecera['total'],
            ]);

            if (!$result) {
                error_log("Error al crear cotización: " . json_encode($stmt->errorInfo()));
                throw new Exception("Error al insertar cotización en base de datos");
            }

            $cotizacionId = (int)$this->db->lastInsertId();

            if ($cotizacionId === 0) {
                error_log("Error: No se obtuvo ID de cotización válido");
                throw new Exception("Error al obtener ID de la nueva cotización");
            }

            // Insertar detalle
            $sqlDetalle = "INSERT INTO {$this->tableDetalle}
                           (cotizacion_id, producto_id, cantidad, precio_unitario, total_linea)
                           VALUES (:cotizacion_id, :producto_id, :cantidad, :precio_unitario, :total_linea)";

            $stmtDetalle = $this->db->prepare($sqlDetalle);

            foreach ($detalles as $detalle) {
                $result = $stmtDetalle->execute([
                    ':cotizacion_id' => $cotizacionId,
                    ':producto_id' => $detalle['producto_id'],
                    ':cantidad' => $detalle['cantidad'],
                    ':precio_unitario' => $detalle['precio_unitario'],
                    ':total_linea' => $detalle['total_linea'],
                ]);

                if (!$result) {
                    error_log("Error al insertar detalle de cotización: " . json_encode($stmtDetalle->errorInfo()));
                    throw new Exception("Error al insertar detalle de cotización para producto ID: " . $detalle['producto_id']);
                }
            }

            $this->db->commit();

            return $cotizacionId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear cotización: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar estado de una cotización
     */
    public function actualizarEstado(int $id, string $estado): bool
    {
        $sql = "UPDATE {$this->tableCotizaciones}
                SET estado = :estado,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':estado' => $estado,
        ]);
    }

    /**
     * Marcar cotizaciones como VENCIDA si fecha_expiracion < hoy
     */
    public function marcarVencidas(): int
    {
        $sql = "UPDATE {$this->tableCotizaciones}
                SET estado = 'VENCIDA'
                WHERE fecha_expiracion < CURDATE()
                  AND estado = 'ACTIVA'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Eliminar cotización (cabecera + detalle en cascada)
     */
    public function eliminar(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Eliminar detalle
            $sqlDetalle = "DELETE FROM {$this->tableDetalle} WHERE cotizacion_id = :id";
            $stmtDetalle = $this->db->prepare($sqlDetalle);
            $stmtDetalle->execute([':id' => $id]);

            // Eliminar cabecera
            $sql = "DELETE FROM {$this->tableCotizaciones} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);

            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar cotización: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar todas las cotizaciones vencidas
     */
    public function eliminarVencidas(): int
    {
        try {
            $this->db->beginTransaction();

            // Obtener IDs de cotizaciones vencidas
            $sqlIds = "SELECT id FROM {$this->tableCotizaciones}
                       WHERE estado = 'VENCIDA'";
            $stmtIds = $this->db->prepare($sqlIds);
            $stmtIds->execute();
            $ids = $stmtIds->fetchAll(PDO::FETCH_COLUMN);

            if (empty($ids)) {
                $this->db->commit();
                return 0;
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            // Eliminar detalle
            $sqlDetalle = "DELETE FROM {$this->tableDetalle} WHERE cotizacion_id IN ($placeholders)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);
            $stmtDetalle->execute($ids);

            // Eliminar cabecera
            $sql = "DELETE FROM {$this->tableCotizaciones} WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($ids);

            $eliminadas = $stmt->rowCount();

            $this->db->commit();

            return $eliminadas;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar cotizaciones vencidas: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener estadísticas de cotizaciones
     */
    public function obtenerEstadisticas(): array
    {
        $sql = "SELECT
                    COUNT(*) as total_cotizaciones,
                    SUM(CASE WHEN estado = 'ACTIVA' THEN 1 ELSE 0 END) as activas,
                    SUM(CASE WHEN estado = 'VENCIDA' THEN 1 ELSE 0 END) as vencidas,
                    SUM(CASE WHEN estado = 'CONVERTIDA' THEN 1 ELSE 0 END) as convertidas,
                    COALESCE(SUM(CASE WHEN estado = 'ACTIVA' THEN total ELSE 0 END), 0) as total_activas
                FROM {$this->tableCotizaciones}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

/*
 * =====================================================
 * SQL CREATE TABLE para referencia (ejecutar en phpMyAdmin)
 * =====================================================
 *
 * CREATE TABLE cotizaciones (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     cliente_id INT NOT NULL,
 *     fecha DATE NOT NULL,
 *     fecha_expiracion DATE NOT NULL,
 *     estado ENUM('ACTIVA', 'VENCIDA', 'CONVERTIDA') NOT NULL DEFAULT 'ACTIVA',
 *     subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 *     total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 *     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *     updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
 *     FOREIGN KEY (cliente_id) REFERENCES clientess(id) ON DELETE RESTRICT,
 *     INDEX idx_cliente (cliente_id),
 *     INDEX idx_estado (estado),
 *     INDEX idx_fecha_expiracion (fecha_expiracion)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * CREATE TABLE cotizaciones_detalle (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     cotizacion_id INT NOT NULL,
 *     producto_id INT NOT NULL,
 *     cantidad INT NOT NULL,
 *     precio_unitario DECIMAL(12,2) NOT NULL,
 *     total_linea DECIMAL(12,2) NOT NULL,
 *     FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id) ON DELETE CASCADE,
 *     FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
 *     INDEX idx_cotizacion (cotizacion_id),
 *     INDEX idx_producto (producto_id)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * =====================================================
 */
