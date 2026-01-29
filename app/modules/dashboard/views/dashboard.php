<?php
// ===============================
// Dashboard View - Comercializadora Sosa
// ===============================

// Inicializar variables con valores por defecto
$ventasHoy = $ventasHoy ?? ['cantidad_ventas' => 0, 'total_ventas' => 0];
$efectivoCaja = $efectivoCaja ?? 0;
$gastosHoy = $gastosHoy ?? ['cantidad_gastos' => 0, 'total_gastos' => 0];
$retirosHoy = $retirosHoy ?? ['cantidad_retiros' => 0, 'total_retiros' => 0];

$margenGanancia = $margenGanancia ?? [
  'ganancia_real' => 0,
  'porcentaje_margen' => 0,
  'ganancia_bruta' => 0,
  'ventas_dia' => 0,
  'cogs_dia' => 0,
  'gastos_dia' => 0,
  // alias esperado por la view
  'reversas_dia' => 0,
  // por si viene con llaves nuevas informativas
  'reversas_ventas_dia' => 0,
  'reversas_cogs_dia' => 0,
];

$gananciasMes = $gananciasMes ?? [
  'ventas_mes' => 0,
  'costo_ventas_mes' => 0,     // COGS
  'gastos_mes' => 0,           // gastos operativos
  // alias esperado por la view
  'reversas_mes' => 0,
  // llaves informativas del modelo
  'reversas_ventas_mes' => 0,
  'reversas_cogs_mes' => 0,
  'ganancia_bruta_mes' => 0,   // ventas - cogs
  'ganancias_mes' => 0         // ganancia neta (ventas - cogs - gastos)
];

$alertas = $alertas ?? [
  'productos_bajo_stock' => 0,
  'productos_sin_stock' => 0,
  'ventas_por_cobrar_cantidad' => 0,
  'ventas_por_cobrar_monto' => 0
];

// ✅ Fallbacks de reversas para evitar “0” por mismatch de keys
$reversasDiaMostrar = (float)($margenGanancia['reversas_dia']
  ?? $margenGanancia['reversas_ventas_dia']
  ?? 0);

$reversasMesMostrar = (float)($gananciasMes['reversas_mes']
  ?? $gananciasMes['reversas_ventas_mes']
  ?? 0);

// Cargar CSS específico del dashboard
?>
<link rel="stylesheet" href="<?= url('/assets/css/dashboard.css') ?>">

