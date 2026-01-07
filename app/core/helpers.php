<?php

/**
 * Funciones Helper Globales
 * =========================
 * Funciones de utilidad para usar en toda la aplicación
 *
 * Objetivo: funcionar igual en Docker (APP_URL = '' o '/')
 * y en XAMPP subcarpeta (APP_URL = '/Comercializadora/ferreteria-pos/public')
 */

/**
 * Normaliza la base APP_URL (sin slash final, excepto si es solo "/")
 */
function app_base(): string
{
    if (!defined('APP_URL')) return '';
    $base = trim(APP_URL);

    // Si base es "/" lo tratamos como vacío para evitar "//ruta"
    if ($base === '/') return '';

    // Quitar slash final si existe
    return rtrim($base, '/');
}

/**
 * Normaliza una ruta para que comience con "/" (si no está vacía)
 */
function norm_path(string $path): string
{
    $path = trim($path);
    if ($path === '') return '';
    return '/' . ltrim($path, '/');
}

/**
 * Genera una URL completa con el prefijo APP_URL
 *
 * @param string $path Ruta relativa (ej: 'admin/dashboard' o '/admin/dashboard')
 * @return string URL completa (ej: '/admin/dashboard' o '/ferreteria-pos/public/admin/dashboard')
 */
function url($path = ''): string
{
    $base = app_base();
    $path = norm_path((string)$path);

    // Si path vacío, devolvemos base (o '/' si base vacío? -> preferible base vacío)
    if ($path === '') return ($base === '' ? '/' : $base . '/');

    return $base . $path;
}

/**
 * Redirige a una URL (respetando APP_URL)
 *
 * @param string $path Ruta a la que redirigir (ej: '/login')
 * @param int $statusCode Código HTTP de redirección (default: 302)
 */
function redirect($path, $statusCode = 302): void
{
    header('Location: ' . url($path), true, $statusCode);
    exit;
}

/**
 * Genera una URL de asset (CSS, JS, imágenes)
 *
 * @param string $path Ruta del asset (ej: 'css/app.css' o '/css/app.css')
 * @return string URL completa del asset (ej: '/assets/css/app.css' o '/base/assets/css/app.css')
 */
function asset($path): string
{
    $base = app_base();
    $path = norm_path((string)$path);

    return $base . '/assets' . $path;
}

/**
 * Escapa HTML para prevenir XSS
 */
function e($string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Debug - Imprime variable y detiene ejecución (solo en desarrollo)
 */
function dd($data): void
{
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}

/**
 * Formatea una fecha
 */
function formatDate($date, $format = 'd/m/Y'): string
{
    if (empty($date)) return '';

    try {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    } catch (Exception $e) {
        return (string)$date;
    }
}

/**
 * Formatea un número como moneda
 */
function formatMoney($amount, $currency = 'Q'): string
{
    return $currency . ' ' . number_format((float)$amount, 2, '.', ',');
}

/**
 * Obtiene el usuario actual de la sesión
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Verifica si el usuario actual tiene un rol específico
 */
function hasRole($role): bool
{
    $user = currentUser();
    return $user && isset($user['rol']) && $user['rol'] === $role;
}

function isAdmin(): bool
{
    return hasRole('ADMIN');
}

function isVendedor(): bool
{
    return hasRole('VENDEDOR');
}
