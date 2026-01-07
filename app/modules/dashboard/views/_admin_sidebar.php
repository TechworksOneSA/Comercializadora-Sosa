<aside class="admin-sidebar">
  <div class="admin-sidebar-brand">
    <img src="<?= url('/assets/img/logo_sosa.png') ?>" class="admin-logo" alt="Comercializadora Sosa">
    <div class="admin-brand-text">
      <div class="admin-brand-sub">Administrador</div>
    </div>
  </div>

<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
function isActive($path) {
  global $currentPath;
  return strpos($currentPath, $path) !== false ? 'active' : '';
}
?>

  <nav class="admin-nav">
    <div class="admin-nav-section">Panel Ejecutivo</div>
    <a href="<?= url('/admin/dashboard') ?>" class="admin-nav-item <?= isActive('/admin/dashboard') ?>">
      <span class="admin-nav-icon">📊</span>
      Dashboard
    </a>

    <div class="admin-nav-section">Catálogos</div>
    <a href="<?= url('/admin/productos') ?>" class="admin-nav-item <?= isActive('/admin/productos') ?>">
      <span class="admin-nav-icon">📦</span>
      Inventario
    </a>
    <a href="<?= url('/admin/inventario-avanzado') ?>" class="admin-nav-item <?= isActive('/admin/inventario-avanzado') ?>">
      <span class="admin-nav-icon">📥</span>
      Inventario Avanzado
    </a>
    <a href="<?= url('/admin/compras') ?>" class="admin-nav-item <?= isActive('/admin/compras') ?>">
      <span class="admin-nav-icon">🛒</span>
      Órdenes de Compra
    </a>
    <a href="<?= url('/admin/proveedores') ?>" class="admin-nav-item <?= isActive('/admin/proveedores') ?>">
      <span class="admin-nav-icon">🚚</span>
      Proveedores
    </a>
    <a href="<?= url('/admin/cotizaciones') ?>" class="admin-nav-item <?= isActive('/admin/cotizaciones') ?>">
      <span class="admin-nav-icon">📝</span>
      Cotizaciones
    </a>

    <div class="admin-nav-section">Operaciones</div>
    <a href="<?= url('/admin/ventas') ?>" class="admin-nav-item <?= isActive('/admin/ventas') ?>">
      <span class="admin-nav-icon">💰</span>
      Ventas
    </a>

    <div class="admin-nav-section">Clientes</div>
    <a href="<?= url('/admin/clientes') ?>" class="admin-nav-item <?= isActive('/admin/clientes') ?>">
      <span class="admin-nav-icon">👥</span>
      Clientes
    </a>
    <a href="<?= url('/admin/deudores') ?>" class="admin-nav-item <?= isActive('/admin/deudores') ?>">
      <span class="admin-nav-icon">🧾</span>
      Deudores
    </a>

    <div class="admin-nav-section">Administración</div>
    <a href="<?= url('/admin/usuarios') ?>" class="admin-nav-item <?= isActive('/admin/usuarios') ?>">
      <span class="admin-nav-icon">👤</span>
      Usuarios
    </a>

    <div class="admin-nav-section">Caja</div>
    <a href="<?= url('/admin/pos') ?>" class="admin-nav-item <?= isActive('/admin/pos') ?>">
      <span class="admin-nav-icon">💰</span>
      Punto de Venta
    </a>

    <div class="admin-nav-section">Reportes</div>
    <a href="<?= url('/admin/reportes') ?>" class="admin-nav-item <?= isActive('/admin/reportes') ?>">
      <span class="admin-nav-icon">📈</span>
      Reportes
    </a>
  </nav>
</aside>
