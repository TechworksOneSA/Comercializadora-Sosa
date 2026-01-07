<?php

class ProveedoresModel extends Model
{
    private $db;
    private string $table = 'proveedores';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Listar solo proveedores activos (para selects, combos, etc.)
     */
    public function listarActivos(): array
    {
        $sql = "SELECT id, nit, nombre
                FROM {$this->table}
                WHERE activo = 1
                ORDER BY nombre ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todos los proveedores (para el módulo de administración)
     */
    public function listarTodos(): array
    {
        $sql = "SELECT *
                FROM {$this->table}
                ORDER BY id DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear proveedor
     */
    public function crear(array $data): int
    {
        $sql = "INSERT INTO {$this->table}
                (nit, nombre, direccion, telefono, correo, activo)
                VALUES
                (:nit, :nombre, :direccion, :telefono, :correo, :activo)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nit'       => $data['nit'],
            ':nombre'    => $data['nombre'],
            ':direccion' => $data['direccion'],
            ':telefono'  => $data['telefono'],
            ':correo'    => $data['correo'],
            ':activo'    => $data['activo'] ?? 1,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function cambiarEstado(int $id, int $activo): bool
    {
        $sql = "UPDATE {$this->table}
                SET activo = :activo
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':activo' => $activo,
            ':id'     => $id,
        ]);
    }

    /**
     * Obtener un proveedor por id (para conocer estado actual)
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Verificar si un NIT ya existe en la base de datos
     */
    public function existeNit(string $nit): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nit = :nit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nit' => $nit]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['total'] ?? 0) > 0;
    }

    /**
     * Verificar si un nombre ya existe en la base de datos
     */
    public function existeNombre(string $nombre): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nombre = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['total'] ?? 0) > 0;
    }

    /**
     * Verificar si un NIT ya existe excluyendo un ID específico (para edición)
     */
    public function existeNitExcluyendoId(string $nit, int $idExcluir): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nit = :nit AND id != :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nit' => $nit, ':id' => $idExcluir]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['total'] ?? 0) > 0;
    }

    /**
     * Verificar si un nombre ya existe excluyendo un ID específico (para edición)
     */
    public function existeNombreExcluyendoId(string $nombre, int $idExcluir): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nombre = :nombre AND id != :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre, ':id' => $idExcluir]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['total'] ?? 0) > 0;
    }

    /**
     * Actualizar proveedor
     */
    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET nit = :nit,
                    nombre = :nombre,
                    direccion = :direccion,
                    telefono = :telefono,
                    correo = :correo,
                    activo = :activo
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nit'       => $data['nit'],
            ':nombre'    => $data['nombre'],
            ':direccion' => $data['direccion'],
            ':telefono'  => $data['telefono'],
            ':correo'    => $data['correo'],
            ':activo'    => $data['activo'] ?? 1,
            ':id'        => $id,
        ]);
    }

    /**
     * Eliminar proveedor
     */
    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
