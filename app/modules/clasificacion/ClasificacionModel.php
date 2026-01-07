<?php

class ClasificacionModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // ===== CATEGORÍAS =====
    public function listarCategorias() {
        $sql = "SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearCategoria($nombre, $descripcion = '') {
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);

        if ($resultado) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function categoriaExiste($nombre) {
        $sql = "SELECT COUNT(*) FROM categorias WHERE nombre = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nombre' => $nombre]);
        return $stmt->fetchColumn() > 0;
    }

    // ===== SUBCATEGORÍAS =====
    public function listarSubcategoriasPorCategoria($categoria_id) {
        $sql = "SELECT * FROM subcategorias WHERE categoria_id = :categoria_id AND activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['categoria_id' => $categoria_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearSubcategoria($categoria_id, $nombre, $descripcion = '') {
        $sql = "INSERT INTO subcategorias (categoria_id, nombre, descripcion)
                VALUES (:categoria_id, :nombre, :descripcion)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'categoria_id' => $categoria_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);
    }

    public function subcategoriaExiste($categoria_id, $nombre) {
        $sql = "SELECT COUNT(*) FROM subcategorias WHERE categoria_id = :categoria_id AND nombre = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'categoria_id' => $categoria_id,
            'nombre' => $nombre
        ]);
        return $stmt->fetchColumn() > 0;
    }

    // ===== MARCAS =====
    public function listarMarcas() {
        $sql = "SELECT * FROM marcas WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearMarca($nombre, $descripcion = '') {
        $sql = "INSERT INTO marcas (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);
    }

    public function marcaExiste($nombre) {
        $sql = "SELECT COUNT(*) FROM marcas WHERE nombre = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nombre' => $nombre]);
        return $stmt->fetchColumn() > 0;
    }
}
