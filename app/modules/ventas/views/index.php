<?php
$ventas   = $ventas ?? [];
$kpis     = $kpis ?? [];
$success  = $success ?? null;
$error    = $error ?? null;
$busqueda = $_GET['busqueda'] ?? '';

// =======================
// Filtro por búsqueda
// =======================
$ventasFiltradas = $ventas;
if (!empty($busqueda)) {
  $ventasFiltradas = array_filter($ventas, function ($v) use ($busqueda) {
    $b = strtolower($busqueda);
    return
      strpos(strtolower($v['cliente_nombre'] ?? ''), $b) !== false ||
      strpos(strtolower($v['cliente_nit'] ?? ''), $b) !== false ||
      strpos(strtolower($v['cliente_telefono'] ?? ''), $b) !== false ||
      strpos((string)($v['id'] ?? ''), $b) !== false;
  });
}
?>

<div class="card">

  <!-- =======================
       KPIs
  ======================== -->
  <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;padding:1.5rem;background:linear-gradient(135deg,#0a3d91,#1565c0);">
    <div class="kpi">💰 TOTAL VENTAS <strong><?= $kpis['total_ventas'] ?? 0 ?></strong></div>
    <div class="kpi">✅ CONFIRMADAS <strong><?= $kpis['ventas_confirmadas'] ?? 0 ?></strong></div>
    <div class="kpi">📝 DE COTIZACIÓN <strong><?= $kpis['ventas_convertidas'] ?? 0 ?></strong></div>
    <div class="kpi">🧾 DE DEUDAS <strong><?= $kpis['ventas_desde_deudas'] ?? 0 ?></strong></div>
    <div class="kpi">💵 TOTAL CONFIRMADO <strong>Q <?= number_format($kpis['total_confirmado'] ?? 0, 2) ?></strong></div>
  </div>

  <!-- =======================
       HEADER
  ======================== -->
  <div class="card-header">
    <div>
      <h1>Ventas</h1>
      <p>Historial y gestión de ventas</p>
    </div>
    <a href="<?= url('/admin/ventas/crear') ?>" class="btn-primary">➕ Nueva Venta</a>
  </div>

  <!-- =======================
       MENSAJES
  ======================== -->
  <?php if ($success): ?>
    <div class="alert success"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
  <?php endif; ?>

  <!-- =======================
       BUSCADOR
  ======================== -->
  <div class="search-bar">
    <form method="GET" action="<?= url('/admin/ventas') ?>">
      <input
        type="text"
        name="busqueda"
        value="<?= e($busqueda) ?>"
        placeholder="🔍 Buscar por cliente, NIT, teléfono o ID">
      <button type="submit">Buscar</button>
      <?php if ($busqueda): ?>
        <a href="<?= url('/admin/ventas') ?>" class="btn-secondary">✖ Limpiar</a>
      <?php endif; ?>
    </form>
    <?php if ($busqueda): ?>
      <small>Resultados: <?= count($ventasFiltradas) ?></small>
    <?php endif; ?>
  </div>

  <!-- =======================
       TABLA
  ======================== -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th>Total</th>
          <th>Pagado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>

      <?php if ($ventasFiltradas): ?>
        <?php foreach ($ventasFiltradas as $v): ?>
          <?php
          $estado = $v['estado'] ?? '';
          $badge = match($estado) {
            'CONFIRMADA' => ['#d4edda','#155724','✅'],
            'ANULADA'    => ['#f8d7da','#721c24','❌'],
            default      => ['#e9ecef','#495057','❓'],
          };
          ?>
          <tr>
            <td><strong>#<?= e($v['id']) ?></strong></td>

            <td>
              <strong><?= e($v['cliente_nombre']) ?></strong><br>
              <small>📞 <?= e($v['cliente_telefono']) ?></small>
            </td>

            <!-- ✅ SOLO FECHA, SIN HORA -->
            <td><?= date('d/m/Y', strtotime($v['fecha_venta'])) ?></td>

            <td>
              <span style="background:<?= $badge[0] ?>;color:<?= $badge[1] ?>;padding:.25rem .75rem;border-radius:1rem;">
                <?= $badge[2] ?> <?= e($estado) ?>
              </span>
            </td>

            <td class="right">Q <?= number_format($v['total'],2) ?></td>
            <td class="right">Q <?= number_format($v['total_pagado'] ?? 0,2) ?></td>

            <td class="center">
              <a href="<?= url('/admin/ventas/ver?id='.$v['id']) ?>" class="btn-sm">👁️</a>

              <?php if ($estado === 'CONFIRMADA'): ?>
                <form method="POST" action="<?= url('/admin/ventas/anular') ?>" style="display:inline">
                  <input type="hidden" name="venta_id" value="<?= $v['id'] ?>">
                  <button type="submit" class="btn-sm danger"
                    onclick="return confirm('¿Anular esta venta?')">❌</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" class="empty">No hay ventas registradas</td>
        </tr>
      <?php endif; ?>

      </tbody>
    </table>
  </div>
</div>

<style>
.card{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.1)}
.card-header{display:flex;justify-content:space-between;align-items:center;padding:1.5rem}
.kpi{background:rgba(255,255,255,.15);padding:1rem;border-radius:8px;color:#fff}
.kpi strong{display:block;font-size:1.4rem}
.alert{margin:1rem;padding:1rem;border-radius:6px}
.alert.success{background:#d4edda;color:#155724}
.alert.error{background:#f8d7da;color:#721c24}
.search-bar{padding:1rem;background:#f8f9fa}
.search-bar form{display:flex;gap:.5rem}
.search-bar input{flex:1;padding:.5rem}
.btn-primary{background:#0a3d91;color:#fff;padding:.75rem 1.25rem;border-radius:6px;text-decoration:none}
.btn-secondary{background:#6c757d;color:#fff;padding:.5rem 1rem;border-radius:6px;text-decoration:none}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
th,td{padding:.75rem;border-bottom:1px solid #e9ecef}
th{text-align:left;background:#f8f9fa}
.right{text-align:right}
.center{text-align:center}
.btn-sm{padding:.4rem .6rem;border-radius:4px;text-decoration:none;background:#0a3d91;color:#fff}
.btn-sm.danger{background:#dc3545}
.empty{text-align:center;padding:2rem;color:#6c757d}
</style>