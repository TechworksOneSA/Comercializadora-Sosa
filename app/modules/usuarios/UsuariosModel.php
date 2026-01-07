<?php

class UsuariosModel extends Model {

    /**
     * Listar todos los usuarios
     */
    public function listar() {
        $stmt = $this->pdo->query(
            "SELECT id, nombre, email, rol, activo, created_at
             FROM usuarios
             ORDER BY created_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un usuario por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, email, rol, activo, created_at
             FROM usuarios
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si un email ya existe
     */
    public function emailExiste($email, $exceptoId = null) {
        if ($exceptoId) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ?"
            );
            $stmt->execute([$email, $exceptoId]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM usuarios WHERE email = ?"
            );
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear un nuevo usuario
     */
    public function crear($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuarios (nombre, email, password_hash, rol, activo)
             VALUES (:nombre, :email, :password_hash, :rol, :activo)"
        );

        return $stmt->execute([
            ':nombre'   => $data['nombre'],
            ':email'    => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':rol'      => $data['rol'],
            ':activo'   => $data['activo'] ?? 1
        ]);
    }

    /**
     * Actualizar un usuario existente
     */
    public function actualizar($id, $data) {
        if (!empty($data['password'])) {
            // Si se proporciona nueva contraseña
            $stmt = $this->pdo->prepare(
                "UPDATE usuarios
                 SET nombre = :nombre,
                     email = :email,
                     password_hash = :password_hash,
                     rol = :rol,
                     activo = :activo
                 WHERE id = :id"
            );

            return $stmt->execute([
                ':id'       => $id,
                ':nombre'   => $data['nombre'],
                ':email'    => $data['email'],
                ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':rol'      => $data['rol'],
                ':activo'   => $data['activo']
            ]);
        } else {
            // Si NO se cambia la contraseña
            $stmt = $this->pdo->prepare(
                "UPDATE usuarios
                 SET nombre = :nombre,
                     email = :email,
                     rol = :rol,
                     activo = :activo
                 WHERE id = :id"
            );

            return $stmt->execute([
                ':id'       => $id,
                ':nombre'   => $data['nombre'],
                ':email'    => $data['email'],
                ':rol'      => $data['rol'],
                ':activo'   => $data['activo']
            ]);
        }
    }

    /**
     * Cambiar estado (activo/inactivo)
     */
    public function cambiarEstado($id, $activo) {
        $stmt = $this->pdo->prepare(
            "UPDATE usuarios SET activo = :activo WHERE id = :id"
        );
        return $stmt->execute([
            ':id'     => $id,
            ':activo' => $activo
        ]);
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public function obtenerEstadisticas() {
        $stmt = $this->pdo->query(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as inactivos,
                SUM(CASE WHEN rol = 'ADMIN' THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN rol = 'VENDEDOR' THEN 1 ELSE 0 END) as vendedores
             FROM usuarios"
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
