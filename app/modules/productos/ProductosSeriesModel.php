<?php
require_once __DIR__ . '/../../core/Model.php';

class ProductosSeriesModel extends Model
{

    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Guardar número de serie de un producto
     */
    public function guardarSerie($data)
    {
        $sql = "INSERT INTO productos_series
                (producto_id, numero_serie, compra_id, observaciones, estado)
                VALUES (:producto_id, :numero_serie, :compra_id, :observaciones, :estado)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':producto_id' => $data['producto_id'],
            ':numero_serie' => $data['numero_serie'],
            ':compra_id' => $data['compra_id'] ?? null,
            ':observaciones' => $data['observaciones'] ?? null,
            ':estado' => $data['estado'] ?? 'EN_STOCK'
        ]);
    }

    /**
     * Obtener series de un producto
     */
    public function getSeriesByProducto($producto_id, $estado = null)
    {
        $sql = "SELECT * FROM productos_series WHERE producto_id = :producto_id";

        $params = [':producto_id' => $producto_id];

        if ($estado) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener series en stock de un producto
     */
    public function getSeriesEnStock($producto_id)
    {
        return $this->getSeriesByProducto($producto_id, 'EN_STOCK');
    }

    /**
     * Verificar si un número de serie ya existe
     */
    public function serieExiste($numero_serie)
    {
        $sql = "SELECT COUNT(*) as count FROM productos_series WHERE numero_serie = :numero_serie";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero_serie' => $numero_serie]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Marcar serie como vendida
     */
    public function marcarComoVendida($numero_serie, $venta_id)
    {
        $sql = "UPDATE productos_series
                SET estado = 'VENDIDO', venta_id = :venta_id, fecha_venta = NOW()
                WHERE numero_serie = :numero_serie AND estado = 'EN_STOCK'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':numero_serie' => $numero_serie,
            ':venta_id' => $venta_id
        ]);
    }

    /**
     * Obtener serie por número
     */
    public function getSerieByNumero($numero_serie)
    {
        $sql = "SELECT ps.*, p.nombre as producto_nombre, p.sku
                FROM productos_series ps
                INNER JOIN productos p ON ps.producto_id = p.id
                WHERE ps.numero_serie = :numero_serie";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero_serie' => $numero_serie]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Contar series en stock de un producto
     */
    public function contarSeriesEnStock($producto_id)
    {
        $sql = "SELECT COUNT(*) as count FROM productos_series
                WHERE producto_id = :producto_id AND estado = 'EN_STOCK'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':producto_id' => $producto_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Eliminar serie
     */
    public function eliminarSerie($id)
    {
        $sql = "DELETE FROM productos_series WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
