<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin - Sistema POS'; ?></title>
    <link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= url('/assets/css/layout_admin.css') ?>">
    <link rel="stylesheet" href="<?= url('/assets/css/tailwind.css') ?>">
    <meta name="description" content="Sistema POS/ERP para Ferretería - Panel Administrativo">
</head>

<body>
    <div class="layout-admin">
        <!-- Sidebar -->
        <aside class="sidebar">
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
            // TODO: Inicializar componentes de admin
            console.log('Layout Admin cargado');
        });
    </script>
</body>

</html>
