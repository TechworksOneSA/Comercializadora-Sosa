<?php

/**
 * Helper unificado para manejo de imágenes (usuarios / productos / etc.)
 *
 * Objetivo:
 * - Guardar FUERA del repo (persistente): /srv/storage/comercializadora/uploads/<scope>/
 * - Exponer por URL pública: /uploads/<scope>/<archivo>
 *
 * Requisitos del server (ya lo tiene funcionando por su prueba):
 * - Apache debe poder servir /uploads/* apuntando a /srv/storage/comercializadora/uploads/*
 */
class ImageUploadHelper
{
    // Base real (persistente fuera del repo)
    private static string $storageBase = '/srv/storage/comercializadora/uploads';

    // URL pública (lo que se guarda en BD)
    private static string $publicBaseUrl = '/uploads';

    // Extensiones permitidas
    private static array $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

    // Límite (bytes)
    private static int $maxSize = 5 * 1024 * 1024; // 5MB

    /**
     * Procesar imagen base64 y guardarla.
     * @param string $base64Data data:image/...;base64,xxxx
     * @param string $scope carpeta lógica: 'usuarios', 'productos', etc.
     * @param string|int|null $entityId opcional para prefijo (userId, productoId)
     * @return array {success:bool, url?:string, fileName?:string, error?:string}
     */
    public static function processBase64Image(string $base64Data, string $scope = 'usuarios', $entityId = null): array
    {
        try {
            $scope = self::sanitizeScope($scope);

            if (!self::validateBase64Format($base64Data)) {
                return ['success' => false, 'error' => 'Formato de imagen inválido'];
            }

            $imageInfo = self::extractImageData($base64Data);
            if (!$imageInfo) {
                return ['success' => false, 'error' => 'No se pudo procesar la imagen'];
            }

            // Validar tamaño real (bytes)
            $size = strlen($imageInfo['data']);
            if ($size > self::$maxSize) {
                return ['success' => false, 'error' => 'La imagen es demasiado grande (máximo 5MB)'];
            }

            // Validar extensión
            $ext = strtolower($imageInfo['extension']);
            if (!in_array($ext, self::$allowedExt, true)) {
                return ['success' => false, 'error' => 'Extensión no permitida'];
            }

            // Generar nombre
            $fileName = self::generateFileName($ext, $scope, $entityId);

            // Guardar
            $save = self::saveImage($imageInfo['data'], $scope, $fileName);
            if (!$save['success']) {
                return $save;
            }

            // URL pública
            $url = rtrim(self::$publicBaseUrl, '/') . '/' . $scope . '/' . $fileName;

            return [
                'success'  => true,
                'url'      => $url,
                'fileName' => $fileName,
            ];
        } catch (Throwable $e) {
            error_log("ImageUploadHelper::processBase64Image ERROR: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error interno procesando la imagen'];
        }
    }

    /**
     * Optimizar imagen base64 (si GD está disponible).
     * Mantiene proporción y reduce tamaño para evitar subir "monstruos".
     */
    public static function optimizeBase64Image(string $base64Data, int $maxWidth = 800, int $maxHeight = 800): string
    {
        if (!extension_loaded('gd')) return $base64Data;

        try {
            $imageInfo = self::extractImageData($base64Data);
            if (!$imageInfo) return $base64Data;

            $image = @imagecreatefromstring($imageInfo['data']);
            if (!$image) return $base64Data;

            $originalWidth  = imagesx($image);
            $originalHeight = imagesy($image);

            $ratio = min($maxWidth / max(1, $originalWidth), $maxHeight / max(1, $originalHeight));
            if ($ratio >= 1) {
                imagedestroy($image);
                return $base64Data;
            }

            $newWidth  = (int)floor($originalWidth * $ratio);
            $newHeight = (int)floor($originalHeight * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Transparencia para PNG/WEBP si aplica
            $ext = strtolower($imageInfo['extension']);
            if ($ext === 'png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefill($resized, 0, 0, $transparent);
            }

            imagecopyresampled(
                $resized,
                $image,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            ob_start();
            if ($ext === 'png') {
                imagepng($resized, null, 8);
                $mime = 'png';
            } else {
                // Para jpg/webp/otros, exportamos a JPEG con calidad
                imagejpeg($resized, null, 85);
                $mime = 'jpeg';
            }
            $newImageData = ob_get_clean();

            imagedestroy($image);
            imagedestroy($resized);

            return 'data:image/' . $mime . ';base64,' . base64_encode($newImageData);
        } catch (Throwable $e) {
            error_log("ImageUploadHelper::optimizeBase64Image ERROR: " . $e->getMessage());
            return $base64Data;
        }
    }

    /**
     * Eliminar imagen por URL pública guardada en BD.
     * Ej: /uploads/usuarios/xxxx.jpg
     */
    public static function deleteImage(?string $publicUrl): bool
    {
        if (!$publicUrl) return true;

        try {
            // Normalizar: solo aceptamos rutas dentro de /uploads/
            $publicUrl = trim($publicUrl);
            if (strpos($publicUrl, self::$publicBaseUrl . '/') !== 0) {
                return true; // no tocamos rutas raras
            }

            $relative = ltrim(substr($publicUrl, strlen(self::$publicBaseUrl)), '/'); // usuarios/xxx.jpg
            $relative = str_replace(['..', '\\'], '', $relative);

            $absPath = rtrim(self::$storageBase, '/') . '/' . $relative;

            if (is_file($absPath)) {
                return @unlink($absPath);
            }
            return true;
        } catch (Throwable $e) {
            error_log("ImageUploadHelper::deleteImage ERROR: " . $e->getMessage());
            return false;
        }
    }

    // =========================
    // Internals
    // =========================

    private static function validateBase64Format(string $base64Data): bool
    {
        return (bool)preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $base64Data);
    }

    private static function extractImageData(string $base64Data): ?array
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $base64Data, $m)) {
            return null;
        }

