<?php
/**
 * Plantilla de Configuración de Entorno
 * =====================================
 * 
 * INSTRUCCIONES DE USO:
 * 1. Copiar este archivo según el entorno:
 *    - Para desarrollo local: app/config/env.local.php
 *    - Para producción: app/config/env.production.php
 * 
 * 2. Ajustar las credenciales y configuraciones según corresponda
 * 
 * 3. Verificar que el archivo principal (env.php) cargue el entorno correcto
 * 
 * IMPORTANTE: 
 * - Este archivo SÍ debe estar en Git (es una plantilla)
 * - Los archivos env.local.php y env.production.php NO deben estar en Git
 * - Agregar en .gitignore: /app/config/env.local.php y /app/config/env.production.php
 */

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================

/**
 * Host del servidor de base de datos
 * - Desarrollo local: 'localhost'
 * - Producción: IP o hostname del servidor MySQL
 * - Si MySQL usa puerto personalizado: 'localhost:3307'
 */
define('DB_HOST', 'localhost');

/**
 * Nombre de la base de datos
 * IMPORTANTE: Debe ser exactamente igual al nombre en phpMyAdmin/MySQL
 */
define('DB_NAME', 'comercializadora_sosa');

/**
 * Usuario de la base de datos
 * - XAMPP por defecto: 'root'
 * - Producción: Crear usuario específico con permisos limitados
 * 
 * SEGURIDAD EN PRODUCCIÓN:
 * ========================
 * NUNCA usar 'root' en producción. Crear usuario específico:
 * 
 * CREATE USER 'comercializadora_user'@'localhost' IDENTIFIED BY 'PASSWORD_SEGURO_AQUÍ';
 * GRANT SELECT, INSERT, UPDATE, DELETE ON comercializadora_sosa.* TO 'comercializadora_user'@'localhost';
 * FLUSH PRIVILEGES;
 */
define('DB_USER', 'root');

/**
 * Contraseña de la base de datos
 * - XAMPP por defecto: '' (vacía)
 * - Producción: Contraseña fuerte (mínimo 16 caracteres, mixta)
 * 
 * Ejemplo de contraseña fuerte: 'Xk9$mP2@nQ7#vL5&'
 */
define('DB_PASS', '');

/**
 * Charset de la conexión
 * utf8mb4 soporta emojis y caracteres especiales (ñ, tildes, etc.)
 */
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ============================================

/**
 * URL base de la aplicación
 * 
 * Opciones:
 * - '' (vacío): Rutas relativas automáticas
 * - '/comercializadora-sosa': Si está en subdirectorio de localhost
 * - 'https://dominio.com': URL completa en producción
 * 
 * Ejemplos:
 * - Desarrollo: define('APP_URL', '');
 * - Subdominio: define('APP_URL', '/pos');
 * - Producción: define('APP_URL', 'https://comercializadorasosa.com');
 */
define('APP_URL', '');

/**
 * Modo de depuración
 * 
 * true:  Muestra errores detallados (SOLO EN DESARROLLO)
 * false: Oculta errores (SIEMPRE EN PRODUCCIÓN)
 * 
 * En producción, los errores deben ir a logs, no mostrarse al usuario
 */
define('APP_DEBUG', true);

/**
 * Entorno de la aplicación
 * 
 * Valores posibles:
 * - 'local': Desarrollo local
 * - 'staging': Servidor de pruebas
 * - 'production': Servidor en vivo
 */
define('APP_ENV', 'local');

/**
 * Nombre de la aplicación
 */
define('APP_NAME', 'Comercializadora Sosa - Sistema POS');

/**
 * Zona horaria
 * Ver lista completa: https://www.php.net/manual/es/timezones.php
 */
define('APP_TIMEZONE', 'America/Guatemala');

// ============================================
// CONFIGURACIÓN DE SESIONES
// ============================================

/**
 * Tiempo de vida de la sesión (en segundos)
 * 
 * Ejemplos:
 * - 1800  = 30 minutos
 * - 3600  = 1 hora
 * - 7200  = 2 horas
 * - 86400 = 24 horas
 */
define('SESSION_LIFETIME', 7200);

/**
 * Nombre de la cookie de sesión
 * Cambiar en producción por seguridad
 */
