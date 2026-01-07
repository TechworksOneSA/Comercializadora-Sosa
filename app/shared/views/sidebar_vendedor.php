<!-- Sidebar para Vendedor -->
<div class="sidebar-header">
    <img src="<?= url('/assets/img/logo_sosa.png') ?>" style="width: 80px; height: 80px; object-fit: contain; background: white; border-radius: 12px; padding: 8px; margin: 0 auto 12px; display: block;" alt="Comercializadora Sosa">
    <h2 class="text-lg font-bold text-white text-center">Panel Vendedor</h2>
    <p class="text-sm text-center" style="color: rgba(255, 255, 255, 0.7);">Sistema POS/ERP</p>
</div>

<nav class="sidebar-nav p-4">
    <ul class="space-y-2">
        <!-- Dashboard -->
        <li>
            <a href="<?= url('/admin/dashboard-vendedor') ?>" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Operaciones -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Operaciones</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="<?= url('/admin/pos') ?>" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>Punto de Venta</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/admin/ventas') ?>" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Historial Ventas</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Inventario (solo consulta) -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Inventario</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="<?= url('/admin/productos') ?>" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/admin/inventario-avanzado') ?>" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>Control Stock</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Cotizaciones -->
        <li>
            <a href="<?= url('/admin/cotizaciones') ?>" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Cotizaciones</span>
            </a>
        </li>

        <!-- Clientes -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Clientes</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="<?= url('/admin/clientes') ?>" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/admin/deudores') ?>" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Deudores</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>

<!-- Usuario actual -->
<div class="sidebar-footer p-4 border-t border-surface-tertiary mt-auto">
    <div class="flex items-center">
        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
            <?= strtoupper(substr($_SESSION['user']['nombre'] ?? 'V', 0, 1)) ?>
        </div>
        <div>
            <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['user']['nombre'] ?? 'Usuario') ?></p>
            <p class="text-xs text-text-secondary"><?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></p>
        </div>
    </div>
    <a href="<?= url('/logout') ?>" class="btn btn-secondary w-full mt-3 text-sm">
        Cerrar Sesión
    </a>
</div>
