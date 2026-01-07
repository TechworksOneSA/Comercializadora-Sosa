<?php
// Script temporal para resetear contraseña del administrador

require_once __DIR__ . "/app/config/env.php";
require_once __DIR__ . "/app/config/database.php";

$pdo = Database::connect();

// Nueva contraseña
$newPassword = 'Admin123*';
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Actualizar contraseña del admin
$stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE email = ?");
$result = $stmt->execute([$hashedPassword, 'admin@comercializadora.com']);

if ($result) {
    echo "✅ Contraseña actualizada exitosamente!\n";
    echo "Email: admin@comercializadora.com\n";
    echo "Password: Admin123*\n";
} else {
    echo "❌ Error al actualizar la contraseña\n";
}
