<?php

class SubcategoriasModel extends Model
{
    private $db;
    private string $table = "subcategorias";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function listarPorCategoria(int $categoria_id): array
    {
        $sql = "SELECT id, nombre, margen_porcentaje, margen_fijo 
                FROM {$this->table} 
                WHERE categoria_id = :categoria_id 
                ORDER BY nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['categoria_id' => $categoria_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO {$this->table} 
                (categoria_id, nombre, margen_porcentaje, margen_fijo) 
                VALUES (:categoria_id, :nombre, :margen_porcentaje, :margen_fijo)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'categoria_id' => $data['categoria_id'],
            'nombre' => $data['nombre'],
            'margen_porcentaje' => $data['margen_porcentaje'] ?? null,
            'margen_fijo' => $data['margen_fijo'] ?? null
        ]);
        
        return (int)$this->db->lastInsertId();
    }
}
