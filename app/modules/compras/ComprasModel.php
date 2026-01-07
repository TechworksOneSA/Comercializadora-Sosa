<?php

class ComprasModel extends Model
{
    /** @var PDO */
    private $db;

    private string $tableCompras = "compras";
    private string $tableDetalle = "compras_detalle";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Listado simple de compras (para la tabla del módulo).
     */
    public function listar(int $limite = 50): array
    {
        $sql = "SELECT
                    c.id,
                    DATE_FORMAT(c.fecha, '%Y-%m-%d') AS fecha_compra,
                    c.numero_factura AS numero_doc,
                    c.total AS total_neto,
                    c.estado,
                    c.proveedor_id,
                    c.usuario_id,
                    p.nombre AS proveedor_nombre,
                    p.nit AS proveedor_nit
                FROM {$this->tableCompras} c
                LEFT JOIN proveedores p ON p.id = c.proveedor_id
                ORDER BY c.id DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el encabezado de una compra.
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT
                    id,
                    proveedor_id,
                    usuario_id,
                    DATE_FORMAT(fecha, '%Y-%m-%d') AS fecha_compra,
                    serie_factura,
                    numero_factura AS numero_doc,
                    subtotal AS total_bruto,
                    iva,
                    total AS total_neto,
                    estado
                FROM {$this->tableCompras}
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Crea la compra + detalle y actualiza inventario.
     *
     * $header: datos de encabezado que vienen del controlador:
     *   proveedor_id, usuario_id, fecha_compra, numero_doc,
     *   total_bruto, descuento, total_neto, estado, notas (opcional),
     *   iva (opcional), serie_factura (opcional)
     *
     * $detalles: arreglo de renglones:
     *   [producto_id, cantidad, costo_unitario, descuento, subtotal]
     */
    public function crearCompra(array $header, array $detalles): int
    {
        try {
            $this->db->beginTransaction();

            // Normalización de datos de encabezado según su esquema real
            $proveedorId   = (int)$header['proveedor_id'];
            $usuarioId     = (int)$header['usuario_id'];
            $fecha         = $header['fecha_compra'];                 // DATE (Y-m-d)
            $serieFactura  = $header['serie_factura'] ?? '';          // opcional
            $numeroFactura = $header['numero_doc'] ?? '';             // viene del form
            $subtotal      = (float)$header['total_bruto'];           // se mapea a columna subtotal
            $descuentoCab  = (float)($header['descuento'] ?? 0);      // solo para lógica interna
            $iva           = (float)($header['iva'] ?? 0);            // si no se manda, 0
            $totalNeto     = (float)$header['total_neto'];            // se mapea a columna total
            $estado        = $header['estado'] ?? 'REGISTRADA';
            // $notas       = $header['notas'] ?? '';                 // NO hay columna notas en la tabla

            // Insert encabezado en la tabla compras
            $sql = "INSERT INTO {$this->tableCompras} (
                        proveedor_id,
                        usuario_id,
                        fecha,
                        serie_factura,
                        numero_factura,
                        subtotal,
                        iva,
                        total,
                        estado
                    ) VALUES (
                        :proveedor_id,
                        :usuario_id,
                        :fecha,
                        :serie_factura,
                        :numero_factura,
                        :subtotal,
                        :iva,
                        :total,
                        :estado
                    )";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':proveedor_id'   => $proveedorId,
                ':usuario_id'     => $usuarioId,
                ':fecha'          => $fecha,
                ':serie_factura'  => $serieFactura,
                ':numero_factura' => $numeroFactura,
                ':subtotal'       => $subtotal,
                ':iva'            => $iva,
                ':total'          => $totalNeto,
                ':estado'         => $estado,
            ]);

            if (!$result) {
                error_log("Error al crear compra: " . json_encode($stmt->errorInfo()));
                throw new Exception("Error al insertar compra en base de datos");
            }

            $compraId = (int)$this->db->lastInsertId();

            if ($compraId === 0) {
                error_log("Error: No se obtuvo ID de compra válido");
                throw new Exception("Error al obtener ID de la nueva compra");
            }

