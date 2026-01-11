<?php

require_once __DIR__ . '/../ventas/VentasModel.php';

class DeudoresModel extends Model
{
    private PDO $db;

    private string $table = "deuda";
    private string $tablePagos = "deuda_pagos";
    private string $tableDetalle = "deuda_detalle";

    public function __construct()
    {
        $this->db = Database::connect();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // =========================================================
    // UTIL: exists (SIN SHOW TABLES LIKE ?)
    // =========================================================
    private function tableExists(string $table): bool
    {
        $sql = "SELECT 1
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :t
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':t' => $table]);
        return (bool)$stmt->fetchColumn();
    }

    private function columnExists(string $table, string $column): bool
    {
        $sql = "SELECT 1
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :t
                  AND COLUMN_NAME = :c
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':t' => $table, ':c' => $column]);
        return (bool)$stmt->fetchColumn();
    }

    // =========================================================
    // LISTADO
    // =========================================================
    public function getAllDeudas(): array
    {
        $this->ensureTables();

        $estadoExiste     = $this->columnExists($this->table, 'estado');
        $anuladaAtExiste  = $this->columnExists($this->table, 'anulada_at');
        $anuladaPorExiste = $this->columnExists($this->table, 'anulada_por');

        $campoEstado      = $estadoExiste ? 'd.estado,' : "'ACTIVA' as estado,";
        $campoAnuladaAt   = $anuladaAtExiste ? 'd.anulada_at,' : "NULL as anulada_at,";
        $campoAnuladaPor  = $anuladaPorExiste ? 'd.anulada_por,' : "NULL as anulada_por,";

        $sql = "SELECT
                    d.id,
                    d.cliente_id,
                    d.usuario_id,
                    d.fecha,
                    d.total,
                    d.descripcion,
                    {$campoEstado}
                    d.created_at,
                    d.updated_at,
                    {$campoAnuladaAt}
                    {$campoAnuladaPor}
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente_nombre,
                    c.telefono AS cliente_telefono,
                    COALESCE(SUM(dp.monto), 0.00) AS total_pagado,
                    (d.total - COALESCE(SUM(dp.monto), 0.00)) AS saldo
                FROM {$this->table} d
                INNER JOIN clientes c ON c.id = d.cliente_id
                LEFT JOIN {$this->tablePagos} dp ON dp.deuda_id = d.id
                GROUP BY
                    d.id, d.cliente_id, d.usuario_id, d.fecha, d.total,
                    d.descripcion, d.created_at, d.updated_at,
                    c.nombre, c.apellido, c.telefono" .
            ($estadoExiste ? ", d.estado" : "") .
            ($anuladaAtExiste ? ", d.anulada_at" : "") .
            ($anuladaPorExiste ? ", d.anulada_por" : "") . "
                ORDER BY d.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CREAR DEUDA
    // =========================================================
    public function crearDeuda(array $data): int
    {
        if (empty($data['cliente_id']) || empty($data['usuario_id'])) {
            throw new Exception("cliente_id / usuario_id requeridos.");
        }
        if (!isset($data['total'])) {
            throw new Exception("total requerido.");
        }

        $this->ensureTables();

        try {
            $this->db->beginTransaction();

            // 1) Insert deuda
            $sql = "INSERT INTO {$this->table}
                        (cliente_id, usuario_id, fecha, total, descripcion)
                    VALUES
                        (:cliente_id, :usuario_id, NOW(), :total, :descripcion)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cliente_id'  => (int)$data['cliente_id'],
                ':usuario_id'  => (int)$data['usuario_id'],
                ':total'       => (float)$data['total'],
                ':descripcion' => (string)($data['descripcion'] ?? ''),
            ]);

            $deudaId = (int)$this->db->lastInsertId();
            if ($deudaId <= 0) {
                throw new Exception("No se pudo obtener ID de deuda.");
            }

            // 2) Insert detalle + descontar stock
            if (!empty($data['detalles']) && is_array($data['detalles'])) {

                $sqlDetalle = "INSERT INTO {$this->tableDetalle}
                                (deuda_id, producto_id, cantidad, precio_unitario, subtotal)
                               VALUES
                                (:deuda_id, :producto_id, :cantidad, :precio_unitario, :subtotal)";
                $stmtDetalle = $this->db->prepare($sqlDetalle);

                // ✅ FIX HY093: NO repetir :cantidad
                $sqlStock = "UPDATE productos
                                SET stock = stock - :cant_desc
                             WHERE id = :producto_id
                               AND stock >= :cant_check";
                $stmtStock = $this->db->prepare($sqlStock);

                // Statement para movimientos de inventario (SALIDA por deuda)
                $sqlMov = "INSERT INTO movimientos_inventario
                           (producto_id, tipo, cantidad, costo_unitario, origen, origen_id, motivo, usuario_id, created_at)
                           VALUES (:producto_id, 'SALIDA', :cantidad, :costo_unitario, 'DEUDA', :origen_id, :motivo, :usuario_id, NOW())";
                $stmtMov = $this->db->prepare($sqlMov);

                foreach ($data['detalles'] as $detalle) {
                    $productoId = (int)($detalle['producto_id'] ?? 0);
                    $cantidad   = (int)($detalle['cantidad'] ?? 0);
                    $precioU    = (float)($detalle['precio_unitario'] ?? 0);
                    $subtotal   = (float)($detalle['subtotal'] ?? 0);

                    if ($productoId <= 0 || $cantidad <= 0) {
                        continue;
                    }

                    // Insert detalle
                    $stmtDetalle->execute([
                        ':deuda_id'        => $deudaId,
                        ':producto_id'     => $productoId,
                        ':cantidad'        => $cantidad,
                        ':precio_unitario' => $precioU,
                        ':subtotal'        => $subtotal,
                    ]);

                    // Descontar stock
                    $stmtStock->execute([
                        ':cant_desc'   => $cantidad,
                        ':cant_check'  => $cantidad,
                        ':producto_id' => $productoId,
                    ]);

                    if ($stmtStock->rowCount() === 0) {
                        throw new Exception("Stock insuficiente para producto ID {$productoId}");
                    }

                    // Registrar movimiento de inventario (SALIDA por deuda)
                    // Obtener costo del producto para el registro correcto
                    $sqlCosto = "SELECT costo FROM productos WHERE id = :producto_id";
                    $stmtCosto = $this->db->prepare($sqlCosto);
                    $stmtCosto->execute([':producto_id' => $productoId]);
                    $productoCosto = $stmtCosto->fetchColumn();
                    $costoUnitario = $productoCosto !== false ? (float)$productoCosto : $precioU;

                    $stmtMov->execute([
                        ':producto_id' => $productoId,
                        ':cantidad' => $cantidad,
                        ':costo_unitario' => $costoUnitario,
                        ':origen_id' => $deudaId,
                        ':motivo' => "Deuda creada #{$deudaId}",
                        ':usuario_id' => (int)$data['usuario_id']
                    ]);
                }
            }

            $this->db->commit();
            return $deudaId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log("Error crearDeuda: " . $e->getMessage());
            throw $e;
        }
    }

    // =========================================================
    // GETS
    // =========================================================
    public function getDeudaById(int $id): ?array
    {
        $this->ensureTables();

        $sql = "SELECT
                    d.id,
                    d.cliente_id,
                    d.usuario_id,
                    d.fecha,
                    d.total,
                    d.descripcion,
                    d.estado,
                    d.created_at,
                    d.updated_at,
                    d.anulada_at,
                    d.anulada_por,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente_nombre,
                    c.telefono AS cliente_telefono,
                    COALESCE(SUM(dp.monto), 0.00) AS total_pagado,
                    (d.total - COALESCE(SUM(dp.monto), 0.00)) AS saldo
                FROM {$this->table} d
                INNER JOIN clientes c ON c.id = d.cliente_id
                LEFT JOIN {$this->tablePagos} dp ON dp.deuda_id = d.id
                WHERE d.id = :id
                GROUP BY
                    d.id, d.cliente_id, d.usuario_id, d.fecha, d.total,
                    d.descripcion, d.estado, d.created_at, d.updated_at,
                    d.anulada_at, d.anulada_por,
                    c.nombre, c.apellido, c.telefono
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function getDetalleProductos(int $deudaId): array
    {
        $this->ensureTables();

        $sql = "SELECT dd.*, p.nombre as producto_nombre, p.sku as producto_sku
                FROM {$this->tableDetalle} dd
                INNER JOIN productos p ON dd.producto_id = p.id
                WHERE dd.deuda_id = :deuda_id
                ORDER BY dd.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':deuda_id' => $deudaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPagosByDeuda(int $deudaId): array
    {
        $this->ensureTables();

        // ✅ Tabla real: dp.fecha
        $sql = "SELECT dp.*, u.nombre as usuario_nombre
                FROM {$this->tablePagos} dp
                LEFT JOIN usuarios u ON dp.usuario_id = u.id
                WHERE dp.deuda_id = :deuda_id
                ORDER BY dp.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':deuda_id' => $deudaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // PAGOS
    // =========================================================
    public function registrarPago(int $deudaId, float $monto, int $usuarioId, string $metodoPago = 'Efectivo'): bool
    {
        $this->ensureTables();

        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->tablePagos} (deuda_id, monto, metodo_pago, fecha, usuario_id)
                    VALUES (:deuda_id, :monto, :metodo_pago, NOW(), :usuario_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':deuda_id'    => $deudaId,
                ':monto'       => $monto,
                ':metodo_pago' => $metodoPago,
                ':usuario_id'  => $usuarioId,
            ]);

            $sqlUpdate = "UPDATE {$this->table}
                          SET total_pagado = COALESCE(total_pagado,0) + :monto
                          WHERE id = :id";
            $upd = $this->db->prepare($sqlUpdate);
            $upd->execute([':monto' => $monto, ':id' => $deudaId]);

            // Autoconvertir a venta si saldo <= 0
            $deudaInfo = $this->getDeudaById($deudaId);
            if ($deudaInfo) {
                $saldo = (float)$deudaInfo['total'] - ((float)$deudaInfo['total_pagado'] + $monto);
                if ($saldo <= 0) {
                    try {
                        $this->convertirDeudaAVenta($deudaId, $usuarioId);
                    } catch (Exception $e) {
                        error_log('Error convirtiendo deuda a venta: ' . $e->getMessage());
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log('Error registrarPago: ' . $e->getMessage());
            throw $e;
        }
    }

    public function ampliarDeuda(int $deudaId, float $monto): bool
    {
        $this->ensureTables();

        $sql = "UPDATE {$this->table} SET total = total + :monto WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':monto' => $monto, ':id' => $deudaId]);
    }

    // =========================================================
    // VENTA AUTO
    // =========================================================
    private function convertirDeudaAVenta(int $deudaId, int $usuarioId): int
    {
        $deuda = $this->getDeudaById($deudaId);
        if (!$deuda) throw new Exception("Deuda no encontrada");

        $detalleDeuda = $this->getDetalleProductos($deudaId);

        $ventasModel = new VentasModel();

        $ventaData = [
            'cliente_id'     => $deuda['cliente_id'],
            'usuario_id'     => $usuarioId,
            'total'          => $deuda['total'],
            'metodo_pago'    => 'Efectivo',
            'observaciones'  => 'Venta generada automáticamente de Deuda #' . $deudaId,
            'deuda_origen_id' => $deudaId,
            'detalles'       => array_map(function ($item) {
                return [
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => $item['subtotal'],
                ];
            }, $detalleDeuda),
        ];

        $ventaId = $ventasModel->crearVentaDesdeDeuda($ventaData);

        $this->marcarDeudaComoConvertida($deudaId, $ventaId);

        return $ventaId;
    }

    private function marcarDeudaComoConvertida(int $deudaId, int $ventaId): bool
    {
        try {
            $estadoExiste  = $this->columnExists($this->table, 'estado');
            $ventaIdExiste = $this->columnExists($this->table, 'venta_generada_id');

            if ($estadoExiste && $ventaIdExiste) {
                $sql = "UPDATE {$this->table}
                        SET estado = 'PAGADA',
                            venta_generada_id = :venta_id
                        WHERE id = :deuda_id";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    ':venta_id' => $ventaId,
                    ':deuda_id' => $deudaId
                ]);
            }

            return true;
        } catch (Exception $e) {
            error_log('Error marcando deuda como convertida: ' . $e->getMessage());
            return true;
        }
    }

    // =========================================================
    // ENSURE TABLES (no toca si ya existen)
    // =========================================================
    private function ensureTables(): void
    {
        if (
            $this->tableExists($this->table) &&
            $this->tableExists($this->tableDetalle) &&
            $this->tableExists($this->tablePagos)
        ) {
            return;
        }

        $sqlDeuda = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            cliente_id INT NOT NULL,
            usuario_id INT NOT NULL,
            fecha DATETIME NOT NULL,
            total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            total_pagado DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            descripcion TEXT,
            estado ENUM('ACTIVA','CANCELADA','PAGADA','CONVERTIDA','ANULADA') NOT NULL DEFAULT 'ACTIVA',
            venta_generada_id INT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            anulada_at DATETIME NULL,
            anulada_por INT NULL,
            PRIMARY KEY (id),
            KEY idx_estado (estado),
            KEY idx_venta_generada (venta_generada_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $sqlDetalle = "CREATE TABLE IF NOT EXISTS {$this->tableDetalle} (
            id INT NOT NULL AUTO_INCREMENT,
            deuda_id INT NOT NULL,
            producto_id INT NOT NULL,
            cantidad INT NOT NULL,
            precio_unitario DECIMAL(12,2) NOT NULL,
            subtotal DECIMAL(12,2) NOT NULL,
            PRIMARY KEY (id),
            KEY idx_deuda (deuda_id),
            KEY idx_producto (producto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $sqlPagos = "CREATE TABLE IF NOT EXISTS {$this->tablePagos} (
            id INT NOT NULL AUTO_INCREMENT,
            deuda_id INT NOT NULL,
            monto DECIMAL(12,2) NOT NULL,
            metodo_pago VARCHAR(50) DEFAULT 'Efectivo',
            fecha DATETIME NOT NULL,
            usuario_id INT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $this->db->exec($sqlDeuda);
        $this->db->exec($sqlDetalle);
        $this->db->exec($sqlPagos);
    }
}
