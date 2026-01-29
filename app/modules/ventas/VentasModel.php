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

        // Extraer placeholders del SQL
        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $matches);
        $sqlPlaceholders = $matches[0]; // Incluye los :
        $sqlParamNames = $matches[1];   // Solo nombres sin :

        // Obtener keys de los parámetros
        $paramKeys = array_keys($params);

        error_log("🔍 [execChecked] TAG: {$tag}");
        error_log("🔍 [execChecked] SQL: {$sql}");
        error_log("🔍 [execChecked] SQL Placeholders: [" . implode(', ', $sqlPlaceholders) . "]");
        error_log("🔍 [execChecked] Param Keys: [" . implode(', ', $paramKeys) . "]");
        error_log("🔍 [execChecked] Params Values: " . json_encode($params, JSON_PRETTY_PRINT));

        // Verificar coincidencia de parámetros
        $missingParams = [];
        $extraParams = [];

        foreach ($sqlPlaceholders as $placeholder) {
            if (!array_key_exists($placeholder, $params)) {
                $missingParams[] = $placeholder;
            }
        }

        foreach ($paramKeys as $key) {
            if (!in_array($key, $sqlPlaceholders)) {
                $extraParams[] = $key;
            }
        }

        if (!empty($missingParams)) {
            error_log("❌ [execChecked] MISSING PARAMS: [" . implode(', ', $missingParams) . "]");
        }

        if (!empty($extraParams)) {
            error_log("⚠️ [execChecked] EXTRA PARAMS: [" . implode(', ', $extraParams) . "]");
        }

        if (!empty($missingParams)) {
            throw new Exception("Missing required parameters for SQL: " . implode(', ', $missingParams) . " in query: {$tag}");
        }

        try {
            $result = $stmt->execute($params);
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("🚨 [execChecked] SQL EXECUTE FAILED: " . implode(", ", $errorInfo));
                throw new Exception("SQL Execute failed: " . implode(", ", $errorInfo));
            }
            error_log("✅ [execChecked] SUCCESS: {$tag}");
        } catch (PDOException $e) {
            error_log("🚨 [execChecked] PDO EXCEPTION: " . $e->getMessage());
            error_log("🚨 [execChecked] SQL: {$sql}");
            error_log("🚨 [execChecked] PARAMS: " . json_encode($params, JSON_PRETTY_PRINT));
            error_log("🚨 [execChecked] ERROR CODE: " . $e->getCode());
            error_log("🚨 [execChecked] ERROR FILE: " . $e->getFile() . ":" . $e->getLine());
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
                           AND stock >= :cantidad_check";
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
                    ':cantidad'       => $qty,
                    ':producto_id'    => $pid,
                    ':cantidad_check' => $qty,
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
            $fechaVenta = $ventaData['fecha_venta'] ?? null;
            $serieFactura = $ventaData['serie_factura'] ?? null;
            $numeroFactura = $ventaData['numero_factura'] ?? null;

            if ($clienteId <= 0 || $usuarioId <= 0 || empty($detalles)) {
                throw new Exception("Datos incompletos para crear la venta");
            }

            // Detectar columnas opcionales en tabla venta
            $colMetodoPago = $this->hasColumn($this->tableVentas, 'metodo_pago');
            $colObservaciones = $this->hasColumn($this->tableVentas, 'observaciones');
            $colFechaVenta = $this->hasColumn($this->tableVentas, 'fecha_venta');
            $colSerieFactura = $this->hasColumn($this->tableVentas, 'serie_factura');
            $colNumeroFactura = $this->hasColumn($this->tableVentas, 'numero_factura');

            // 1. Crear encabezado de venta
            $cols = [
                "cliente_id",
                "usuario_id",
                "estado",
                "subtotal",
                "total",
                "total_pagado"
            ];
            $vals = [
                ":cliente_id",
                ":usuario_id",
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

            if ($colFechaVenta && !empty($fechaVenta)) {
                $cols[] = "fecha_venta";
                $vals[] = ":fecha_venta";
                $paramsVenta[':fecha_venta'] = $fechaVenta;
            } else {
                // Si no existe la columna o no se envió, usar NOW()
                $cols[] = "fecha_venta";
                $vals[] = "NOW()";
            }

            if ($colSerieFactura && !empty($serieFactura)) {
                $cols[] = "serie_factura";
                $vals[] = ":serie_factura";
                $paramsVenta[':serie_factura'] = $serieFactura;
            }

            if ($colNumeroFactura && !empty($numeroFactura)) {
                $cols[] = "numero_factura";
                $vals[] = ":numero_factura";
                $paramsVenta[':numero_factura'] = $numeroFactura;
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
                           AND stock >= :cantidad_check";
            $stmtStock = $this->db->prepare($sqlStock);

            // 2.1 Statement para movimientos de inventario
            $sqlMov = "INSERT INTO movimientos_inventario
                       (producto_id, tipo, cantidad, costo_unitario, origen, origen_id, motivo, usuario_id, created_at)
                       VALUES (:producto_id, 'SALIDA', :cantidad, :costo_unitario, 'VENTA', :origen_id, :motivo, :usuario_id, NOW())";
            $stmtMov = $this->db->prepare($sqlMov);

            // 2.2 Statement para actualizar serie (productos_series)
            $sqlUpdateSerie = "UPDATE productos_series
                              SET estado = 'VENDIDO', venta_id = :venta_id, fecha_venta = NOW()
                              WHERE numero_serie = :numero_serie AND producto_id = :producto_id AND estado = 'EN_STOCK'";
            $stmtUpdateSerie = $this->db->prepare($sqlUpdateSerie);

            // 3. Procesar cada detalle
            foreach ($detalles as $i => $detalle) {
                $productoId = (int)($detalle['producto_id'] ?? 0);
                $cantidad = (int)($detalle['cantidad'] ?? 0);
                $precioUnitario = (float)($detalle['precio_unitario'] ?? 0);
                $subtotalLinea = (float)($detalle['subtotal'] ?? 0);
                $numeroSerie = trim((string)($detalle['numero_serie'] ?? '')); // ✅ Guardar como STRING

                if ($productoId <= 0 || $cantidad <= 0) {
                    throw new Exception("Detalle inválido en producto #{$i}");
                }

                error_log("🔍 [VentasModel] Procesando detalle #{$i}: producto_id={$productoId}, cantidad={$cantidad}, numero_serie='{$numeroSerie}'");

                // 3.1 Insertar detalle de venta
                $this->execChecked($stmtDetVenta, [
                    ':venta_id' => $ventaId,
                    ':producto_id' => $productoId,
                    ':cantidad' => $cantidad,
                    ':precio_unitario' => $precioUnitario,
                    ':subtotal' => $subtotalLinea
                ], "VENTA_DETALLE_MANUAL_PID_{$productoId}");

                // 3.2 Si tiene número de serie, actualizar en productos_series
                if (!empty($numeroSerie)) {
                    error_log("🔍 [VentasModel] Actualizando serie '{$numeroSerie}' para producto #{$productoId}");

                    $this->execChecked($stmtUpdateSerie, [
                        ':venta_id' => $ventaId,
                        ':numero_serie' => $numeroSerie, // ✅ Se guarda como STRING (VARCHAR)
                        ':producto_id' => $productoId
                    ], "UPDATE_SERIE_{$numeroSerie}_PID_{$productoId}");

                    if ($stmtUpdateSerie->rowCount() == 0) {
                        error_log("⚠️ [VentasModel] No se encontró la serie '{$numeroSerie}' en productos_series para el producto #{$productoId}");
                        // No lanzamos excepción para permitir continuar si la serie no existe en la tabla
                    } else {
                        error_log("✅ [VentasModel] Serie '{$numeroSerie}' marcada como VENDIDA");
                    }
                }

                // 3.3 Actualizar stock
                $this->execChecked($stmtStock, [
                    ':cantidad'       => $cantidad,
                    ':producto_id'    => $productoId,
                    ':cantidad_check' => $cantidad
                ], "STOCK_UPDATE_MANUAL_PID_{$productoId}");

                // Verificar que se actualizó el stock
                if ($stmtStock->rowCount() == 0) {
                    throw new Exception("Stock insuficiente para producto ID: {$productoId}");
                }

                // 3.4 Registrar movimiento de inventario (SALIDA por venta)
                // Obtener costo del producto para el registro correcto
                $sqlCosto = "SELECT costo FROM productos WHERE id = :producto_id";
                $stmtCosto = $this->db->prepare($sqlCosto);
                $stmtCosto->execute([':producto_id' => $productoId]);
                $productoCosto = $stmtCosto->fetchColumn();
                $costoUnitario = $productoCosto !== false ? (float)$productoCosto : $precioUnitario;

                $this->execChecked($stmtMov, [
                    ':producto_id' => $productoId,
                    ':cantidad' => $cantidad,
                    ':costo_unitario' => $costoUnitario,
                    ':origen_id' => $ventaId,
                    ':motivo' => "Venta manual #{$ventaId}",
                    ':usuario_id' => $usuarioId
                ], "MOV_INVENTARIO_MANUAL_PID_{$productoId}");
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

    /**
     * Crear venta desde deuda saldada
     */
    public function crearVentaDesdeDeuda(array $ventaData): int
    {
        error_log("🔍 [VentasModel] Iniciando creación de venta desde deuda saldada");
        try {
            $this->db->beginTransaction();

            $clienteId = (int)$ventaData['cliente_id'];
            $usuarioId = (int)$ventaData['usuario_id'];
            $total = (float)$ventaData['total'];
            $metodoPago = $ventaData['metodo_pago'] ?? 'Efectivo';
            $observaciones = $ventaData['observaciones'] ?? '';
            $deudaOrigenId = (int)($ventaData['deuda_origen_id'] ?? 0);
            $detalles = $ventaData['detalles'] ?? [];

            if ($clienteId <= 0) throw new Exception("Cliente ID inválido");
            if ($usuarioId <= 0) throw new Exception("Usuario ID inválido");
            if (empty($detalles)) throw new Exception("No hay productos en la venta");

            // Detectar columnas opcionales
            $colMetodoPago = $this->hasColumn($this->tableVentas, 'metodo_pago');
            $colObservaciones = $this->hasColumn($this->tableVentas, 'observaciones');
            $colDeudaOrigen = $this->hasColumn($this->tableVentas, 'deuda_origen_id');

            // 1. Insert venta
            $cols = ["cliente_id", "usuario_id", "fecha_venta", "estado", "subtotal", "total", "total_pagado"];
            $vals = [":cliente_id", ":usuario_id", "NOW()", "'CONFIRMADA'", ":subtotal", ":total", ":total_pagado"];
            $paramsVenta = [
                ':cliente_id' => $clienteId,
                ':usuario_id' => $usuarioId,
                ':subtotal' => $total,
                ':total' => $total,
                ':total_pagado' => $total, // Ya está pagada al ser de deuda saldada
            ];

            if ($colMetodoPago) {
                $cols[] = "metodo_pago";
                $vals[] = ":metodo_pago";
                $paramsVenta[':metodo_pago'] = $metodoPago;
            }

            if ($colObservaciones) {
                $cols[] = "observaciones";
                $vals[] = ":observaciones";
                $paramsVenta[':observaciones'] = $observaciones;
            }

            if ($colDeudaOrigen) {
                $cols[] = "deuda_origen_id";
                $vals[] = ":deuda_origen_id";
                $paramsVenta[':deuda_origen_id'] = $deudaOrigenId;
            }

            $sqlVenta = "INSERT INTO {$this->tableVentas} (" . implode(", ", $cols) . ")
                         VALUES (" . implode(", ", $vals) . ")";
            $stmtVenta = $this->db->prepare($sqlVenta);
            $this->execChecked($stmtVenta, $paramsVenta, "VENTA_DESDE_DEUDA_INSERT");

            $ventaId = (int)$this->db->lastInsertId();
            if ($ventaId <= 0) {
                throw new Exception("Error al obtener ID de la nueva venta desde deuda");
            }

            // 2. Insert detalles (sin descontar stock ya que fue descontado al crear la deuda)
            $sqlDetVenta = "INSERT INTO {$this->tableDetalle}
                            (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                            VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal)";
            $stmtDetVenta = $this->db->prepare($sqlDetVenta);

            foreach ($detalles as $d) {
                $this->execChecked($stmtDetVenta, [
                    ':venta_id' => $ventaId,
                    ':producto_id' => (int)$d['producto_id'],
                    ':cantidad' => (float)$d['cantidad'],
                    ':precio_unitario' => (float)$d['precio_unitario'],
                    ':subtotal' => (float)$d['subtotal'],
                ], "VENTA_DETALLE_DESDE_DEUDA_PID_{$d['producto_id']}");
            }

            // 3. Actualizar total gastado del cliente
            $sqlCli = "UPDATE clientes
                       SET total_gastado = total_gastado + :total
                       WHERE id = :cliente_id";
            $stmtCli = $this->db->prepare($sqlCli);
            $this->execChecked($stmtCli, [
                ':total' => $total,
                ':cliente_id' => $clienteId,
            ], "CLIENTE_TOTAL_GASTADO_DESDE_DEUDA_UPD");

            $this->db->commit();
            error_log("✅ [VentasModel] Venta desde deuda #{$ventaId} creada exitosamente");

            return $ventaId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("❌ [VentasModel] Error al crear venta desde deuda: " . $e->getMessage());
            throw $e;
        }
    }
public function anularVenta(int $ventaId, int $usuarioId): bool
{
    try {
        $this->db->beginTransaction();

        // Detectar columnas (porque usted antes asumió que no existían)
        $colAnuladaAt  = $this->hasColumn($this->tableVentas, 'anulada_at');
        $colAnuladaPor = $this->hasColumn($this->tableVentas, 'anulada_por');
        $colUpdatedAt  = $this->hasColumn($this->tableVentas, 'updated_at');

        // 1) Obtener venta (con lock)
        $sqlVenta = "SELECT * FROM {$this->tableVentas} WHERE id = :id LIMIT 1 FOR UPDATE";
        $stmtVenta = $this->db->prepare($sqlVenta);
        $this->execChecked($stmtVenta, [':id' => $ventaId], "ANULAR_VENTA_GET");

        $venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);
        if (!$venta) throw new Exception("Venta no encontrada");

        // Idempotente: si ya está anulada, igual garantizamos anulada_at (por si quedó NULL)
        if (($venta['estado'] ?? '') === 'ANULADA') {

            // Si la venta ya está ANULADA pero anulada_at está NULL, lo corregimos.
            $anuladaAtActual = $venta['anulada_at'] ?? null;

            if ($colAnuladaAt && empty($anuladaAtActual)) {
                $sets = [];
                $params = [':id' => $ventaId];

                $sets[] = "anulada_at = NOW()";
                if ($colAnuladaPor) {
                    $sets[] = "anulada_por = :usuario_id";
                    $params[':usuario_id'] = $usuarioId;
                }
                if ($colUpdatedAt) $sets[] = "updated_at = NOW()";

                $sqlFix = "UPDATE {$this->tableVentas} SET " . implode(", ", $sets) . " WHERE id = :id";
                $stmtFix = $this->db->prepare($sqlFix);
                $this->execChecked($stmtFix, $params, "ANULAR_VENTA_FIX_ANULADA_AT");
            }

            $this->db->commit();
            return true;
        }

        // 2) Obtener detalle
        $detalle = $this->getVentaDetalle($ventaId);
        if (empty($detalle)) throw new Exception("La venta no tiene detalle");

        // 3) Revertir stock + movimientos inventario (ENTRADA)
        $sqlStock = "UPDATE productos
                     SET stock = stock + :cantidad
                     WHERE id = :producto_id";
        $stmtStock = $this->db->prepare($sqlStock);

        $sqlMovInv = "INSERT INTO movimientos_inventario
                      (producto_id, tipo, cantidad, costo_unitario, origen, origen_id, motivo, usuario_id)
                      VALUES (:producto_id, 'ENTRADA', :cantidad, :costo_unitario, 'DEVOLUCION', :origen_id, :motivo, :usuario_id)";
        $stmtMovInv = $this->db->prepare($sqlMovInv);

        $sqlCosto = "SELECT costo FROM productos WHERE id = :producto_id LIMIT 1";
        $stmtCosto = $this->db->prepare($sqlCosto);

        foreach ($detalle as $d) {
            $pid = (int)$d['producto_id'];
            $qty = (float)$d['cantidad'];

            $this->execChecked($stmtStock, [
                ':cantidad'   => $qty,
                ':producto_id'=> $pid,
            ], "ANULAR_STOCK_RESTORE_PID_{$pid}");

            $this->execChecked($stmtCosto, [':producto_id' => $pid], "ANULAR_GET_COSTO_PID_{$pid}");
            $costo = $stmtCosto->fetchColumn();
            $costoUnit = $costo !== false ? (float)$costo : (float)$d['precio_unitario'];

            $this->execChecked($stmtMovInv, [
                ':producto_id'   => $pid,
                ':cantidad'      => $qty,
                ':costo_unitario'=> $costoUnit,
                ':origen_id'     => $ventaId,
                ':motivo'        => "Reverso por anulación de venta #{$ventaId}",
                ':usuario_id'    => $usuarioId,
            ], "ANULAR_MOV_INV_PID_{$pid}");
        }

        // 4) Marcar venta como ANULADA (CORRECTO: guardar anulada_at/anulada_por)
        $sets = ["estado = 'ANULADA'"];
        $paramsUpd = [':id' => $ventaId];

        if ($colAnuladaAt) {
            $sets[] = "anulada_at = NOW()";
        }
        if ($colAnuladaPor) {
            $sets[] = "anulada_por = :usuario_id";
            $paramsUpd[':usuario_id'] = $usuarioId;
        }
        if ($colUpdatedAt) {
            $sets[] = "updated_at = NOW()";
        }

        $sqlUpd = "UPDATE {$this->tableVentas}
                   SET " . implode(", ", $sets) . "
                   WHERE id = :id";
        $stmtUpd = $this->db->prepare($sqlUpd);
        $this->execChecked($stmtUpd, $paramsUpd, "ANULAR_VENTA_UPDATE");

        // 5) Reverso de caja (si hubo cobro)
        $totalPagado = (float)($venta['total_pagado'] ?? 0);

        if ($totalPagado > 0) {

            // Verificar si ya existe reverso (idempotencia)
            $sqlExisteRev = "SELECT id
                             FROM movimientos_caja
                             WHERE venta_id = :venta_id
                               AND tipo = 'gasto'
                               AND concepto LIKE :concepto
                             LIMIT 1";
            $stmtExisteRev = $this->db->prepare($sqlExisteRev);
            $stmtExisteRev->execute([
                ':venta_id' => $ventaId,
                ':concepto' => "Reverso por anulación de venta #{$ventaId}%",
            ]);
            $yaExiste = $stmtExisteRev->fetchColumn();

            if (!$yaExiste) {
                $sqlCaja = "INSERT INTO movimientos_caja
                            (tipo, concepto, monto, metodo_pago, observaciones, venta_id, usuario_id, fecha)
                            VALUES
                            ('gasto', :concepto, :monto, :metodo_pago, :obs, :venta_id, :usuario_id, NOW())";
                $stmtCaja = $this->db->prepare($sqlCaja);

                $paramsCaja = [
                    ':concepto'    => "Reverso por anulación de venta #{$ventaId}",
                    ':monto'       => $totalPagado,
                    ':metodo_pago' => (string)($venta['metodo_pago'] ?? 'Efectivo'),
                    ':obs'         => "Reverso automático del cobro al anular la venta.",
                    ':venta_id'    => $ventaId,
                    ':usuario_id'  => $usuarioId
                ];

                $ok = $stmtCaja->execute($paramsCaja);
                if (!$ok) {
                    $errorInfo = $stmtCaja->errorInfo();
                    throw new Exception("Error al crear movimiento de caja reverso: " . implode(", ", $errorInfo));
                }
            }
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        if ($this->db->inTransaction()) $this->db->rollBack();
        error_log("🚨 [VentasModel] Error anularVenta: " . $e->getMessage());
        throw $e;
    }


}
