<?php

class VentasModel extends Model
{
    private PDO $db;
    private string $tableVentas  = "venta";
    private string $tableDetalle = "venta_detalle";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /* =========================================================
     * Helpers de infraestructura (evitar 1064 / HY093)
     * ========================================================= */

    private function execChecked(PDOStatement $stmt, array $params, string $tag): void
    {
        $sql = $stmt->queryString;
        error_log("🔍 [execChecked] TAG: {$tag}");
        error_log("🔍 [execChecked] SQL: {$sql}");
        error_log("🔍 [execChecked] Input Params: " . print_r($params, true));

        // 🔧 NORMALIZAR PARÁMETROS PRIMERO ([:param] -> :param)
        error_log("🔧 ORIGINAL PARAMS RECEIVED: " . json_encode($params));

        $normalizedParams = [];
        foreach ($params as $key => $value) {
            $originalKey = (string)$key;
            error_log("🔧 DEBUG: Processing key = '$originalKey' (raw key inspect: " . var_export($key, true) . ")");

            // Si el key tiene formato [:param], convertir a :param
            if (str_starts_with($originalKey, '[:') && str_ends_with($originalKey, ']')) {
                // Extraer el nombre del parámetro: [:param] -> param
                $paramName = substr($originalKey, 2, -1);
                $finalKey = ':' . $paramName;
                $normalizedParams[$finalKey] = $value;
                error_log("🔧 NORMALIZED: '$originalKey' -> '$finalKey'");
            } else {
                $normalizedParams[$originalKey] = $value;
                error_log("🔧 UNCHANGED: '$originalKey'");
            }
        }

        error_log("🔧 BEFORE NORMALIZATION: " . json_encode(array_keys($params)));
        error_log("🔧 AFTER NORMALIZATION: " . json_encode(array_keys($normalizedParams)));

        // Usar parámetros normalizados para todo lo que sigue
        $params = $normalizedParams;
        error_log("🔧 [execChecked] After Normalization: " . print_r($params, true));

        // Extraer placeholders :nombre
        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $m);
        $placeholders = array_unique($m[1]);

        // Normalizar keys de params (quitar corchetes, dejar solo el nombre)
        $paramKeys = array_map(function ($k) {
            $keyStr = (string)$k;
            // Quitar corchetes [ ] y dos puntos :
            $keyStr = str_replace(['[', ']', ':'], '', $keyStr);
            return $keyStr;
        }, array_keys($params));

        error_log("🔍 [execChecked] Placeholders: " . implode(', ', $placeholders));
        error_log("🔍 [execChecked] ParamKeys: " . implode(', ', $paramKeys));

        $missing = array_values(array_diff($placeholders, $paramKeys));
        $extra   = array_values(array_diff($paramKeys, $placeholders));

        if (!empty($missing) || !empty($extra)) {
            error_log("=== HY093 PRECHECK [$tag] ===");
            error_log("SQL: " . $sql);
            error_log("Placeholders: " . implode(', ', $placeholders));
            error_log("Params: " . implode(', ', $paramKeys));
            if (!empty($missing)) error_log("MISSING: " . implode(', ', $missing));
            if (!empty($extra))   error_log("EXTRA: " . implode(', ', $extra));
            throw new Exception("HY093 precheck falló en [$tag]. Revise log.");
        }

        try {
            $stmt->execute($normalizedParams);
            error_log("✅ [execChecked] SUCCESS: {$tag}");
        } catch (PDOException $e) {
            error_log("❌ [execChecked] PDO FAIL [$tag] ===");
            error_log("SQL: " . $sql);
            error_log("Final Params Used: " . print_r($normalizedParams, true));
            error_log("PDO Error: " . $e->getMessage());
            throw $e;
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        $sql = "SELECT 1
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :t
                  AND COLUMN_NAME = :c
                LIMIT 1";
        $stmt = $this->db->prepare($sql);

        $this->execChecked($stmt, [
            ':t' => $table,
            ':c' => $column,
        ], "HAS_COLUMN_{$table}_{$column}");

        return (bool)$stmt->fetchColumn();
    }

    /* =========================================================
     * KPIs / Listados / Consultas
     * ========================================================= */

