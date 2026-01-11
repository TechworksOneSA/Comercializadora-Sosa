<!-- Navbar superior -->
<div class="flex justify-between items-center navbar-responsive">
    <!-- Info de la página actual -->
    <div class="flex items-center navbar-title">
        <!-- Botón hamburger para móvil - MÁS VISIBLE -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle" style="background: #dc2626 !important; border: 2px solid #fff !important; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.6) !important; min-width: 44px !important; height: 44px !important;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-width: 3;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <h1 class="text-xl font-semibold text-text-primary navbar-heading">
            <?php echo $pageTitle ?? 'Dashboard'; ?>
        </h1>
        <?php if (isset($breadcrumb)): ?>
            <nav class="ml-4 text-sm text-text-secondary navbar-breadcrumb">
                <?php echo $breadcrumb; ?>
            </nav>
        <?php endif; ?>
    </div>

    <!-- Acciones y usuario -->
    <div class="flex items-center space-x-4 navbar-actions">
        <!-- Info rápida del sistema -->
        <div class="text-sm text-text-secondary navbar-user-info">
            <p>Fecha: <?php echo date('d/m/Y'); ?></p>
            <p>Hora: <span id="current-time"><?php echo date('g:i:s A'); ?></span></p>
        </div>

        <!-- Usuario actual -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-bold">
                <?php
                // TODO: Obtener inicial del usuario logueado
                echo 'U';
                ?>
            </div>
            <div class="text-sm">
                <!-- Usuario y rol removidos por solicitud -->
            </div>
        </div>
    </div>
</div>

<script>
    // Actualizar hora en tiempo real
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-GT', {
            hour12: true
        });
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    // Actualizar cada segundo
    setInterval(updateTime, 1000);
</script>
