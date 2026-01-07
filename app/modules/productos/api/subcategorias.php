<?php
// API para obtener subcategorías por categoría
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';

try {
    $db = Database::connect();

    $categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

    if ($categoria_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de categoría inválido']);
        exit;
    }

    $sql = "SELECT id, nombre, margen_porcentaje, margen_fijo
            FROM subcategorias
            WHERE categoria_id = :categoria_id
            ORDER BY nombre ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute(['categoria_id' => $categoria_id]);
    $subcategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'subcategorias' => $subcategorias
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
