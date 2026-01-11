<?php
$ventasHoy = $ventasHoy ?? ['cantidad_ventas' => 0, 'total_ventas' => 0];
$misVentas = $misVentas ?? ['cantidad' => 0, 'total' => 0];
$efectivoCaja = $efectivoCaja ?? 0;
$alertas = $alertas ?? ['productos_bajo_stock' => 0, 'ventas_por_cobrar_cantidad' => 0];
?>

<style>
  .vendedor-dashboard {
    padding: 2rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    min-height: 100vh;
  }

  .vendedor-header {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    padding: 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 20px rgba(10, 61, 145, 0.3);
    color: white;
  }

  .vendedor-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
  }

  .vendedor-header p {
    opacity: 0.9;
    margin: 0;
    font-size: 1rem;
  }

  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .kpi-card {
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }

  .kpi-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }

  .kpi-icon.blue {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  }

  .kpi-icon.green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  }

  .kpi-icon.orange {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  }

  .kpi-icon.purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  }

  .kpi-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
  }

  .kpi-value {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0.5rem 0;
  }

  .kpi-subtitle {
    font-size: 0.875rem;
    color: #94a3b8;
  }

  .quick-actions {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
  }

  .quick-actions h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0a3d91;
    margin: 0 0 1.5rem 0;
  }

  .actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s;
    color: #0f172a;
  }

  .action-btn:hover {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    color: white;
    border-color: #0a3d91;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(10, 61, 145, 0.2);
  }

  .action-btn svg {
    width: 32px;
    height: 32px;
  }

  .action-btn span {
    font-weight: 600;
    font-size: 0.9375rem;
  }

  .alert-box {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .alert-box h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0a3d91;
    margin: 0 0 1rem 0;
  }

  .alert-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    border-radius: 8px;
    margin-bottom: 0.75rem;
  }

  .alert-item:last-child {
    margin-bottom: 0;
  }

  .alert-item.info {
    background: #dbeafe;
    border-left-color: #3b82f6;
  }

  .alert-icon {
    font-size: 1.5rem;
  }

  .alert-text {
    flex: 1;
    font-size: 0.9375rem;
    color: #1f2937;
  }
</style>

<div class="vendedor-dashboard">
  <!-- Header -->
  <div class="vendedor-header">
    <h1>👋 ¡Bienvenido, <?= htmlspecialchars($_SESSION['user']['nombre']) ?>!</h1>
    <p>Panel de Ventas - <?php
                          $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                          $meses = [
                            'enero',
                            'febrero',
                            'marzo',
                            'abril',
                            'mayo',
                            'junio',
                            'julio',
                            'agosto',
                            'septiembre',
                            'octubre',
                            'noviembre',
                            'diciembre'
                          ];
                          $dia_semana = $dias[date('w')];
                          $dia = date('d');
                          $mes = $meses[date('n') - 1];
                          $año = date('Y');
                          echo "$dia_semana, $dia de $mes de $año";
                          ?></p>
  </div>

  <!-- KPIs -->
  <div class="kpi-grid">
    <!-- Ventas del día -->
    <div class="kpi-card">
      <div class="kpi-header">
        <div class="kpi-icon blue">💰</div>
        <div>
          <div class="kpi-label">Ventas de Hoy</div>
        </div>
      </div>
      <div class="kpi-value">Q<?= number_format($ventasHoy['total_ventas'], 2) ?></div>
      <div class="kpi-subtitle"><?= $ventasHoy['cantidad_ventas'] ?> ventas realizadas</div>
    </div>

    <!-- Mis ventas -->
    <div class="kpi-card">
      <div class="kpi-header">
        <div class="kpi-icon green">📊</div>
        <div>
          <div class="kpi-label">Mis Ventas Hoy</div>
        </div>
      </div>
      <div class="kpi-value">Q<?= number_format($misVentas['total'], 2) ?></div>
      <div class="kpi-subtitle"><?= $misVentas['cantidad'] ?> ventas personales</div>
    </div>

    <!-- Efectivo en caja -->
    <div class="kpi-card">
      <div class="kpi-header">
        <div class="kpi-icon orange">💵</div>
        <div>
          <div class="kpi-label">Efectivo en Caja</div>
        </div>
      </div>
      <div class="kpi-value">Q<?= number_format($efectivoCaja, 2) ?></div>
      <div class="kpi-subtitle">Balance actual</div>
    </div>

    <!-- Cobros pendientes -->
    <div class="kpi-card">
      <div class="kpi-header">
        <div class="kpi-icon purple">⏱️</div>
        <div>
          <div class="kpi-label">Cobros Pendientes</div>
        </div>
      </div>
      <div class="kpi-value"><?= $alertas['ventas_por_cobrar_cantidad'] ?></div>
      <div class="kpi-subtitle">Ventas por cobrar</div>
    </div>
  </div>

  <!-- Acciones rápidas -->
  <div class="quick-actions">
    <h2>⚡ Acciones Rápidas</h2>
    <div class="actions-grid">
      <a href="/admin/pos" class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        <span>Nueva Venta</span>
      </a>

      <a href="/admin/ventas" class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span>Historial Ventas</span>
      </a>

      <a href="/admin/cotizaciones" class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span>Cotizaciones</span>
      </a>

      <a href="/admin/clientes" class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span>Clientes</span>
      </a>

      <a href="/admin/productos" class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <span>Productos</span>
      </a>

      <a href="/admin/deudores" class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Deudores</span>
      </a>
    </div>
  </div>

  <!-- Alertas -->
  <?php if ($alertas['productos_bajo_stock'] > 0 || $alertas['ventas_por_cobrar_cantidad'] > 0): ?>
    <div class="alert-box">
      <h2>⚠️ Alertas y Notificaciones</h2>

      <?php if ($alertas['productos_bajo_stock'] > 0): ?>
        <div class="alert-item">
          <div class="alert-icon">📦</div>
          <div class="alert-text">
            <strong><?= $alertas['productos_bajo_stock'] ?> productos</strong> tienen stock bajo. Revisa el inventario.
          </div>
        </div>
      <?php endif; ?>

      <?php if ($alertas['ventas_por_cobrar_cantidad'] > 0): ?>
        <div class="alert-item info">
          <div class="alert-icon">💰</div>
          <div class="alert-text">
            Tienes <strong><?= $alertas['ventas_por_cobrar_cantidad'] ?> ventas pendientes</strong> de cobro.
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
