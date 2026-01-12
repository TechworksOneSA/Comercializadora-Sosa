<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema POS</title>

    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>">
</head>

<body>
    <div class="layout-admin">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header p-4 border-b border-surface-tertiary">
                <h2 class="text-lg font-bold text-primary">Admin Panel</h2>
                <p class="text-sm text-text-secondary">Sistema POS/ERP</p>
            </div>

            <nav class="sidebar-nav p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="<?= url('/dashboard') ?>" class="sidebar-link flex items-center p-3 rounded bg-primary text-white">
                            <span class="icon mr-3">📊</span>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= url('/admin/productos') ?>" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                            <span class="icon mr-3">📦</span>
                            <span>Productos</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= url('/admin/ventas') ?>" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                            <span class="icon mr-3">💰</span>
                            <span>Punto de Venta</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= url('/logout') ?>" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary text-error">
                            <span class="icon mr-3">🚪</span>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Navbar superior -->
            <header class="navbar">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-text-primary">Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-text-secondary">
                            <p>Fecha: <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); echo date('d/m/Y'); ?></p>
                            <p>Usuario: Administrador</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenido -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-2xl font-bold text-primary">Q 12,500</h3>
                            <p class="text-text-secondary">Ventas del Día</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-2xl font-bold text-success">156</h3>
                            <p class="text-text-secondary">Productos en Stock</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-2xl font-bold text-warning">12</h3>
                            <p class="text-text-secondary">Stock Bajo</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-2xl font-bold text-info">45</h3>
                            <p class="text-text-secondary">Ventas Hoy</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="font-semibold">🎉 Sistema Funcionando</h3>
                        </div>
                        <div class="card-body">
                            <p class="mb-4">¡Felicidades! El sistema POS/ERP está funcionando correctamente.</p>
                            <ul class="list-disc list-inside space-y-2 text-sm text-text-secondary">
                                <li>✅ Base de datos conectada</li>
                                <li>✅ Estructura de archivos completa</li>
                                <li>✅ CSS y JavaScript cargados</li>
                                <li>⚠️ Pendiente: Funcionalidades de módulos</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="font-semibold">📋 Próximos Pasos</h3>
                        </div>
                        <div class="card-body">
                            <ol class="list-decimal list-inside space-y-2 text-sm">
                                <li>Implementar autenticación funcional</li>
                                <li>Desarrollar módulo de productos</li>
                                <li>Crear sistema POS</li>
                                <li>Configurar FEL</li>
                                <li>Desplegar en Hostinger</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        console.log('Dashboard cargado correctamente');
        setInterval(() => {
            const now = new Date().toLocaleTimeString();
            console.log('Sistema activo:', now);
        }, 60000);
    </script>
</body>

</html>
