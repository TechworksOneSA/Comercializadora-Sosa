<?php
// API para crear nueva categoría
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Iniciar la sesión primero
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración de environment primero
require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';

// Verificar autenticación (simplificado - solo revisar sesión)
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado - Sesión no iniciada'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Obtener y decodificar el input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    // Log para debug
    error_log("Categorias API - Input recibido: " . $rawInput);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    $nombre = trim($input['nombre'] ?? '');
    $margen_porcentaje = isset($input['margen_porcentaje']) ? (float)$input['margen_porcentaje'] : null;
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es requerido'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $db = Database::connect();
    
    // Verificar si ya existe
    $check = $db->prepare("SELECT id FROM categorias WHERE nombre = :nombre");
    $check->execute([':nombre' => $nombre]);
    if ($check->fetch()) {
        echo json_encode([
            'success' => false, 
            'message' => 'Ya existe una categoría con ese nombre'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Insertar nueva categoría
    $sql = "INSERT INTO categorias (nombre, margen_porcentaje) VALUES (:nombre, :margen)";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        ':nombre' => $nombre,
        ':margen' => $margen_porcentaje
    ]);
    
    if (!$result) {
        throw new Exception('Error al insertar en la base de datos');
    }
    
    $id = (int)$db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'categoria' => [
            'id' => $id,
            'nombre' => $nombre,
            'margen_porcentaje' => $margen_porcentaje
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    error_log("Categorias API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_UNESCAPED_UNICODE);
}
