<?php

function app_base(): string
{
    if (!defined('APP_URL')) return '';
    $base = trim(APP_URL);

    if ($base === '/' || $base === '') return '';

    return rtrim($base, '/');
}

function norm_path(string $path): string
{
    $path = trim($path);
    if ($path === '') return '';
    return '/' . ltrim($path, '/');
}

function url($path = ''): string
{
    $base = app_base();
    $path = norm_path((string)$path);

    // Si piden url(''), devolvemos base o "/"
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }

    return $base . $path;
}

function redirect($path, $statusCode = 302): void
{
    header('Location: ' . url($path), true, $statusCode);
    exit;
}

function asset($path): string
{
    $base = app_base();
    $path = norm_path((string)$path);

    return $base . '/assets' . $path;
}

function e($string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function dd($data): void
{
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}

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

function formatMoney($amount, $currency = 'Q'): string
{
    return $currency . ' ' . number_format((float)$amount, 2, '.', ',');
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

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
