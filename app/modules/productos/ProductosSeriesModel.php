<?php
require_once __DIR__ . '/../../core/Model.php';

class ProductosSeriesModel extends Model {
    
    /**
     * Guardar número de serie de un producto
     */
    public function guardarSerie($data) {
        $sql = "INSERT INTO productos_series 
                (producto_id, numero_serie, compra_id, observaciones, estado) 
                VALUES (:producto_id, :numero_serie, :compra_id, :observaciones, :estado)";
        
        $params = [
            'producto_id' => $data['producto_id'],
            'numero_serie' => $data['numero_serie'],
            'compra_id' => $data['compra_id'] ?? null,
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => $data['estado'] ?? 'EN_STOCK'
        ];
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener series de un producto
     */
    public function getSeriesByProducto($producto_id, $estado = null) {
        $sql = "SELECT * FROM productos_series WHERE producto_id = :producto_id";
        
        if ($estado) {
            $sql .= " AND estado = :estado";
        }
        
        $sql .= " ORDER BY fecha_ingreso DESC";
        
        $params = ['producto_id' => $producto_id];
        if ($estado) {
            $params['estado'] = $estado;
        }
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtener series en stock de un producto
     */
    public function getSeriesEnStock($producto_id) {
        return $this->getSeriesByProducto($producto_id, 'EN_STOCK');
    }
    
    /**
     * Verificar si un número de serie ya existe
     */
    public function serieExiste($numero_serie) {
        $sql = "SELECT COUNT(*) as count FROM productos_series WHERE numero_serie = :numero_serie";
        $result = $this->query($sql, ['numero_serie' => $numero_serie]);
        return $result[0]['count'] > 0;
    }
    
    /**
     * Marcar serie como vendida
     */
    public function marcarComoVendida($numero_serie, $venta_id) {
        $sql = "UPDATE productos_series 
                SET estado = 'VENDIDO', venta_id = :venta_id, fecha_venta = NOW()
                WHERE numero_serie = :numero_serie AND estado = 'EN_STOCK'";
        
        return $this->query($sql, [
            'numero_serie' => $numero_serie,
            'venta_id' => $venta_id
        ]);
    }
    
    /**
     * Obtener serie por número
     */
    public function getSerieByNumero($numero_serie) {
        $sql = "SELECT ps.*, p.nombre as producto_nombre, p.sku 
                FROM productos_series ps
                INNER JOIN productos p ON ps.producto_id = p.id
                WHERE ps.numero_serie = :numero_serie";
        
        $result = $this->query($sql, ['numero_serie' => $numero_serie]);
        return $result[0] ?? null;
    }
    
    /**
     * Contar series en stock de un producto
     */
    public function contarSeriesEnStock($producto_id) {
        $sql = "SELECT COUNT(*) as count FROM productos_series 
                WHERE producto_id = :producto_id AND estado = 'EN_STOCK'";
        
        $result = $this->query($sql, ['producto_id' => $producto_id]);
        return (int)$result[0]['count'];
    }
    
    /**
     * Eliminar serie
     */
    public function eliminarSerie($id) {
        $sql = "DELETE FROM productos_series WHERE id = :id";
        return $this->query($sql, ['id' => $id]);
    }
}