            // Insert detalle + actualizar inventario
            $sqlDet = "INSERT INTO {$this->tableDetalle} (
                           compra_id,
                           producto_id,
                           cantidad,
                           costo_unitario,
                           descuento,
                           subtotal
                       ) VALUES (
                           :compra_id,
                           :producto_id,
                           :cantidad,
                           :costo_unitario,
                           :descuento,
                           :subtotal
                       )";

            $stmtDet = $this->db->prepare($sqlDet);

            // OJO: aquí uso 'costo' (coincide con su form de productos)
            $sqlUpdProd = "UPDATE productos
               SET stock = stock + :cantidad,
                   costo_actual = :costo_unitario
               WHERE id = :producto_id";

            $stmtProd = $this->db->prepare($sqlUpdProd);

            foreach ($detalles as $item) {
                $productoId    = (int)$item['producto_id'];
                $cantidad      = (float)$item['cantidad'];
                $costoUnitario = (float)$item['costo_unitario'];
                $descuentoDet  = (float)($item['descuento'] ?? 0);
                $subtotalDet   = (float)$item['subtotal'];

                // saltar renglones vacíos
                if ($productoId <= 0 || $cantidad <= 0) {
                    continue;
                }

                // Insert detalle
                $result = $stmtDet->execute([
                    ':compra_id'      => $compraId,
                    ':producto_id'    => $productoId,
                    ':cantidad'       => $cantidad,
                    ':costo_unitario' => $costoUnitario,
                    ':descuento'      => $descuentoDet,
                    ':subtotal'       => $subtotalDet,
                ]);

                if (!$result) {
                    error_log("Error al insertar detalle de compra: " . json_encode($stmtDet->errorInfo()));
                    throw new Exception("Error al insertar detalle de compra para producto ID: $productoId");
                }

                // Actualizar producto (stock y costo)
                $result = $stmtProd->execute([
                    ':cantidad'       => $cantidad,
                    ':costo_unitario' => $costoUnitario,
                    ':producto_id'    => $productoId,
                ]);

                if (!$result) {
                    error_log("Error al actualizar producto en compra: " . json_encode($stmtProd->errorInfo()));
                    throw new Exception("Error al actualizar stock del producto ID: $productoId");
                }
            }

            $this->db->commit();
            return $compraId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene los detalles de una compra con información del producto
     */
    public function obtenerDetalles(int $compraId): array
    {
        $sql = "SELECT
                    d.*,
                    p.nombre AS producto_nombre,
                    p.sku AS producto_sku,
                    p.stock AS stock_actual
                FROM {$this->tableDetalle} d
                JOIN productos p ON p.id = d.producto_id
                WHERE d.compra_id = :compra_id
                ORDER BY d.id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':compra_id', $compraId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza una compra existente.
     * 1. Revierte el inventario de los productos antiguos
     * 2. Actualiza el encabezado
     * 3. Elimina los detalles antiguos
     * 4. Inserta los nuevos detalles
     * 5. Actualiza el inventario con los nuevos productos
     */
    public function actualizarCompra(int $compraId, array $header, array $detalles): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener detalles antiguos para revertir inventario
            $detallesAntiguos = $this->obtenerDetalles($compraId);

            // Revertir stock de productos antiguos (restar lo que se había sumado)
            $sqlRevertir = "UPDATE productos
                           SET stock = stock - :cantidad
                           WHERE id = :producto_id";
            $stmtRevertir = $this->db->prepare($sqlRevertir);

            foreach ($detallesAntiguos as $det) {
                $stmtRevertir->execute([
                    ':cantidad' => (float)$det['cantidad'],
                    ':producto_id' => (int)$det['producto_id']
                ]);
            }

            // 2. Actualizar encabezado
            $proveedorId   = (int)$header['proveedor_id'];
            $fecha         = $header['fecha_compra'];
            $numeroFactura = $header['numero_doc'] ?? '';
            $subtotal      = (float)$header['total_bruto'];
            $iva           = (float)($header['iva'] ?? 0);
            $totalNeto     = (float)$header['total_neto'];

            $sqlUpdate = "UPDATE {$this->tableCompras}
                         SET proveedor_id = :proveedor_id,
                             fecha = :fecha,
                             numero_factura = :numero_factura,
                             subtotal = :subtotal,
                             iva = :iva,
                             total = :total
                         WHERE id = :compra_id";

            $stmt = $this->db->prepare($sqlUpdate);
            $stmt->execute([
                ':proveedor_id'   => $proveedorId,
                ':fecha'          => $fecha,
                ':numero_factura' => $numeroFactura,
                ':subtotal'       => $subtotal,
                ':iva'            => $iva,
                ':total'          => $totalNeto,
                ':compra_id'      => $compraId
            ]);

            // 3. Eliminar detalles antiguos
            $sqlDelete = "DELETE FROM {$this->tableDetalle} WHERE compra_id = :compra_id";
            $stmtDelete = $this->db->prepare($sqlDelete);
            $stmtDelete->execute([':compra_id' => $compraId]);

            // 4. Insertar nuevos detalles
            $sqlDet = "INSERT INTO {$this->tableDetalle} (
                           compra_id,
                           producto_id,
                           cantidad,
                           costo_unitario,
                           descuento,
                           subtotal
                       ) VALUES (
                           :compra_id,
                           :producto_id,
                           :cantidad,
                           :costo_unitario,
                           :descuento,
                           :subtotal
                       )";

            $stmtDet = $this->db->prepare($sqlDet);

            // 5. Actualizar inventario con nuevos productos
            $sqlUpdProd = "UPDATE productos
                          SET stock = stock + :cantidad,
                              costo_actual = :costo_unitario
                          WHERE id = :producto_id";

            $stmtProd = $this->db->prepare($sqlUpdProd);

            foreach ($detalles as $item) {
                $productoId    = (int)$item['producto_id'];
                $cantidad      = (float)$item['cantidad'];
                $costoUnitario = (float)$item['costo_unitario'];
                $descuentoDet  = (float)($item['descuento'] ?? 0);
                $subtotalDet   = (float)$item['subtotal'];

                if ($productoId <= 0 || $cantidad <= 0) {
                    continue;
                }

                // Insert detalle
                $stmtDet->execute([
                    ':compra_id'      => $compraId,
                    ':producto_id'    => $productoId,
                    ':cantidad'       => $cantidad,
                    ':costo_unitario' => $costoUnitario,
                    ':descuento'      => $descuentoDet,
                    ':subtotal'       => $subtotalDet,
                ]);

                // Actualizar producto (stock y costo)
                $stmtProd->execute([
                    ':cantidad'       => $cantidad,
                    ':costo_unitario' => $costoUnitario,
                    ':producto_id'    => $productoId,
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Elimina una compra y revierte el inventario.
     * 1. Obtiene los detalles de la compra
     * 2. Revierte el inventario (resta las cantidades que se habían sumado)
     * 3. Elimina los detalles
     * 4. Elimina el encabezado
     */
    public function eliminarCompra(int $compraId): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener detalles para revertir inventario
            $detalles = $this->obtenerDetalles($compraId);

            if (empty($detalles)) {
                throw new Exception("No se encontraron detalles para la compra");
            }

            // 2. Revertir inventario (restar cantidades)
            $sqlRevertir = "UPDATE productos
                           SET stock = stock - :cantidad
                           WHERE id = :producto_id";
            $stmtRevertir = $this->db->prepare($sqlRevertir);

            foreach ($detalles as $det) {
                $stmtRevertir->execute([
                    ':cantidad' => (float)$det['cantidad'],
                    ':producto_id' => (int)$det['producto_id']
                ]);
            }

            // 3. Eliminar detalles
            $sqlDeleteDet = "DELETE FROM {$this->tableDetalle} WHERE compra_id = :compra_id";
            $stmtDeleteDet = $this->db->prepare($sqlDeleteDet);
            $stmtDeleteDet->execute([':compra_id' => $compraId]);

            // 4. Eliminar encabezado
            $sqlDeleteCompra = "DELETE FROM {$this->tableCompras} WHERE id = :id";
            $stmtDeleteCompra = $this->db->prepare($sqlDeleteCompra);
            $stmtDeleteCompra->execute([':id' => $compraId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
