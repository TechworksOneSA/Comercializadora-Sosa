<?php

class ReportesModel extends Model
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /* ==================== RESUMEN GENERAL ==================== */

    /**
     * Resumen global de ventas (todas las fechas).
     * Nota: Si existe el campo v.estado, excluye ANULADA para no inflar métricas.
     */
    public function obtenerResumenVentas(): array
    {
        // Intento "pro" (con estado), y si falla por columna inexistente, cae al query simple.
        try {
            $sql = "SELECT
                        COUNT(*) AS total_ventas,
                        COALESCE(SUM(total), 0) AS monto_total,
                        COALESCE(AVG(total), 0) AS promedio_venta
                    FROM venta
                    WHERE (estado IS NULL OR estado != 'ANULADA')";
            return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            $sql = "SELECT
                        COUNT(*) AS total_ventas,
                        COALESCE(SUM(total), 0) AS monto_total,
                        COALESCE(AVG(total), 0) AS promedio_venta
                    FROM venta";
            return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];
        }
    }

    /**
     * ✅ MÉTODO QUE LE FALTABA (lo pide el Controller):
     * Resumen de ventas por período.
     */
    public function obtenerResumenVentasPeriodo(string $inicio, string $fin): array
    {
        // Igual: intento con estado y si no existe, cae al simple.
        try {
            $sql = "SELECT
                        COUNT(*) AS total_ventas,
                        COALESCE(SUM(v.total), 0) AS monto_total,
                        COALESCE(AVG(v.total), 0) AS promedio_venta
                    FROM venta v
                    WHERE DATE(v.fecha_venta) BETWEEN :i AND :f
                      AND (v.estado IS NULL OR v.estado != 'ANULADA')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':i' => $inicio, ':f' => $fin]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            $sql = "SELECT
                        COUNT(*) AS total_ventas,
                        COALESCE(SUM(v.total), 0) AS monto_total,
                        COALESCE(AVG(v.total), 0) AS promedio_venta
                    FROM venta v
                    WHERE DATE(v.fecha_venta) BETWEEN :i AND :f";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':i' => $inicio, ':f' => $fin]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
    }

    public function obtenerResumenCompras(): array
    {
        $sql = "SELECT
                    COUNT(*) AS total_compras,
                    COALESCE(SUM(total), 0) AS monto_total
                FROM compras";

        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /* ==================== REPORTES DE COMPRAS ==================== */

    public function obtenerReporteCompras(string $inicio, string $fin): array
    {
        $sql = "SELECT
                    c.id,
                    c.fecha,
                    c.serie_factura,
                    c.numero_factura,
                    c.subtotal,
                    c.iva,
                    c.total,
                    c.estado,
                    p.nombre AS proveedor_nombre,
                    p.nit AS proveedor_nit,
                    u.nombre AS usuario_nombre
                FROM compras c
                LEFT JOIN proveedores p ON p.id = c.proveedor_id
                LEFT JOIN usuarios u ON u.id = c.usuario_id
                WHERE DATE(c.fecha) BETWEEN :i AND :f
                ORDER BY c.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':i' => $inicio, ':f' => $fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenComprasPeriodo(string $inicio, string $fin): array
    {
        $sql = "SELECT
                    COUNT(*) AS total_compras,
                    COALESCE(SUM(c.total), 0) AS monto_total,
                    COALESCE(AVG(c.total), 0) AS promedio_compra,
                    COALESCE(SUM(c.subtotal), 0) AS subtotal_total,
                    COALESCE(SUM(c.iva), 0) AS iva_total
                FROM compras c
                WHERE DATE(c.fecha) BETWEEN :i AND :f
                  AND c.estado = 'REGISTRADA'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':i' => $inicio, ':f' => $fin]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_compras' => 0,
            'monto_total' => 0,
            'promedio_compra' => 0,
            'subtotal_total' => 0,
            'iva_total' => 0
        ];
    }

    public function obtenerResumenInventario(): array
    {
        $sql = "SELECT
                    COUNT(*) AS total_productos,
                    COALESCE(SUM(stock), 0) AS total_stock,
                    COALESCE(SUM(stock * costo_actual), 0) AS valor_inventario,
                    SUM(stock <= stock_minimo) AS productos_bajo_stock,
                    SUM(stock = 0) AS productos_sin_stock
                FROM productos
                WHERE activo = 1";

        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /* ==================== REPORTES DE VENTAS ==================== */

    public function obtenerReporteVentas(string $inicio, string $fin): array
    {
        $sql = "SELECT
                    v.id,
                    v.fecha_venta AS fecha,
                    v.subtotal,
                    v.total,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente_nombre,
                    c.nit AS cliente_nit,
                    u.nombre AS vendedor
                FROM venta v
                LEFT JOIN clientes c ON c.id = v.cliente_id
                LEFT JOIN usuarios u ON u.id = v.usuario_id
                WHERE DATE(v.fecha_venta) BETWEEN :i AND :f
                ORDER BY v.fecha_venta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':i' => $inicio, ':f' => $fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasPorDia(string $inicio, string $fin): array
    {
        $sql = "SELECT
                    DATE(fecha_venta) AS fecha,
                    COUNT(*) AS cantidad_ventas,
                    SUM(total) AS total_ventas
                FROM venta
                WHERE DATE(fecha_venta) BETWEEN :i AND :f
                GROUP BY DATE(fecha_venta)
                ORDER BY fecha";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':i' => $inicio, ':f' => $fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================== REPORTES DE INVENTARIO ==================== */

    public function obtenerReporteInventario(string $filtro = 'todos'): array
    {
        $whereConditions = ["p.activo = 1"];

        // Aplicar filtros específicos
        if ($filtro == 'bajo_stock') {
            $whereConditions[] = "p.stock <= p.stock_minimo AND p.stock > 0";
        } elseif ($filtro == 'sin_stock') {
            $whereConditions[] = "p.stock = 0";
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT
                    p.id,
                    p.nombre,
                    p.sku,
                    p.stock,
                    p.stock_minimo,
                    p.costo_actual,
                    p.precio_venta,
                    (p.stock * p.costo_actual) AS valor,
                    cat.nombre AS categoria,
                    m.nombre AS marca
                FROM productos p
                LEFT JOIN categorias cat ON cat.id = p.categoria_id
                LEFT JOIN marcas m ON m.id = p.marca_id
                WHERE $whereClause
                ORDER BY
                    CASE
                        WHEN p.stock = 0 THEN 1
                        WHEN p.stock <= p.stock_minimo THEN 2
                        ELSE 3
                    END,
                    p.nombre";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================== PRODUCTOS MÁS / MENOS VENDIDOS ==================== */

    public function obtenerProductosMasVendidos(int $limite = 10): array
    {
        $sql = "SELECT
                    p.id,
                    p.nombre,
                    p.sku,
                    p.stock,
                    p.precio_venta,
                    COALESCE(SUM(vd.cantidad),0) AS total_vendido,
                    COALESCE(SUM(vd.cantidad * vd.precio_unitario),0) AS ingresos
                FROM productos p
                LEFT JOIN venta_detalle vd ON vd.producto_id = p.id
                LEFT JOIN venta v ON v.id = vd.venta_id AND v.estado != 'ANULADA'
                WHERE p.activo = 1
                GROUP BY p.id, p.nombre, p.sku, p.stock, p.precio_venta
                ORDER BY total_vendido DESC
                LIMIT :l";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':l', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMenosVendidos(int $limite = 10): array
    {
        $sql = "SELECT
                    p.id,
                    p.nombre,
                    p.sku,
                    p.stock,
                    p.precio_venta,
                    COALESCE(SUM(vd.cantidad),0) AS total_vendido
                FROM productos p
                LEFT JOIN venta_detalle vd ON vd.producto_id = p.id
                LEFT JOIN venta v ON v.id = vd.venta_id AND v.estado != 'ANULADA'
                WHERE p.activo = 1
                GROUP BY p.id, p.nombre, p.sku, p.stock, p.precio_venta
                ORDER BY total_vendido ASC
                LIMIT :l";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':l', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================== BALANCE ==================== */

    public function obtenerBalanceFinanciero(string $inicio, string $fin): array
    {
        $ing = $this->db->prepare(
            "SELECT SUM(total) total, COUNT(*) c
             FROM venta
             WHERE DATE(fecha_venta) BETWEEN :i AND :f"
        );
        $ing->execute([':i' => $inicio, ':f' => $fin]);
        $i = $ing->fetch(PDO::FETCH_ASSOC);

        $egr = $this->db->prepare(
            "SELECT SUM(total) total, COUNT(*) c
             FROM compras
             WHERE DATE(fecha) BETWEEN :i AND :f"
        );
        $egr->execute([':i' => $inicio, ':f' => $fin]);
        $e = $egr->fetch(PDO::FETCH_ASSOC);

        return [
            'ingresos' => (float)($i['total'] ?? 0),
            'egresos'  => (float)($e['total'] ?? 0),
            'balance'  => (float)($i['total'] ?? 0) - (float)($e['total'] ?? 0),
            'ventas'   => (int)($i['c'] ?? 0),
            'compras'  => (int)($e['c'] ?? 0),
        ];
    }

    public function obtenerBalancePorDia(string $inicio, string $fin): array
    {
        $sql = "SELECT
                    fecha,
                    SUM(ingresos) AS ingresos,
                    SUM(egresos) AS egresos,
                    SUM(ingresos) - SUM(egresos) AS balance
                FROM (
                    SELECT
                        DATE(fecha_venta) AS fecha,
                        SUM(total) AS ingresos,
                        0 AS egresos
                    FROM venta
                    WHERE DATE(fecha_venta) BETWEEN :i AND :f
                    GROUP BY DATE(fecha_venta)

                    UNION ALL

                    SELECT
                        DATE(fecha) AS fecha,
                        0 AS ingresos,
                        SUM(total) AS egresos
                    FROM compras
                    WHERE DATE(fecha) BETWEEN :i2 AND :f2
                    GROUP BY DATE(fecha)
                ) AS balance_diario
                GROUP BY fecha
                ORDER BY fecha";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':i' => $inicio,
            ':f' => $fin,
            ':i2' => $inicio,
            ':f2' => $fin
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================== DETALLES ==================== */

    public function obtenerDetalleVentas(string $inicio, string $fin): array
    {
        $sql = "SELECT
                    v.id,
                    v.fecha_venta,
                    v.total,
                    c.nombre AS cliente,
                    u.nombre AS vendedor,
                    COUNT(vd.id) AS productos
                FROM venta v
                LEFT JOIN clientes c ON c.id = v.cliente_id
                LEFT JOIN usuarios u ON u.id = v.usuario_id
                LEFT JOIN venta_detalle vd ON vd.venta_id = v.id
                WHERE DATE(v.fecha_venta) BETWEEN :i AND :f
                GROUP BY v.id, v.fecha_venta, v.total, c.nombre, u.nombre
                ORDER BY v.fecha_venta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':i' => $inicio, ':f' => $fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleCompras(string $inicio, string $fin): array
    {
        $sql = "SELECT
                    c.id,
                    c.fecha,
                    c.total,
                    p.nombre AS proveedor,
                    COUNT(cd.id) AS productos
                FROM compras c
                LEFT JOIN proveedores p ON p.id = c.proveedor_id
                LEFT JOIN compras_detalle cd ON cd.compra_id = c.id
                WHERE DATE(c.fecha) BETWEEN :i AND :f
                GROUP BY c.id, c.fecha, c.total, p.nombre
                ORDER BY c.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':i' => $inicio, ':f' => $fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
