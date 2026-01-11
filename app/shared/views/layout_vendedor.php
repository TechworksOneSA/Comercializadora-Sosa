<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'POS - Sistema Ventas'; ?></title>
    <link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= url('/assets/css/layout_vendedor.css') ?>">
    <script>
        // Suprimir warning de Tailwind CDN
        const originalWarn = console.warn;
        console.warn = function(...args) {
            if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com should not be used in production')) {
                return; // Suprimir este warning específico
            }
            originalWarn.apply(console, args);
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* HAMBURGER BUTTON IN NAVBAR */
        .mobile-menu-toggle {
            display: none;
            background: #0a3d91;
            color: #ffffff;
            border: none;
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 12px;
            min-width: 36px;
            height: 36px;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            background: #083875;
            transform: scale(1.05);
        }

        .mobile-menu-toggle svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
        }

        @media screen and (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex !important;
            }

            .sidebar {
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease !important;
            }

            .sidebar.sidebar-open {
                transform: translateX(0) !important;
            }

            .sidebar-overlay {
                display: block !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: rgba(0, 0, 0, 0.5) !important;
                z-index: 999 !important;
                opacity: 0 !important;
                transition: opacity 0.3s ease !important;
            }

            .sidebar-overlay.overlay-active {
                opacity: 1 !important;
            }
        }
    </style>
    <script>
        // Suprimir warning de producción de Tailwind CSS
        window.tailwind = {
            config: {}
        };
        // Ocultar advertencias de consola para desarrollo
        if (console && console.warn) {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                if (args[0] && args[0].includes && args[0].includes('cdn.tailwindcss.com should not be used in production')) {
                    return; // Silenciar esta advertencia específica
                }
                return originalWarn.apply(console, args);
            };
        }
    </script>
    <meta name="description" content="Sistema POS para Ferretería - Panel de Ventas">
</head>

<body>
    <div class="layout-vendedor">
        <!-- Overlay para cerrar sidebar en móvil -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar simplificado para vendedor -->
        <aside class="sidebar" id="sidebar">
            <?php include 'sidebar_vendedor.php'; ?>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Navbar superior -->
            <header class="navbar">
                <?php include 'navbar.php'; ?>
            </header>

            <!-- Contenido de la página -->
            <div class="content-wrapper">
                <?php echo $content ?? ''; ?>
            </div>
        </main>
    </div>

    <!-- Scripts globales -->
    <script src="<?= url('/assets/js/app.js') ?>"></script>

    <!-- Scripts específicos de página -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= url($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Configuración global para vendedor/POS
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Layout Vendedor/POS cargado');

            // Inicializar escáner si está disponible
            if (POS && POS.scanner) {
                POS.scanner.startListening();
            }

            // Funcionalidad responsive de sidebar
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Toggle sidebar en móvil
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('sidebar-open');
                    sidebarOverlay.classList.toggle('overlay-active');
                });
            }

            // Cerrar sidebar al hacer click en overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('sidebar-open');
                    sidebarOverlay.classList.remove('overlay-active');
                });
            }

            // Cerrar sidebar al redimensionar a pantalla grande
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('sidebar-open');
                    sidebarOverlay.classList.remove('overlay-active');
                }
            });

            // Cerrar sidebar al hacer click en un enlace (solo en móvil)
            const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('sidebar-open');
                        sidebarOverlay.classList.remove('overlay-active');
                    }
                });
            });
        });
    </script>

    <!-- Modal de advertencia de cierre de sesión -->
    <div id="sessionWarningModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; max-width: 400px; animation: slideDown 0.3s ease-out;">
            <div style="font-size: 60px; margin-bottom: 15px; animation: pulse 1s infinite;">⏰</div>
            <h2 style="color: #dc2626; margin-bottom: 15px; font-size: 24px;">¡Sesión Inactiva!</h2>
            <p style="color: #4b5563; margin-bottom: 20px; font-size: 16px;">Tu sesión se cerrará en <span id="countdownTimer" style="font-size: 32px; font-weight: bold; color: #dc2626;">60</span> segundos por inactividad.</p>
            <button onclick="mantenerSesion()" style="background: #10b981; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; transition: background 0.3s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">Mantener Sesión Activa</button>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }
    </style>

    <script>
        // Control de inactividad del usuario
        let inactivityTimer;
        let warningTimer;
        let countdownInterval;
        const INACTIVITY_TIME = 840000; // 14 minutos antes de mostrar advertencia
        const WARNING_TIME = 60000; // 60 segundos de advertencia antes de logout

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            clearTimeout(warningTimer);
            clearInterval(countdownInterval);
            hideWarningModal();

            // Reiniciar el temporizador de inactividad
            inactivityTimer = setTimeout(showWarning, INACTIVITY_TIME);
        }

        function showWarning() {
            const modal = document.getElementById('sessionWarningModal');
            modal.style.display = 'flex';

            let countdown = 60;
            const countdownElement = document.getElementById('countdownTimer');

            countdownInterval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    cerrarSesion();
                }
            }, 1000);
        }

        function hideWarningModal() {
            const modal = document.getElementById('sessionWarningModal');
            modal.style.display = 'none';
            document.getElementById('countdownTimer').textContent = '60';
        }

        function mantenerSesion() {
            clearInterval(countdownInterval);
            hideWarningModal();

            // Hacer una petición AJAX para actualizar la actividad en el servidor
            fetch('<?= url("/api/mantener-sesion") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                resetInactivityTimer();
            });
        }

        function cerrarSesion() {
            window.location.href = '<?= url("/logout") ?>';
        }

        // Eventos que resetean el temporizador de inactividad
        document.addEventListener('mousemove', resetInactivityTimer);
        document.addEventListener('keypress', resetInactivityTimer);
        document.addEventListener('click', resetInactivityTimer);
        document.addEventListener('scroll', resetInactivityTimer);
        document.addEventListener('touchstart', resetInactivityTimer);

        // Iniciar el temporizador cuando carga la página
        resetInactivityTimer();
    </script>

</body>

</html>
