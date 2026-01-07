<?php

class InventarioAvanzadoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Lista productos con stock (desde tabla productos),
     * aplicando filtros básicos.
     */
    public function listarProductosConStock(array $filtros = []): array
    {
        $q = trim($filtros['q'] ?? '');
        $categoriaId = (int)($filtros['categoria_id'] ?? 0);
        $marcaId = (int)($filtros['marca_id'] ?? 0);
        $stockEstado = $filtros['stock'] ?? 'ALL'; // ALL | BAJO | AGOTADO

        $where = [];
        $params = [];

        // Solo productos activos si su sistema lo maneja
        // (si no existe la columna activo/estado, lo quitamos luego)
        // $where[] = "p.activo = 1";

        if ($q !== '') {
            $where[] = "(p.nombre LIKE :q OR p.sku LIKE :q OR p.codigo_barra LIKE :q)";
            $params[':q'] = "%{$q}%";
        }
        if ($categoriaId > 0) {
            $where[] = "p.categoria_id = :cat";
            $params[':cat'] = $categoriaId;
        }
        if ($marcaId > 0) {
            $where[] = "p.marca_id = :marca";
            $params[':marca'] = $marcaId;
        }

        // Filtro por estado de stock
        if ($stockEstado === 'AGOTADO') {
            $where[] = "p.stock <= 0";
        } elseif ($stockEstado === 'BAJO') {
            // bajo = stock <= stock_minimo (si existe stock_minimo)
            $where[] = "(p.stock_minimo IS NOT NULL AND p.stock <= p.stock_minimo AND p.stock > 0)";
        }

        $sql = "
            SELECT
                p.id,
                p.sku,
                p.codigo_barra,
                p.nombre,
                p.stock,
                p.stock_minimo,
                p.costo_actual,
                p.precio_venta,
                p.categoria_id,
                p.marca_id
            FROM productos p
        ";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY p.nombre ASC LIMIT 500";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * KPIs básicos para inventario
     */
    public function kpisInventario(array $filtros = []): array
    {
        // Para KPI aplicamos mismos filtros (sin límite).
        $q = trim($filtros['q'] ?? '');
        $categoriaId = (int)($filtros['categoria_id'] ?? 0);
        $marcaId = (int)($filtros['marca_id'] ?? 0);

        $where = [];
        $params = [];

        if ($q !== '') {
            $where[] = "(p.nombre LIKE :q OR p.sku LIKE :q OR p.codigo_barra LIKE :q)";
            $params[':q'] = "%{$q}%";
        }
        if ($categoriaId > 0) {
            $where[] = "p.categoria_id = :cat";
            $params[':cat'] = $categoriaId;
        }
        if ($marcaId > 0) {
            $where[] = "p.marca_id = :marca";
            $params[':marca'] = $marcaId;
        }

        $whereSql = !empty($where) ? ("WHERE " . implode(" AND ", $where)) : "";

        $sql = "
            SELECT
                COUNT(*) AS total_productos,
                SUM(CASE WHEN p.stock <= 0 THEN 1 ELSE 0 END) AS agotados,
                SUM(CASE WHEN (p.stock_minimo IS NOT NULL AND p.stock > 0 AND p.stock <= p.stock_minimo) THEN 1 ELSE 0 END) AS bajos,
                SUM(p.stock * p.costo_actual) AS valor_costo,
                SUM(p.stock * p.precio_venta) AS valor_venta
            FROM productos p
            {$whereSql}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_productos' => (int)($row['total_productos'] ?? 0),
            'agotados'        => (int)($row['agotados'] ?? 0),
            'bajos'           => (int)($row['bajos'] ?? 0),
            'valor_costo'     => (float)($row['valor_costo'] ?? 0),
            'valor_venta'     => (float)($row['valor_venta'] ?? 0),
        ];
    }

    /**
     * Kardex por producto desde movimientos_inventario
     */
    public function kardex(int $productoId, ?string $desde = null, ?string $hasta = null, int $limit = 200): array
    {
        $limit = max(1, min($limit, 1000));

        $where = ["m.producto_id = :pid"];
        $params = [":pid" => $productoId];

        if (!empty($desde)) {
            $where[] = "DATE(m.created_at) >= :desde";
            $params[":desde"] = $desde;
        }
        if (!empty($hasta)) {
            $where[] = "DATE(m.created_at) <= :hasta";
            $params[":hasta"] = $hasta;
        }

        $sql = "
            SELECT
                m.id,
                m.tipo,
                m.cantidad,
                m.costo_unitario,
                m.origen,
                m.origen_id,
                m.es_reverso,
                m.movimiento_ref_id,
                m.motivo,
                m.created_at
            FROM movimientos_inventario m
            WHERE " . implode(" AND ", $where) . "
            ORDER BY m.created_at DESC
            LIMIT {$limit}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener stock actual de un producto
     */
    public function obtenerStockActual(int $productoId): float
    {
        $sql = "SELECT stock FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $productoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($row['stock'] ?? 0);
    }

    /**
     * Calcular totales del kardex (entradas, salidas, saldo)
     */
    public function calcularTotalesKardex(int $productoId, ?string $desde = null, ?string $hasta = null): array
    {
        $where = ["producto_id = :pid"];
        $params = [":pid" => $productoId];

        if (!empty($desde)) {
            $where[] = "DATE(created_at) >= :desde";
            $params[":desde"] = $desde;
        }
        if (!empty($hasta)) {
            $where[] = "DATE(created_at) <= :hasta";
            $params[":hasta"] = $hasta;
        }

        $sql = "
            SELECT
                SUM(CASE WHEN tipo = 'ENTRADA' THEN cantidad ELSE 0 END) as entradas,
                SUM(CASE WHEN tipo = 'SALIDA' THEN cantidad ELSE 0 END) as salidas,
                SUM(CASE
                    WHEN tipo = 'ENTRADA' THEN cantidad
                    WHEN tipo = 'SALIDA' THEN -cantidad
                    ELSE cantidad
                END) as saldo_rango
            FROM movimientos_inventario
            WHERE " . implode(" AND ", $where);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'entradas' => (float)($row['entradas'] ?? 0),
            'salidas' => (float)($row['salidas'] ?? 0),
            'saldo_rango' => (float)($row['saldo_rango'] ?? 0),
        ];
    }
}
