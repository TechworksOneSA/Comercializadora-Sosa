<!-- Sidebar para Administrador -->
<!-- DEBUG: Sidebar admin cargado correctamente -->
<div class="sidebar-header p-4 border-b border-surface-tertiary" style="position:relative;">
    <div style="width: 40px; height: 40px; background: white !important; border-radius: 14px !important; margin: 44px auto 0 !important; position: relative; z-index: 1000 !important; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 12px rgba(0,0,0,0.10);">
        <img src="<?= url('/assets/img/logo_sosa.png') ?>" style="width: 260px; height: 260px; object-fit: contain; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -62%); z-index: 2000 !important;" alt="Comercializadora Sosa">
    </div>
    <div style="height: 48px;"></div>
    <h2 class="text-lg font-bold text-primary text-center">Admin Panel</h2>
    <p class="text-sm text-text-secondary text-center">Sistema POS/ERP</p>
</div>

<nav class="sidebar-nav p-4">
    <ul class="space-y-2">
        <!-- Dashboard -->
        <li>
            <a href="<?= url('/admin/dashboard') ?>" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Gestión de Caja - NUEVO BOTÓN -->
        <li>
            <a href="<?= url('/admin/caja') ?>" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary" style="background-color: #059669; color: white;">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>🏦 GESTIÓN DE CAJA</span>
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

        <!-- Productos e Inventario -->
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


                </ul>
            </div>
        </li>

        <!-- Catálogos -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Catálogos</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="/catalogos" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <span>Catálogos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/compras" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Órdenes de Compra</span>
                        </a>
                    </li>
                    <li>
                        <a href="/proveedores" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Proveedores</span>
                        </a>
                    </li>
                    <li>
                        <a href="/cotizaciones" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Cotizaciones</span>
                        </a>
                    </li>
                </ul>
            </div>
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

        <!-- FEL -->
        <li>
            <a href="/fel" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>FEL</span>
            </a>
        </li>

        <!-- Administración -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Administración</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="/admin/usuarios" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Usuarios</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Reportes -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Reportes</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="/reportes/ventas" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <span>Ventas</span>
                        </a>
                    </li>

                    <li>
                        <a href="/cierrecaja" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Cierre de Caja</span>
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
            A
        </div>
        <div>
            <p class="text-sm font-medium">Administrador</p>
            <p class="text-xs text-text-secondary">admin@ferreteria.com</p>
        </div>
    </div>
    <a href="<?= url('/logout') ?>" class="btn btn-secondary w-full mt-3 text-sm">
        Cerrar Sesión
    </a>
</div>
