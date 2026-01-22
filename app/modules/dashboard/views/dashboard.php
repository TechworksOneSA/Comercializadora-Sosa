<?php
// Inicializar variables con valores por defecto
$ventasHoy = $ventasHoy ?? ['cantidad_ventas' => 0, 'total_ventas' => 0];
$efectivoCaja = $efectivoCaja ?? 0;
$gastosHoy = $gastosHoy ?? ['cantidad_gastos' => 0, 'total_gastos' => 0];
$retirosHoy = $retirosHoy ?? ['cantidad_retiros' => 0, 'total_retiros' => 0];
$margenGanancia = $margenGanancia ?? ['ganancia_real' => 0, 'porcentaje_margen' => 0];
$gananciasMes = $gananciasMes ?? ['ventas_mes' => 0, 'gastos_mes' => 0, 'compras_mes' => 0, 'ganancias_mes' => 0];
$alertas = $alertas ?? [
  'productos_bajo_stock' => 0,
  'productos_sin_stock' => 0,
  'ventas_por_cobrar_cantidad' => 0,
  'ventas_por_cobrar_monto' => 0
];

// Cargar CSS específico del dashboard
?>
<link rel="stylesheet" href="<?= url('/assets/css/dashboard.css') ?>">

<div class="dashboard-container">
  <!-- Header del Dashboard -->
  <header class="dashboard-header">
    <h1>👋 Bienvenido, <?= htmlspecialchars($user["nombre"]) ?></h1>
    <p>Panel Ejecutivo - Comercializadora Sosa</p>
    <div class="datetime-info">
      <span class="date-badge">
        📅 <?php
            $dias = [
              'Sunday' => 'Domingo',
              'Monday' => 'Lunes',
              'Tuesday' => 'Martes',
              'Wednesday' => 'Miércoles',
              'Thursday' => 'Jueves',
              'Friday' => 'Viernes',
              'Saturday' => 'Sábado'
            ];
            $meses = [
              'January' => 'Enero',
              'February' => 'Febrero',
              'March' => 'Marzo',
              'April' => 'Abril',
              'May' => 'Mayo',
              'June' => 'Junio',
              'July' => 'Julio',
              'August' => 'Agosto',
              'September' => 'Septiembre',
              'October' => 'Octubre',
              'November' => 'Noviembre',
              'December' => 'Diciembre'
            ];
            $diaEn = date('l');
            $mesEn = date('F');
            echo $dias[$diaEn] . ', ' . date('d') . ' de ' . $meses[$mesEn] . ' de ' . date('Y');
            ?>
      </span>
      <span class="time-badge" id="current-time">
        🕐 <?= date('h:i:s A') ?>
      </span>
    </div>
  </header>

  <script>
    // Actualizar la hora en tiempo real
    function updateTime() {
      const now = new Date();
      const hours = now.getHours();
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');
      const ampm = hours >= 12 ? 'PM' : 'AM';
      const displayHours = hours % 12 || 12;

      document.getElementById('current-time').innerHTML =
        `🕐 ${String(displayHours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
    }

    setInterval(updateTime, 1000);
    updateTime();
  </script>

  <!-- Sección: Métricas Principales -->
  <section class="dashboard-section">
    <h2 class="section-title">📊 Resumen del Día</h2>

    <div class="cards-grid">
      <!-- Ventas del Día -->
      <div class="metric-card ventas">
        <div class="metric-icon">💰</div>
        <div class="metric-label">Ventas del Día</div>
        <div class="metric-value">Q <?= number_format($ventasHoy['total_ventas'], 2) ?></div>
        <div class="metric-detail">
          <?= $ventasHoy['cantidad_ventas'] ?> ventas realizadas
        </div>
      </div>

      <!-- Efectivo en Caja -->
      <div class="metric-card efectivo">
        <div class="metric-icon">💵</div>
        <div class="metric-label">Efectivo en Caja</div>
        <div class="metric-value">Q <?= number_format($efectivoCaja, 2) ?></div>
        <div class="metric-detail">
          Solo billetes y monedas disponibles
        </div>
      </div>

      <!-- Gastos del Día -->
      <div class="metric-card gastos">
        <div class="metric-icon">📤</div>
        <div class="metric-label">Gastos del Día</div>
        <div class="metric-value">Q <?= number_format($gastosHoy['total_gastos'], 2) ?></div>
        <div class="metric-detail">
          <?= $gastosHoy['cantidad_gastos'] ?> gastos operativos
        </div>
      </div>

      <!-- Retiros del Día -->
      <div class="metric-card retiros">
        <div class="metric-icon">🏦</div>
        <div class="metric-label">Retiros del Día</div>
        <div class="metric-value">Q <?= number_format($retirosHoy['total_retiros'], 2) ?></div>
        <div class="metric-detail">
          <?= $retirosHoy['cantidad_retiros'] ?> retiros personales
        </div>
      </div>

      <!-- Ganancia Real -->
      <div class="metric-card ganancia">
        <div class="metric-icon"><?= $margenGanancia['ganancia_real'] >= 0 ? '📈' : '📉' ?></div>
        <div class="metric-label">Ganancia Real del Día</div>
        <div class="metric-value">Q <?= number_format($margenGanancia['ganancia_real'], 2) ?></div>
        <div class="metric-detail">
          Margen: <?= number_format($margenGanancia['porcentaje_margen'], 1) ?>%
          <span class="<?= $margenGanancia['ganancia_real'] >= 0 ? 'text-success' : 'text-danger' ?>">
            <?= $margenGanancia['ganancia_real'] >= 0 ? '(Rentable ✅)' : '(Pérdida ⚠️)' ?>
          </span>
        </div>
      </div>

      <!-- Ganancias del Mes -->
      <div class="metric-card ganancia-mes">
        <div class="metric-icon"><?= $gananciasMes['ganancias_mes'] >= 0 ? '📊' : '📉' ?></div>
        <div class="metric-label">Ganancias del Mes</div>
        <div class="metric-value">Q <?= number_format($gananciasMes['ganancias_mes'], 2) ?></div>
        <div class="metric-detail">
          Ventas: Q <?= number_format($gananciasMes['ventas_mes'], 2) ?> |
          Gastos: Q <?= number_format($gananciasMes['gastos_mes'], 2) ?> |
          Compras: Q <?= number_format($gananciasMes['compras_mes'], 2) ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección: Accesos Rápidos -->
  <section class="dashboard-section">
    <h2 class="section-title">🚀 Accesos Rápidos</h2>

    <div class="cards-grid">
      <a href="<?= url('/admin/usuarios') ?>" class="quick-access-card usuarios">
        <div class="metric-icon">👥</div>
        <div class="metric-label">Gestión de Usuarios</div>
        <div class="metric-detail">
          Crear y administrar usuarios del sistema
        </div>
      </a>

      <a href="<?= url('/admin/productos') ?>" class="quick-access-card productos">
        <div class="metric-icon">📦</div>
        <div class="metric-label">Catálogo de Productos</div>
        <div class="metric-detail">
          Gestionar inventario y catálogo
        </div>
      </a>

      <a href="<?= url('/admin/clientes') ?>" class="quick-access-card clientes">
        <div class="metric-icon">🧑‍💼</div>
        <div class="metric-label">Base de Clientes</div>
        <div class="metric-detail">
          Administrar información de clientes
        </div>
      </a>

      <a href="<?= url('/admin/ventas') ?>" class="quick-access-card ventas">
        <div class="metric-icon">💳</div>
        <div class="metric-label">Historial de Ventas</div>
        <div class="metric-detail">
          Ver y gestionar todas las ventas
        </div>
      </a>

      <a href="<?= url('/admin/pos') ?>" class="quick-access-card pos">
        <div class="metric-icon">💻</div>
        <div class="metric-label">Punto de Venta</div>
        <div class="metric-detail">
          Acceso directo al sistema POS
        </div>
      </a>

      <a href="<?= url('/admin/reportes') ?>" class="quick-access-card reportes">
        <div class="metric-icon">📈</div>
        <div class="metric-label">Reportes y Análisis</div>
        <div class="metric-detail">
          Informes detallados del negocio
        </div>
      </a>

      <a href="<?= url('/admin/proveedores') ?>" class="quick-access-card proveedores">
        <div class="metric-icon">🏢</div>
        <div class="metric-label">Proveedores</div>
        <div class="metric-detail">
          Gestionar información de proveedores
        </div>
      </a>
    </div>
  </section>

  <!-- Sección: Alertas y Avisos -->
  <section class="dashboard-section">
    <h2 class="section-title">⚠️ Alertas del Sistema</h2>

    <div class="cards-grid">
      <?php if ($alertas['productos_sin_stock'] > 0): ?>
        <div class="alert-card danger">
          <div class="alert-title">
            ❌ Productos Sin Stock
            <span class="badge-critical"><?= $alertas['productos_sin_stock'] ?></span>
          </div>
          <div class="alert-content">
            Hay productos agotados que no pueden venderse.
            <a href="<?= url('/admin/productos') ?>">Gestionar productos →</a>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($alertas['productos_bajo_stock'] > 0): ?>
        <div class="alert-card warning">
          <div class="alert-title">
            ⚠️ Stock Bajo
            <span class="badge-critical"><?= $alertas['productos_bajo_stock'] ?></span>
          </div>
          <div class="alert-content">
            Productos por debajo del stock mínimo.
            <a href="<?= url('/admin/productos') ?>">Gestionar productos →</a>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($alertas['ventas_por_cobrar_cantidad'] > 0): ?>
        <div class="alert-card info">
          <div class="alert-title">
            💳 Ventas Pendientes de Cobro
            <span class="badge-critical"><?= $alertas['ventas_por_cobrar_cantidad'] ?></span>
          </div>
          <div class="alert-content">
            Monto pendiente: Q <?= number_format($alertas['ventas_por_cobrar_monto'], 2) ?>
            <a href="<?= url('/admin/pos') ?>">Ver detalles →</a>
          </div>
        </div>
      <?php endif; ?>

      <?php
      $hasAlerts = $alertas['productos_sin_stock'] > 0 ||
        $alertas['productos_bajo_stock'] > 0 ||
        $alertas['ventas_por_cobrar_cantidad'] > 0;

      if (!$hasAlerts): ?>
        <div class="alert-card success">
          <div class="alert-title success">
            ✅ Todo en Orden
          </div>
          <div class="alert-content success">
            No hay alertas críticas en este momento. El negocio opera normalmente.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
</div>
