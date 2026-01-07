<?php

class CategoriasModel extends Model
{
    private PDO $db;
    private string $table = 'categorias';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function listarTodos(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarActivas(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre): int
    {
        $sql  = "INSERT INTO {$this->table} (nombre, activo) VALUES (:nombre, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);

        return (int)$this->db->lastInsertId();
    }

    public function cambiarEstado(int $id, int $activo): bool
    {
        $sql  = "UPDATE {$this->table} SET activo = :activo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':activo' => $activo,
            ':id'     => $id,
        ]);
    }
}