define('SESSION_NAME', 'comercializadora_session');

// ============================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================

/**
 * Clave secreta para encriptación
 * 
 * Generar una clave única:
 * - Método 1: openssl rand -base64 32
 * - Método 2: https://www.grc.com/passwords.htm
 * 
 * IMPORTANTE: Cambiar en producción y NUNCA compartir
 */
define('APP_SECRET_KEY', 'CAMBIAR_POR_CLAVE_SECRETA_UNICA');

/**
 * Salt para hashing de contraseñas
 * Solo si usas hash personalizado (NO necesario con password_hash)
 */
define('PASSWORD_SALT', 'CAMBIAR_POR_SALT_UNICO');

// ============================================
// CONFIGURACIÓN DE UPLOADS
// ============================================

/**
 * Directorio para archivos subidos
 */
define('UPLOAD_DIR', __DIR__ . '/../../public/uploads/');

/**
 * Tamaño máximo de archivo (en bytes)
 * 5MB = 5 * 1024 * 1024 = 5242880
 */
define('MAX_FILE_SIZE', 5242880);

/**
 * Extensiones de archivo permitidas
 */
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx');

// ============================================
// CONFIGURACIÓN DE EMAIL (Opcional)
// ============================================

/**
 * Servidor SMTP para envío de correos
 * Ejemplo con Gmail:
 * - MAIL_HOST: 'smtp.gmail.com'
 * - MAIL_PORT: 587 (TLS) o 465 (SSL)
 * - MAIL_USERNAME: 'tu-correo@gmail.com'
 * - MAIL_PASSWORD: 'tu-contraseña-de-aplicación'
 */
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_FROM', 'noreply@comercializadorasosa.com');
define('MAIL_FROM_NAME', 'Comercializadora Sosa');

// ============================================
// CONFIGURACIÓN DE API (Si aplica)
// ============================================

/**
 * Token de API para integraciones externas
 */
define('API_TOKEN', '');

/**
 * URL de APIs externas
 */
define('EXTERNAL_API_URL', '');

// ============================================
// CONFIGURACIÓN FINAL
// ============================================

/**
 * Aplicar configuración de PHP según el entorno
 */
if (APP_DEBUG) {
    // Modo desarrollo: mostrar todos los errores
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Modo producción: ocultar errores y escribir a log
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../../storage/logs/php_errors.log');
}

/**
 * Configurar zona horaria
 */
if (defined('APP_TIMEZONE')) {
    date_default_timezone_set(APP_TIMEZONE);
}

/**
 * Configurar sesiones seguras
 */
if (defined('SESSION_LIFETIME')) {
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
}

// ============================================
// NOTAS FINALES
// ============================================

/*
 * CHECKLIST DE SEGURIDAD PARA PRODUCCIÓN:
 * ========================================
 * 
 * [ ] Cambiar DB_USER de 'root' a usuario específico
 * [ ] Establecer DB_PASS con contraseña fuerte
 * [ ] Configurar APP_DEBUG a false
 * [ ] Cambiar APP_SECRET_KEY por una única y segura
 * [ ] Configurar APP_URL con el dominio real
 * [ ] Verificar que logs/ tenga permisos de escritura
 * [ ] Verificar que uploads/ tenga permisos restringidos
 * [ ] Configurar HTTPS (certificado SSL)
 * [ ] Habilitar headers de seguridad (HSTS, CSP, etc.)
 * [ ] Configurar backups automáticos de la BD
 * [ ] Revisar permisos de archivos (644 para archivos, 755 para carpetas)
 * [ ] Deshabilitar listing de directorios en Apache
 * [ ] Configurar firewall del servidor
 * [ ] Implementar rate limiting para login
 * 
 * PRUEBAS ANTES DE DESPLEGAR:
 * ============================
 * 
 * 1. Ejecutar: http://tu-dominio.com/health.php
 * 2. Ejecutar: http://tu-dominio.com/test_db.php
 * 3. Probar login con usuario de prueba
 * 4. Verificar que assets (CSS/JS) carguen
 * 5. Probar todas las funcionalidades críticas
 * 6. Revisar logs de errores
 * 7. Verificar tiempos de respuesta
 * 8. Hacer backup completo antes de deployment
 */
