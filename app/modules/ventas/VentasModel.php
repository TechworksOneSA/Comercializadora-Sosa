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