<div class="dashboard-container">
  <!-- Header del Dashboard -->
  <header class="dashboard-header">
    <h1>👋 Bienvenido, <?= htmlspecialchars($user["nombre"] ?? 'Usuario') ?></h1>
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
            echo ($dias[$diaEn] ?? $diaEn) . ', ' . date('d') . ' de ' . ($meses[$mesEn] ?? $mesEn) . ' de ' . date('Y');
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

      const el = document.getElementById('current-time');
      if (el) {
        el.innerHTML = `🕐 ${String(displayHours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
      }
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
        <div class="metric-value">Q <?= number_format((float)($ventasHoy['total_ventas'] ?? 0), 2) ?></div>
        <div class="metric-detail">
          <?= (int)($ventasHoy['cantidad_ventas'] ?? 0) ?> ventas realizadas
        </div>
      </div>

      <!-- Efectivo en Caja -->
      <div class="metric-card efectivo">
        <div class="metric-icon">💵</div>
        <div class="metric-label">Efectivo en Caja</div>
        <div class="metric-value">Q <?= number_format((float)$efectivoCaja, 2) ?></div>
        <div class="metric-detail">
          Solo billetes y monedas disponibles
        </div>
      </div>

      <!-- Gastos del Día -->
      <div class="metric-card gastos">
        <div class="metric-icon">📤</div>
        <div class="metric-label">Gastos del Día</div>
        <div class="metric-value">Q <?= number_format((float)($gastosHoy['total_gastos'] ?? 0), 2) ?></div>
        <div class="metric-detail">
          <?= (int)($gastosHoy['cantidad_gastos'] ?? 0) ?> gastos operativos
        </div>
      </div>

      <!-- Retiros del Día -->
      <div class="metric-card retiros">
        <div class="metric-icon">🏦</div>
        <div class="metric-label">Retiros del Día</div>
        <div class="metric-value">Q <?= number_format((float)($retirosHoy['total_retiros'] ?? 0), 2) ?></div>
        <div class="metric-detail">
          <?= (int)($retirosHoy['cantidad_retiros'] ?? 0) ?> retiros personales
        </div>
      </div>

      <!-- Ganancia Real del Día -->
      <div class="metric-card ganancia">
        <div class="metric-icon"><?= ((float)($margenGanancia['ganancia_real'] ?? 0)) >= 0 ? '📈' : '📉' ?></div>
        <div class="metric-label">Ganancia Real del Día</div>

        <div class="metric-value">Q <?= number_format((float)($margenGanancia['ganancia_real'] ?? 0), 2) ?></div>

        <div class="metric-detail">
          Margen: <?= number_format((float)($margenGanancia['porcentaje_margen'] ?? 0), 1) ?>%
          <span class="<?= ((float)($margenGanancia['ganancia_real'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' ?>">
            <?= ((float)($margenGanancia['ganancia_real'] ?? 0)) >= 0 ? '(Rentable ✅)' : '(Pérdida ⚠️)' ?>
          </span>
          <br>
          Ventas: Q <?= number_format((float)($margenGanancia['ventas_dia'] ?? 0), 2) ?> |
          Costo: Q <?= number_format((float)($margenGanancia['cogs_dia'] ?? 0), 2) ?> |
          Gastos: Q <?= number_format((float)($margenGanancia['gastos_dia'] ?? 0), 2) ?> |
          Reversas: Q <?= number_format($reversasDiaMostrar, 2) ?>
          <br>
          Ganancia Bruta: Q <?= number_format((float)($margenGanancia['ganancia_bruta'] ?? 0), 2) ?>
        </div>
      </div>

      <!-- Ganancias del Mes -->
      <div class="metric-card ganancia-mes">
        <div class="metric-icon"><?= ((float)($gananciasMes['ganancias_mes'] ?? 0)) >= 0 ? '📊' : '📉' ?></div>
        <div class="metric-label">Ganancia Neta del Mes</div>

        <div class="metric-value">Q <?= number_format((float)($gananciasMes['ganancias_mes'] ?? 0), 2) ?></div>

        <div class="metric-detail">
          Ventas: Q <?= number_format((float)($gananciasMes['ventas_mes'] ?? 0), 2) ?> |
          Costo: Q <?= number_format((float)($gananciasMes['costo_ventas_mes'] ?? 0), 2) ?> |
          Gastos: Q <?= number_format((float)($gananciasMes['gastos_mes'] ?? 0), 2) ?> |
          Reversas: Q <?= number_format($reversasMesMostrar, 2) ?>
          <br>
          Ganancia Bruta: Q <?= number_format((float)($gananciasMes['ganancia_bruta_mes'] ?? 0), 2) ?>
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
      <?php if ((int)($alertas['productos_sin_stock'] ?? 0) > 0): ?>
        <div class="alert-card danger">
          <div class="alert-title">
            ❌ Productos Sin Stock
            <span class="badge-critical"><?= (int)($alertas['productos_sin_stock'] ?? 0) ?></span>
          </div>
          <div class="alert-content">
            Hay productos agotados que no pueden venderse.
            <a href="<?= url('/admin/productos') ?>">Gestionar productos →</a>
          </div>
        </div>
      <?php endif; ?>

      <?php if ((int)($alertas['productos_bajo_stock'] ?? 0) > 0): ?>
        <div class="alert-card warning">
          <div class="alert-title">
            ⚠️ Stock Bajo
            <span class="badge-critical"><?= (int)($alertas['productos_bajo_stock'] ?? 0) ?></span>
          </div>
          <div class="alert-content">
            Productos por debajo del stock mínimo.
            <a href="<?= url('/admin/productos') ?>">Gestionar productos →</a>
          </div>
        </div>
      <?php endif; ?>

      <?php if ((int)($alertas['ventas_por_cobrar_cantidad'] ?? 0) > 0): ?>
        <div class="alert-card info">
          <div class="alert-title">
            💳 Ventas Pendientes de Cobro
            <span class="badge-critical"><?= (int)($alertas['ventas_por_cobrar_cantidad'] ?? 0) ?></span>
          </div>
          <div class="alert-content">
            Monto pendiente: Q <?= number_format((float)($alertas['ventas_por_cobrar_monto'] ?? 0), 2) ?>
            <a href="<?= url('/admin/pos') ?>">Ver detalles →</a>
          </div>
        </div>
      <?php endif; ?>

      <?php
      $hasAlerts = ((int)($alertas['productos_sin_stock'] ?? 0) > 0) ||
        ((int)($alertas['productos_bajo_stock'] ?? 0) > 0) ||
        ((int)($alertas['ventas_por_cobrar_cantidad'] ?? 0) > 0);

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
