<?php

class ClientesModel extends Model
{
    private PDO $db;
    private string $table = "clientes";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Listar todos los clientes
     */
    public function listar(): array
    {
        $sql = "SELECT
                    id,
                    nombre,
                    apellido,
                    telefono,
                    direccion,
                    preferencia_metodo_pago,
                    nit,
                    COALESCE(total_gastado, 0.00) as total_gastado,
                    created_at
                FROM {$this->table}
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar clientes por nombre, apellido o teléfono (para AJAX)
     */
    public function buscar(string $q): array
    {
        $q = trim($q);
        if ($q === "") {
            return [];
        }

        $sql = "SELECT
                    id,
                    nombre,
                    apellido,
                    telefono,
                    direccion,
                    preferencia_metodo_pago,
                    nit
                FROM {$this->table}
                WHERE nombre LIKE :q
                   OR apellido LIKE :q2
                   OR telefono LIKE :q3
                ORDER BY nombre ASC
                LIMIT 10";

        $stmt = $this->db->prepare($sql);
        $like = "%{$q}%";
        $stmt->bindValue(":q",  $like, PDO::PARAM_STR);
        $stmt->bindValue(":q2", $like, PDO::PARAM_STR);
        $stmt->bindValue(":q3", $like, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Crear nuevo cliente
     * NOTA: total_gastado se inicializa en 0.00 automáticamente.
     * En el futuro, este campo se actualizará desde ventas/pagos con:
     * UPDATE clientes SET total_gastado = (SELECT SUM(total) FROM ventas WHERE cliente_id = ?)
     */
    public function crear(array $data): int
    {
        $sql = "INSERT INTO {$this->table}
                (nombre, apellido, telefono, direccion, preferencia_metodo_pago, nit, total_gastado, created_at)
                VALUES (:nombre, :apellido, :telefono, :direccion, :preferencia_metodo_pago, :nit, :total_gastado, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":nombre"       => $data["nombre"],
            ":apellido"     => $data["apellido"],
            ":telefono"     => $data["telefono"],
            ":direccion"    => $data["direccion"] ?? "",
            ":preferencia_metodo_pago"  => $data["metodo_pago"],
            ":nit"          => $data["nit"] ?? "",
            ":total_gastado" => $data["total_gastado"] ?? 0.00,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar cliente (preparado para futuro)
     */
    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET nombre = :nombre,
                    apellido = :apellido,
                    telefono = :telefono,
                    direccion = :direccion,
                    preferencia_metodo_pago = :preferencia_metodo_pago,
                    nit = :nit,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":id"          => $id,
            ":nombre"      => $data["nombre"],
            ":apellido"    => $data["apellido"],
            ":telefono"    => $data["telefono"],
            ":direccion"   => $data["direccion"] ?? "",
            ":preferencia_metodo_pago" => $data["metodo_pago"],
            ":nit"         => $data["nit"] ?? "",
        ]);
    }

    /**
     * Obtener estadísticas generales
     */
    public function obtenerEstadisticas(): array
    {
        $sql = "SELECT
                    COUNT(*) as total_clientes,
                    COALESCE(SUM(total_gastado), 0.00) as total_gastado_global
                FROM {$this->table}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

/*
 * =====================================================
 * SQL CREATE TABLE para referencia (ejecutar en phpMyAdmin)
 * =====================================================
 *
 * CREATE TABLE clientes (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     nombre VARCHAR(100) NOT NULL,
 *     apellido VARCHAR(100) NOT NULL,
 *     telefono VARCHAR(30) NOT NULL,
 *     direccion VARCHAR(200) NULL,
 *     metodo_pago VARCHAR(50) NOT NULL COMMENT 'Ej: Efectivo, Transferencia, Tarjeta, Crédito',
 *     nit VARCHAR(30) NULL,
 *     total_gastado DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Se actualizará desde ventas/pagos',
 *     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *     updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
 *     INDEX idx_nombre (nombre),
 *     INDEX idx_telefono (telefono)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * =====================================================
 */
