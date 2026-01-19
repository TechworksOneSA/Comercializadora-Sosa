<?php

class ProductosModel extends Model
{
    private $db;
    private string $table = "productos";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getKpis()
    {
        $sql = "SELECT
                    COUNT(*) as total_productos,
                    SUM(stock * precio_venta) as valor_inventario,
                    SUM(stock * costo_actual) as costo_inversion,
                    SUM(CASE WHEN stock <= stock_minimo THEN 1 ELSE 0 END) as stock_bajo,
                    SUM(CASE WHEN estado = 'ACTIVO' THEN 1 ELSE 0 END) as productos_activos
                FROM {$this->table}";

        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar(string $q = "", array $filters = [])
    {
        $where  = [];
        $params = [];

        if ($q !== "") {
            $where[] = "(p.nombre LIKE :q1 OR p.sku LIKE :q2 OR p.codigo_barra LIKE :q3)";
            $searchTerm = "%{$q}%";
            $params["q1"] = $searchTerm;
            $params["q2"] = $searchTerm;
            $params["q3"] = $searchTerm;
        }

        $categoriaId = (int)($filters["categoria_id"] ?? 0);
        if ($categoriaId > 0) {
            $where[] = "p.categoria_id = :categoria_id";
            $params["categoria_id"] = $categoriaId;
        }

        $marcaId = (int)($filters["marca_id"] ?? 0);
        if ($marcaId > 0) {
            $where[] = "p.marca_id = :marca_id";
            $params["marca_id"] = $marcaId;
        }

        $estado = strtoupper(trim($filters["estado"] ?? "ALL"));
        if (in_array($estado, ["ACTIVO", "INACTIVO"], true)) {
            $where[] = "p.estado = :estado";
            $params["estado"] = $estado;
        }

        $stock = strtolower(trim($filters["stock"] ?? "all"));
        if ($stock === "cero") {
            $where[] = "p.stock <= 0";
        } elseif ($stock === "bajo") {
            $where[] = "p.stock > 0 AND p.stock <= p.stock_minimo";
        }

        $tipo = strtoupper(trim($filters["tipo"] ?? "ALL"));
        if ($tipo === "SERIE") {
            $where[] = "p.requiere_serie = 1";
        } elseif ($tipo === "NORMAL") {
            $where[] = "(p.requiere_serie IS NULL OR p.requiere_serie = 0)";
        }

        $sql = "SELECT p.*,
                       c.nombre as categoria_nombre,
                       m.nombre as marca_nombre,
                       DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') as fecha_registro
                FROM {$this->table} p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN marcas m ON p.marca_id = m.id";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY p.estado DESC, p.created_at DESC";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":" . $key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function skuExiste(string $sku, ?int $excluirId = null): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE sku = :sku";
        if ($excluirId) $sql .= " AND id != :id";
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":sku", $sku);
        if ($excluirId) $stmt->bindParam(":id", $excluirId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    public function codigoExiste(string $codigoBarra, ?int $ignoreId = null): bool
    {
        $sql    = "SELECT id FROM {$this->table} WHERE codigo_barra = :codigo";
        $params = [":codigo" => $codigoBarra];

        if ($ignoreId) {
            $sql .= " AND id <> :id";
            $params[":id"] = $ignoreId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function crear(array $data): bool
    {
        try {
            $imagenPath = $data["imagen_path"] ?? null;

            // Verificar si la columna numero_serie existe
            $checkColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'numero_serie'")->fetch();
            $tieneNumeroSerie = !empty($checkColumn);

            if ($tieneNumeroSerie) {
                $sql = "INSERT INTO {$this->table}
                    (sku, codigo_barra, numero_serie, nombre, tipo_producto, categoria_id, marca_id, unidad_medida_id,
                     precio_venta, costo_actual, stock, stock_minimo, descripcion, imagen_path, activo, estado)
                    VALUES
                    (:sku, :codigo_barra, :numero_serie, :nombre, :tipo_producto, :categoria_id, :marca_id, :unidad_medida_id,
                     :precio_venta, :costo_actual, :stock, :stock_minimo, :descripcion, :imagen_path, :activo, :estado)";
            } else {
                $sql = "INSERT INTO {$this->table}
                    (sku, codigo_barra, nombre, tipo_producto, categoria_id, marca_id, unidad_medida_id,
                     precio_venta, costo_actual, stock, stock_minimo, descripcion, imagen_path, activo, estado)
                    VALUES
                    (:sku, :codigo_barra, :nombre, :tipo_producto, :categoria_id, :marca_id, :unidad_medida_id,
                     :precio_venta, :costo_actual, :stock, :stock_minimo, :descripcion, :imagen_path, :activo, :estado)";
            }

            $stmt = $this->db->prepare($sql);

            $numeroSerie = $data["numero_serie"] ?? null;
            if (empty($numeroSerie)) {
                $numeroSerie = null;
            }
            
            // Debug
            require_once __DIR__ . '/../../core/Logger.php';
            Logger::log("numero_serie a guardar: " . ($numeroSerie ?? 'NULL'));
            Logger::log("Tiene columna numero_serie: " . ($tieneNumeroSerie ? 'SI' : 'NO'));
            error_log("numero_serie a guardar: " . ($numeroSerie ?? 'NULL'));
            error_log("Tiene columna numero_serie: " . ($tieneNumeroSerie ? 'SI' : 'NO'));

            $params = [
                ":sku"              => $data["sku"],
                ":codigo_barra"     => $data["codigo_barra"] ?? null,
                ":nombre"           => $data["nombre"],
                ":tipo_producto"    => $data["tipo_producto"] ?? 'UNIDAD',
                ":categoria_id"     => $data["categoria_id"],
                ":marca_id"         => $data["marca_id"],
                ":unidad_medida_id" => $data["unidad_medida_id"] ?? 1,
                ":precio_venta"     => $data["precio_venta"],
                ":costo_actual"     => $data["costo_actual"],
                ":stock"            => $data["stock"] ?? 0,
                ":stock_minimo"     => $data["stock_minimo"] ?? 0,
                ":descripcion"      => $data["descripcion"] ?? null,
                ":imagen_path"      => $imagenPath,
                ":activo"           => $data["activo"] ?? 1,
                ":estado"           => $data["estado"] ?? "ACTIVO",
            ];

            if ($tieneNumeroSerie) {
                $params[":numero_serie"] = $numeroSerie;
            }

            $result = $stmt->execute($params);

            if (!$result) {
                error_log("Error al crear producto: " . json_encode($stmt->errorInfo()));
                return false;
            }

            return true;
        } catch (PDOException $e) {
            error_log("PDOException en ProductosModel::crear: " . $e->getMessage());
            throw $e;
        }
    }

    public function actualizar(int $id, array $data): bool
    {
        $imagenPath = $data["imagen_path"] ?? null;

        // Verificar si la columna numero_serie existe
        $checkColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'numero_serie'")->fetch();
        $tieneNumeroSerie = !empty($checkColumn);

        if ($tieneNumeroSerie) {
            $sql = "UPDATE {$this->table}
                    SET sku              = :sku,
                        codigo_barra     = :codigo_barra,
                        numero_serie     = :numero_serie,
                        nombre           = :nombre,
                        tipo_producto    = :tipo_producto,
                        categoria_id     = :categoria_id,
                        marca_id         = :marca_id,
                        unidad_medida_id = :unidad_medida_id,
                        precio_venta     = :precio_venta,
                        costo_actual     = :costo_actual,
                        stock            = :stock,
                        stock_minimo     = :stock_minimo,
                        descripcion      = :descripcion,
                        estado           = :estado,
                        imagen_path      = :imagen_path,
                        updated_at       = NOW()
                    WHERE id = :id";
        } else {
            $sql = "UPDATE {$this->table}
                    SET sku              = :sku,
                        codigo_barra     = :codigo_barra,
                        nombre           = :nombre,
                        tipo_producto    = :tipo_producto,
                        categoria_id     = :categoria_id,
                        marca_id         = :marca_id,
                        unidad_medida_id = :unidad_medida_id,
                        precio_venta     = :precio_venta,
                        costo_actual     = :costo_actual,
                        stock            = :stock,
                        stock_minimo     = :stock_minimo,
                        descripcion      = :descripcion,
                        estado           = :estado,
                        imagen_path      = :imagen_path,
                        updated_at       = NOW()
                    WHERE id = :id";
        }

        $stmt = $this->db->prepare($sql);

        $numeroSerie = $data['numero_serie'] ?? null;
        if (empty($numeroSerie)) {
            $numeroSerie = null;
        }

        $params = [
            ':sku'              => $data['sku'],
            ':codigo_barra'     => $data['codigo_barra'] ?? null,
            ':nombre'           => $data['nombre'],
            ':tipo_producto'    => $data['tipo_producto'] ?? 'UNIDAD',
            ':categoria_id'     => $data['categoria_id'],
            ':marca_id'         => $data['marca_id'],
            ':unidad_medida_id' => $data['unidad_medida_id'] ?? 1,
            ':precio_venta'     => $data['precio_venta'],
            ':costo_actual'     => $data['costo_actual'],
            ':stock'            => $data['stock'],
            ':stock_minimo'     => $data['stock_minimo'],
            ':descripcion'      => $data['descripcion'] ?? null,
            ':estado'           => $data['estado'] ?? 'ACTIVO',
            ':imagen_path'      => $imagenPath,
            ':id'               => $id,
        ];

        if ($tieneNumeroSerie) {
            $params[':numero_serie'] = $numeroSerie;
        }

        return $stmt->execute($params);
    }

    public function desactivar(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET estado = 'INACTIVO', updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function activar(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET estado = 'ACTIVO', updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarActivos(): array
    {
        $sql = "SELECT id, sku, nombre, tipo_producto, precio_venta, stock, imagen_path
                FROM productos
                WHERE estado = 'ACTIVO'
                ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