    public function getKpis(): array
    {
        $deudaOrigenExiste = $this->hasColumn($this->tableVentas, 'deuda_origen_id');

        $campoVentasDeDeuda = $deudaOrigenExiste
            ? "SUM(CASE WHEN deuda_origen_id IS NOT NULL THEN 1 ELSE 0 END) as ventas_desde_deudas,"
            : "0 as ventas_desde_deudas,";

        $sql = "SELECT
                    COUNT(*) as total_ventas,
                    SUM(CASE WHEN estado = 'CONFIRMADA' THEN 1 ELSE 0 END) as ventas_confirmadas,
                    SUM(CASE WHEN cotizacion_id IS NOT NULL THEN 1 ELSE 0 END) as ventas_convertidas,
                    {$campoVentasDeDeuda}
                    COALESCE(SUM(CASE WHEN estado = 'CONFIRMADA' THEN total ELSE 0 END), 0) as total_confirmado
                FROM {$this->tableVentas}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAllVentas(): array
    {
        $deudaOrigenExiste   = $this->hasColumn($this->tableVentas, 'deuda_origen_id');
        $observacionesExiste = $this->hasColumn($this->tableVentas, 'observaciones');

        $camposAdicionales = '';
        if ($deudaOrigenExiste)   $camposAdicionales .= ', v.deuda_origen_id';
        if ($observacionesExiste) $camposAdicionales .= ', v.observaciones';

        $sql = "SELECT
                    v.id,
                    v.cliente_id,
                    CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.nit as cliente_nit,
                    v.fecha_venta,
                    v.estado,
                    v.total,
                    v.total_pagado,
                    v.cotizacion_id
                    {$camposAdicionales}
                FROM {$this->tableVentas} v
                INNER JOIN clientes c ON v.cliente_id = c.id
                ORDER BY v.fecha_venta DESC, v.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVentaById(int $id): ?array
    {
        $sql = "SELECT
                    v.*,
                    CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.direccion as cliente_direccion,
                    c.nit as cliente_nit,
                    c.preferencia_metodo_pago as cliente_metodo_pago,
                    u.nombre as usuario_nombre
                FROM {$this->tableVentas} v
                INNER JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                WHERE v.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $this->execChecked($stmt, [':id' => $id], "GET_VENTA_BY_ID");

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getVentaDetalle(int $ventaId): array
    {
        $sql = "SELECT
                    vd.*,
                    p.nombre as producto_nombre,
                    p.sku as producto_sku
                FROM {$this->tableDetalle} vd
                INNER JOIN productos p ON vd.producto_id = p.id
                WHERE vd.venta_id = :venta_id
                ORDER BY vd.id ASC";

        $stmt = $this->db->prepare($sql);
        $this->execChecked($stmt, [':venta_id' => $ventaId], "GET_VENTA_DETALLE");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * Convertir Cotización -> Venta (blindado)
     * ========================================================= */

    public function convertirCotizacion(int $cotizacionId, int $usuarioId): int
    {
        error_log("🔍 [VentasModel] Iniciando convertirCotizacion ID: {$cotizacionId}, Usuario: {$usuarioId}");
        try {
            $this->db->beginTransaction();

            // Detectar columnas opcionales en tabla venta
            $colMetodoPago     = $this->hasColumn($this->tableVentas, 'metodo_pago');
            $colObservaciones  = $this->hasColumn($this->tableVentas, 'observaciones');

            // 1) Cotización activa
            $sqlCot = "SELECT id, cliente_id, subtotal, total, estado
                       FROM cotizacion
                       WHERE id = :id
                         AND estado = 'ACTIVA'
                       LIMIT 1";
            $stmtCot = $this->db->prepare($sqlCot);
            $this->execChecked($stmtCot, [':id' => $cotizacionId], "COTIZACION_GET");
            $cot = $stmtCot->fetch(PDO::FETCH_ASSOC);

            if (!$cot) {
                throw new Exception("Cotización no encontrada o no está activa");
            }

            // 2) Detalle cotización
            $sqlDet = "SELECT producto_id, cantidad, precio_unitario, total_linea
                       FROM cotizacion_detalle
                       WHERE cotizacion_id = :cid";
            $stmtDet = $this->db->prepare($sqlDet);
            $this->execChecked($stmtDet, [':cid' => $cotizacionId], "COTIZACION_DETALLE_GET");
            $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

            if (empty($detalles)) {
                throw new Exception("La cotización no tiene productos");
            }

            // 3) Insert venta (armado dinámico)
            $cols = [
                "cliente_id",
                "usuario_id",
                "cotizacion_id",
                "fecha_venta",
                "estado",
                "subtotal",
                "total",
                "total_pagado",
            ];
            $vals = [
                ":cliente_id",
                ":usuario_id",
                ":cotizacion_id",
                "NOW()",
                "'CONFIRMADA'",
                ":subtotal",
                ":total",
                ":total_pagado",
            ];
            $paramsVenta = [
                ':cliente_id'    => (int)$cot['cliente_id'],
                ':usuario_id'    => $usuarioId,
                ':cotizacion_id' => $cotizacionId,
                ':subtotal'      => (float)$cot['subtotal'],
                ':total'         => (float)$cot['total'],
                ':total_pagado'  => 0.00,
            ];

            if ($colMetodoPago) {
                $cols[] = "metodo_pago";
                $vals[] = ":metodo_pago";
                $paramsVenta[':metodo_pago'] = 'Efectivo';
            }
            if ($colObservaciones) {
                $cols[] = "observaciones";
                $vals[] = ":observaciones";
                $paramsVenta[':observaciones'] = "Convertida desde cotización #{$cotizacionId}";
            }

            $sqlVenta = "INSERT INTO {$this->tableVentas} (" . implode(", ", $cols) . ")
                         VALUES (" . implode(", ", $vals) . ")";
            $stmtVenta = $this->db->prepare($sqlVenta);
            $this->execChecked($stmtVenta, $paramsVenta, "VENTA_INSERT");

            $ventaId = (int)$this->db->lastInsertId();
            if ($ventaId <= 0) {
                throw new Exception("Error al obtener ID de la nueva venta");
            }

            // 4) Statements reutilizables
            $sqlDetVenta = "INSERT INTO {$this->tableDetalle}
                            (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                            VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal)";
            $stmtDetVenta = $this->db->prepare($sqlDetVenta);

            $sqlStock = "UPDATE productos
                         SET stock = stock - :cantidad
                         WHERE id = :producto_id
                           AND stock >= :cantidad";
            $stmtStock = $this->db->prepare($sqlStock);

            $sqlMov = "INSERT INTO movimientos_inventario
                       (producto_id, tipo, cantidad, costo_unitario, origen, origen_id, motivo, usuario_id, created_at)
                       VALUES (:producto_id, 'SALIDA', :cantidad, :costo_unitario, 'VENTA', :origen_id, :motivo, :usuario_id, NOW())";
            $stmtMov = $this->db->prepare($sqlMov);

            foreach ($detalles as $d) {
                $pid = (int)$d['producto_id'];
                $qty = (int)$d['cantidad'];
                $pu  = (float)$d['precio_unitario'];
                $sub = (float)$d['total_linea'];

                // 4.1 detalle venta
                $this->execChecked($stmtDetVenta, [
                    ':venta_id'        => $ventaId,
                    ':producto_id'     => $pid,
                    ':cantidad'        => $qty,
                    ':precio_unitario' => $pu,
                    ':subtotal'        => $sub,
                ], "VENTA_DETALLE_INSERT_PID_{$pid}");

                // 4.2 stock
                $this->execChecked($stmtStock, [
                    ':cantidad'    => $qty,
                    ':producto_id' => $pid,
                ], "STOCK_UPDATE_PID_{$pid}");

                if ($stmtStock->rowCount() === 0) {
                    throw new Exception("Stock insuficiente para producto ID {$pid}");
                }

                // 4.3 movimiento
                $this->execChecked($stmtMov, [
                    ':producto_id'    => $pid,
                    ':cantidad'       => $qty,
                    ':costo_unitario' => $pu,
                    ':origen_id'      => $ventaId,
                    ':motivo'         => "Venta desde cotización #{$cotizacionId}",
                    ':usuario_id'     => $usuarioId,
                ], "MOV_INSERT_PID_{$pid}");
            }

            // 5) marcar cotización convertida
            $sqlUpd = "UPDATE cotizacion SET estado = 'CONVERTIDA' WHERE id = :id";
            $stmtUpd = $this->db->prepare($sqlUpd);
            $this->execChecked($stmtUpd, [':id' => $cotizacionId], "COTIZACION_SET_CONVERTIDA");

            // 6) total gastado cliente
            $sqlCli = "UPDATE clientes
                       SET total_gastado = total_gastado + :total
                       WHERE id = :cliente_id";
            $stmtCli = $this->db->prepare($sqlCli);
            $this->execChecked($stmtCli, [
                ':total'      => (float)$cot['total'],
                ':cliente_id' => (int)$cot['cliente_id'],
            ], "CLIENTE_TOTAL_GASTADO_UPD");

            $this->db->commit();
            return $ventaId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al convertir cotización: " . $e->getMessage());
            throw $e;
        }
    }

    /* =========================================================
     * Crear Venta Manual (desde formulario)
     * ========================================================= */

    /**
     * Crear una nueva venta de forma manual
     * @param array $ventaData Datos de la venta: cliente_id, usuario_id, metodo_pago, subtotal, total, detalles[]
     * @return int ID de la venta creada
     */
    public function crearVentaManual(array $ventaData): int
    {
        error_log("🔍 [VentasModel] Iniciando crearVentaManual");
        error_log("🔍 [VentasModel] Data: " . print_r($ventaData, true));

        try {
            $this->db->beginTransaction();

            // Validar datos requeridos
            $clienteId = (int)($ventaData['cliente_id'] ?? 0);
            $usuarioId = (int)($ventaData['usuario_id'] ?? 0);
            $metodoPago = $ventaData['metodo_pago'] ?? 'Efectivo';
            $subtotal = (float)($ventaData['subtotal'] ?? 0);
            $total = (float)($ventaData['total'] ?? 0);
            $detalles = $ventaData['detalles'] ?? [];

            if ($clienteId <= 0 || $usuarioId <= 0 || empty($detalles)) {
                throw new Exception("Datos incompletos para crear la venta");
            }

            // Detectar columnas opcionales en tabla venta
            $colMetodoPago = $this->hasColumn($this->tableVentas, 'metodo_pago');
            $colObservaciones = $this->hasColumn($this->tableVentas, 'observaciones');

            // 1. Crear encabezado de venta
            $cols = [
                "cliente_id",
                "usuario_id",
                "fecha_venta",
                "estado",
                "subtotal",
                "total",
                "total_pagado"
            ];
            $vals = [
                ":cliente_id",
                ":usuario_id",
                "NOW()",
                "'CONFIRMADA'",
                ":subtotal",
                ":total",
                ":total_pagado"
            ];
            $paramsVenta = [
                ':cliente_id' => $clienteId,
                ':usuario_id' => $usuarioId,
                ':subtotal' => $subtotal,
                ':total' => $total,
                ':total_pagado' => 0.00
            ];

            if ($colMetodoPago) {
                $cols[] = "metodo_pago";
                $vals[] = ":metodo_pago";
                $paramsVenta[':metodo_pago'] = $metodoPago;
            }

            if ($colObservaciones) {
                $cols[] = "observaciones";
                $vals[] = ":observaciones";
                $paramsVenta[':observaciones'] = "Venta manual desde formulario";
            }

            $sqlVenta = "INSERT INTO {$this->tableVentas} (" . implode(", ", $cols) . ")
                         VALUES (" . implode(", ", $vals) . ")";
            $stmtVenta = $this->db->prepare($sqlVenta);
            $this->execChecked($stmtVenta, $paramsVenta, "VENTA_MANUAL_INSERT");

            $ventaId = (int)$this->db->lastInsertId();
            if ($ventaId <= 0) {
                throw new Exception("Error al obtener ID de la nueva venta");
            }

            // 2. Statements reutilizables para detalles
            $sqlDetVenta = "INSERT INTO {$this->tableDetalle}
                            (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                            VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal)";
            $stmtDetVenta = $this->db->prepare($sqlDetVenta);

            $sqlStock = "UPDATE productos
                         SET stock = stock - :cantidad
                         WHERE id = :producto_id
                           AND stock >= :cantidad";
            $stmtStock = $this->db->prepare($sqlStock);

            // 3. Procesar cada detalle
            foreach ($detalles as $i => $detalle) {
                $productoId = (int)($detalle['producto_id'] ?? 0);
                $cantidad = (int)($detalle['cantidad'] ?? 0);
                $precioUnitario = (float)($detalle['precio_unitario'] ?? 0);
                $subtotalLinea = (float)($detalle['subtotal'] ?? 0);

                if ($productoId <= 0 || $cantidad <= 0) {
                    throw new Exception("Detalle inválido en producto #{$i}");
                }

                // 3.1 Insertar detalle de venta
                $this->execChecked($stmtDetVenta, [
                    ':venta_id' => $ventaId,
                    ':producto_id' => $productoId,
                    ':cantidad' => $cantidad,
                    ':precio_unitario' => $precioUnitario,
                    ':subtotal' => $subtotalLinea
                ], "VENTA_DETALLE_MANUAL_PID_{$productoId}");

                // 3.2 Actualizar stock
                $this->execChecked($stmtStock, [
                    ':cantidad' => $cantidad,
                    ':producto_id' => $productoId
                ], "STOCK_UPDATE_MANUAL_PID_{$productoId}");

                // Verificar que se actualizó el stock
                if ($stmtStock->rowCount() == 0) {
                    throw new Exception("Stock insuficiente para producto ID: {$productoId}");
                }
            }

            // 4. Actualizar totales del cliente (opcional)
            $sqlClienteUpdate = "UPDATE clientes
                                SET total_gastado = total_gastado + :total
                                WHERE id = :cliente_id";
            $stmtClienteUpdate = $this->db->prepare($sqlClienteUpdate);
            $this->execChecked($stmtClienteUpdate, [
                ':total' => $total,
                ':cliente_id' => $clienteId
            ], "CLIENTE_TOTAL_GASTADO_MANUAL_UPD");

            $this->db->commit();
            error_log("✅ [VentasModel] Venta manual #{$ventaId} creada exitosamente");

            return $ventaId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("❌ [VentasModel] Error al crear venta manual: " . $e->getMessage());
            throw $e;
        }
    }
}