        $ext = strtolower($m[1]);
        if ($ext === 'jpeg') $ext = 'jpg';

        $raw = preg_replace('/^data:image\/(jpeg|jpg|png|webp);base64,/', '', $base64Data);
        $bin = base64_decode($raw, true);

        if ($bin === false) return null;

        return [
            'extension' => $ext,
            'data'      => $bin,
        ];
    }

    private static function sanitizeScope(string $scope): string
    {
        $scope = strtolower(trim($scope));
        $scope = preg_replace('/[^a-z0-9_\-]/', '', $scope);
        if ($scope === '') $scope = 'usuarios';
        return $scope;
    }

    private static function generateFileName(string $ext, string $scope, $entityId = null): string
    {
        $idPart = ($entityId !== null && $entityId !== '') ? preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$entityId) : null;
        $prefix = $idPart ? "{$scope}_{$idPart}" : $scope;

        // Nombre único
        return $prefix . '_' . bin2hex(random_bytes(6)) . '_' . time() . '.' . $ext;
    }

    private static function saveImage(string $binary, string $scope, string $fileName): array
    {
        $dir = rtrim(self::$storageBase, '/') . '/' . $scope . '/';

        // Crear directorio si no existe
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true)) {
                return ['success' => false, 'error' => 'No se pudo crear el directorio de uploads'];
            }
        }

        // Permisos: asegurar que www-data pueda escribir (si el owner/grupo está bien)
        // (No forzamos chown/chgrp aquí por seguridad; eso se hace en servidor con sudo.)

        $path = $dir . $fileName;

        if (@file_put_contents($path, $binary) === false) {
            return ['success' => false, 'error' => 'No se pudo guardar la imagen'];
        }

        if (!is_file($path)) {
            return ['success' => false, 'error' => 'Error verificando la imagen guardada'];
        }

        return ['success' => true];
    }
}
