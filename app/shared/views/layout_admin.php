<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin - Sistema POS'; ?></title>
    <link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= url('/assets/css/layout_admin.css') ?>">
    <link rel="stylesheet" href="<?= url('/assets/css/tailwind.css') ?>">
    <style>
        /* HAMBURGER BUTTON IN NAVBAR - IMPROVED VISIBILITY */
        .mobile-menu-toggle {
            display: none;
            background: #dc2626 !important;
            /* Rojo para mayor visibilidad */
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
            padding: 8px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            margin-right: 12px !important;
            min-width: 40px !important;
            height: 40px !important;
            justify-content: center !important;
            align-items: center !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4) !important;
            transition: all 0.2s ease !important;
        }

        .mobile-menu-toggle:hover {
            background: #b91c1c !important;
            transform: scale(1.1) !important;
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.6) !important;
        }

        .mobile-menu-toggle svg {
            width: 22px !important;
            height: 22px !important;
            stroke: currentColor !important;
            stroke-width: 3 !important;
        }

        @media screen and (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex !important;
                opacity: 1 !important;
                visibility: visible !important;
                z-index: 9999 !important;
            }

            /* Force show hamburger in mobile */
            #mobileMenuToggle {
                display: flex !important;
                background: #dc2626 !important;
                color: white !important;
                position: relative !important;
                opacity: 1 !important;
                visibility: visible !important;
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
    <meta name="description" content="Sistema POS/ERP para Ferretería - Panel Administrativo">
</head>

<body>
    <div class="layout-admin">
        <!-- Overlay para cerrar sidebar en móvil -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <?php include 'sidebar_admin.php'; ?>
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
        // Configuración global para admin
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Layout Admin cargado');
            console.log('Ancho de pantalla:', window.innerWidth);

            // Verificar si el botón hamburguesa existe
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            console.log('Botón hamburguesa encontrado:', mobileMenuToggle);

            if (mobileMenuToggle) {
                console.log('Estilos del botón:', window.getComputedStyle(mobileMenuToggle).display);
                console.log('Visibilidad del botón:', window.getComputedStyle(mobileMenuToggle).visibility);
            }

            // Funcionalidad responsive de sidebar
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Toggle sidebar en móvil
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    console.log('Hamburguesa clicked!');
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
</body>

</html>
