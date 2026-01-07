<?php
// API para crear nueva subcategoría
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';

// Verificar autenticación
session_start();
if (!Auth::check()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    $db = Database::connect();
    
    $input = json_decode(file_get_contents('php://input'), true);
    $categoria_id = (int)($input['categoria_id'] ?? 0);
    $nombre = trim($input['nombre'] ?? '');
    $margen_porcentaje = isset($input['margen_porcentaje']) ? (float)$input['margen_porcentaje'] : null;
    
    if ($categoria_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Categoría inválida']);
        exit;
    }
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
        exit;
    }
    
    // Verificar si ya existe
    $check = $db->prepare("SELECT id FROM subcategorias WHERE categoria_id = ? AND nombre = ?");
    $check->execute([$categoria_id, $nombre]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya existe una subcategoría con ese nombre']);
        exit;
    }
    
    $sql = "INSERT INTO subcategorias (categoria_id, nombre, margen_porcentaje) 
            VALUES (:categoria_id, :nombre, :margen)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'categoria_id' => $categoria_id,
        'nombre' => $nombre,
        'margen' => $margen_porcentaje
    ]);
    
    $id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'subcategoria' => [
            'id' => $id,
            'categoria_id' => $categoria_id,
            'nombre' => $nombre,
            'margen_porcentaje' => $margen_porcentaje
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
