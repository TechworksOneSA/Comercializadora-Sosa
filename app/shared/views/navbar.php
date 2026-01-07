<!-- Navbar superior -->
<div class="flex justify-between items-center">
    <!-- Info de la página actual -->
    <div class="flex items-center">
        <h1 class="text-xl font-semibold text-text-primary">
            <?php echo $pageTitle ?? 'Dashboard'; ?>
        </h1>
        <?php if (isset($breadcrumb)): ?>
            <nav class="ml-4 text-sm text-text-secondary">
                <?php echo $breadcrumb; ?>
            </nav>
        <?php endif; ?>
    </div>

    <!-- Acciones y usuario -->
    <div class="flex items-center space-x-4">
        <!-- Notificaciones -->
        <button class="p-2 rounded hover:bg-surface-secondary relative">
            <span class="text-lg">🔔</span>
            <span class="absolute -top-1 -right-1 bg-error text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                3
            </span>
        </button>

        <!-- Info rápida del sistema -->
        <div class="text-sm text-text-secondary">
            <p>Fecha: <?php echo date('d/m/Y'); ?></p>
            <p>Hora: <span id="current-time"><?php echo date('H:i:s'); ?></span></p>
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
                <p class="font-medium">
                    <?php
                    // TODO: Obtener nombre del usuario logueado
                    echo 'Usuario Sistema';
                    ?>
                </p>
                <p class="text-text-secondary">
                    <?php
                    // TODO: Obtener rol del usuario logueado
                    echo 'Administrador';
                    ?>
                </p>
            </div>
        </div>

        <!-- Menú de usuario -->
        <div class="relative">
            <button class="p-2 rounded hover:bg-surface-secondary" onclick="toggleUserMenu()">
                <span class="text-lg">⚙️</span>
            </button>

            <!-- Dropdown del menú de usuario -->
            <div id="user-menu" class="hidden absolute right-0 top-full mt-2 w-48 bg-surface border border-surface-tertiary rounded shadow-lg z-50">
                <a href="/profile" class="block px-4 py-2 text-sm hover:bg-surface-secondary">
                    Mi Perfil
                </a>
                <a href="/configuracion" class="block px-4 py-2 text-sm hover:bg-surface-secondary">
                    Configuración
                </a>
                <hr class="border-surface-tertiary">
                <a href="/auth/logout" class="block px-4 py-2 text-sm text-error hover:bg-surface-secondary">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Actualizar hora en tiempo real
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-GT', {
            hour12: false
        });
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    // Actualizar cada segundo
    setInterval(updateTime, 1000);

    // Toggle del menú de usuario
    function toggleUserMenu() {
        const menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    }

    // Cerrar menú cuando se hace click fuera
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('user-menu');
        const button = event.target.closest('button');

        if (!button || !button.getAttribute('onclick')) {
            menu.classList.add('hidden');
        }
    });
</script>
