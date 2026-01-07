<?php

/**
 * Sistema de autenticación y manejo de sesiones
 * Compatible con roles ADMIN y VENDEDOR
 */

class Auth
{
    private static $instance = null;
    private $user = null;

    private function __construct()
    {
        $this->startSession();
        $this->loadUser();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();

            // Regenerar ID de sesión por seguridad
            if (!isset($_SESSION['regenerated'])) {
                session_regenerate_id(true);
                $_SESSION['regenerated'] = true;
            }
        }
    }

    private function loadUser()
    {
        if (isset($_SESSION['user_id'])) {
            // TODO: Cargar usuario desde base de datos
            // $this->user = User::find($_SESSION['user_id']);
        }
    }

    public function login($username, $password)
    {
        // TODO: Implementar lógica de login
        /*
        $user = User::findByUsername($username);

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['login_time'] = time();

            $this->user = $user;

            // Log de acceso
            $this->logAccess($user->id, 'login');

            return true;
        }
        */

        return false; // Placeholder
    }

    public function logout()
    {
        if ($this->user) {
            // Log de salida
            // $this->logAccess($this->user->id, 'logout');
        }

        // Limpiar datos de sesión
        $_SESSION = [];

        // Eliminar cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Destruir sesión
        session_destroy();

        $this->user = null;
    }

    public function isLoggedIn()
    {
        return $this->user !== null && $this->isSessionValid();
    }

    public function user()
    {
        return $this->user;
    }

    public function hasRole($role)
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return $this->user->role === $role;
    }

    public function isAdmin()
    {
        return $this->hasRole('ADMIN');
    }

    public function isVendedor()
    {
        return $this->hasRole('VENDEDOR');
    }

    public function hasPermission($permission)
    {
        // TODO: Implementar sistema de permisos granular
        if (!$this->isLoggedIn()) {
            return false;
        }

        // Permisos básicos por rol
        $rolePermissions = [
            'ADMIN' => [
                'usuarios.ver',
                'usuarios.crear',
                'usuarios.editar',
                'usuarios.eliminar',
                'productos.ver',
                'productos.crear',
                'productos.editar',
                'productos.eliminar',
                'compras.ver',
                'compras.crear',
                'compras.editar',
                'ventas.ver',
                'ventas.crear',
                'ventas.cancelar',
                'reportes.ver',
                'reportes.generar',
                'cierrecaja.ver',
                'cierrecaja.crear',
                'configuracion.ver',
                'configuracion.editar'
            ],
            'VENDEDOR' => [
                'productos.ver',
                'ventas.ver',
                'ventas.crear',
                'clientes.ver',
                'clientes.crear'
            ]
        ];

        $userRole = $this->user->role ?? '';
        $permissions = $rolePermissions[$userRole] ?? [];

        return in_array($permission, $permissions);
    }

    private function isSessionValid()
    {
        // Verificar tiempo de vida de la sesión
        if (!isset($_SESSION['login_time'])) {
            return false;
        }

        $sessionAge = time() - $_SESSION['login_time'];
        if ($sessionAge > SESSION_LIFETIME) {
            $this->logout();
            return false;
        }

        // Extender sesión si está activa
        $_SESSION['login_time'] = time();

        return true;
    }

    private function logAccess($userId, $action)
    {
        // TODO: Log de accesos en base de datos
        /*
        $log = [
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => date('Y-m-d H:i:s')
        ];

        AccessLog::create($log);
        */
    }

    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireRole($role)
    {
        $this->requireLogin();

        if (!$this->hasRole($role)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }
    }

    public function requirePermission($permission)
    {
        $this->requireLogin();

        if (!$this->hasPermission($permission)) {
            http_response_code(403);
            echo json_encode(['error' => 'Permiso denegado']);
            exit;
        }
    }
}
