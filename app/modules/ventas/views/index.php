<?php
$ventas = $ventas ?? [];
$kpis = $kpis ?? [];
$success = $success ?? null;
$error = $error ?? null;
$busqueda = $_GET['busqueda'] ?? '';

// Filtrar ventas si hay búsqueda
$ventasFiltradas = $ventas;
if (!empty($busqueda)) {
  $ventasFiltradas = array_filter($ventas, function ($v) use ($busqueda) {
    $busquedaLower = strtolower($busqueda);
    $nombreCliente = strtolower($v['cliente_nombre'] ?? '');
    $nitCliente = strtolower($v['cliente_nit'] ?? '');
    $telefonoCliente = strtolower($v['cliente_telefono'] ?? '');

    return strpos($nombreCliente, $busquedaLower) !== false ||
      strpos($nitCliente, $busquedaLower) !== false ||
      strpos($telefonoCliente, $busquedaLower) !== false;
  });
}
?>

<div class="card">
  <!-- ESTADÍSTICAS -->
  <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; padding: 1.5rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); border-radius: 0.5rem 0.5rem 0 0;">
    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2);">
      <div style="color: rgba(255,255,255,0.9); font-size: 0.75rem; font-weight: 600;">💰 TOTAL VENTAS</div>
      <div style="color: white; font-size: 1.75rem; font-weight: 700;"><?= $kpis['total_ventas'] ?? 0 ?></div>
    </div>
    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2);">
      <div style="color: rgba(255,255,255,0.9); font-size: 0.75rem; font-weight: 600;">✅ CONFIRMADAS</div>
      <div style="color: white; font-size: 1.75rem; font-weight: 700;"><?= $kpis['ventas_confirmadas'] ?? 0 ?></div>
    </div>
    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2);">
      <div style="color: rgba(255,255,255,0.9); font-size: 0.75rem; font-weight: 600;">📝 DE COTIZACIÓN</div>
      <div style="color: white; font-size: 1.75rem; font-weight: 700;"><?= $kpis['ventas_convertidas'] ?? 0 ?></div>
    </div>
    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2);">
      <div style="color: rgba(255,255,255,0.9); font-size: 0.75rem; font-weight: 600;">🧾 DE DEUDAS</div>
      <div style="color: white; font-size: 1.75rem; font-weight: 700;"><?= $kpis['ventas_desde_deudas'] ?? 0 ?></div>
    </div>
    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2);">
      <div style="color: rgba(255,255,255,0.9); font-size: 0.75rem; font-weight: 600;">💵 TOTAL CONFIRMADO</div>
      <div style="color: white; font-size: 1.5rem; font-weight: 700;">Q <?= number_format($kpis['total_confirmado'] ?? 0, 2) ?></div>
    </div>
  </div>

  <!-- HEADER -->
  <div class="card-header" style="background: white; border-bottom: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1 class="card-title" style="color: #495057; font-size: 1.75rem; font-weight: 700; margin: 0;">Ventas</h1>
      <p class="card-subtitle" style="color: #6c757d; margin: 0.25rem 0 0 0;">Gestiona ventas y consulta el historial de transacciones</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
      <a href="<?= url('/admin/ventas/crear') ?>" class="btn btn-primary" style="background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; border: none; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none;">
        ➕ Nueva Venta
      </a>
    </div>
  </div>

  <!-- MENSAJES FLASH -->
  <?php if ($success): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.5rem; color: #155724;">
      ✅ <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
      ⚠️ <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <!-- BARRA DE BÚSQUEDA -->
  <div style="padding: 1.5rem; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
    <form method="GET" action="<?= url('/admin/ventas') ?>" id="busquedaForm" style="display: flex; gap: 0.75rem; max-width: 600px;">
      <div style="flex: 1; position: relative;">
        <input
          type="text"
          name="busqueda"
          id="busquedaInput"
          value="<?= htmlspecialchars($busqueda) ?>"
          placeholder="🔍 Buscar en tiempo real por nombre del cliente, NIT o teléfono..."
          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #dee2e6; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;"
          onfocus="this.style.borderColor='#0a3d91'"
          onblur="this.style.borderColor='#dee2e6'"
          oninput="busquedaEnTiempoReal(this.value)"
          autocomplete="off">
        <!-- Indicador de búsqueda -->
        <div id="indicadorBusqueda" style="display: none; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #0a3d91;">
          <div style="width: 16px; height: 16px; border: 2px solid #e9ecef; border-top: 2px solid #0a3d91; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        </div>
      </div>
      <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        Buscar
      </button>
      <?php if (!empty($busqueda)): ?>
        <a href="<?= url('/admin/ventas') ?>" style="padding: 0.75rem 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; display: flex; align-items: center;">
          ✖️ Limpiar
        </a>
      <?php endif; ?>
    </form>
    <?php if (!empty($busqueda)): ?>
      <p style="margin: 0.75rem 0 0 0; color: #6c757d; font-size: 0.9rem;">
        📊 Mostrando <?= count($ventasFiltradas) ?> resultado(s) para: <strong><?= htmlspecialchars($busqueda) ?></strong>
      </p>
    <?php endif; ?>
    <!-- Resultados de búsqueda en tiempo real -->
    <div id="resultadosBusqueda" style="margin-top: 0.75rem; color: #6c757d; font-size: 0.9rem; display: none;">
      <span id="contadorResultados"></span>
    </div>
  </div>

  <!-- TABLA -->
  <div class="card-body" style="padding: 0;">
    <div style="overflow-x: auto;">
      <table class="table" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
          <tr>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">ID</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Cliente</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Fecha</th>
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #495057;">Estado</th>
            <th style="padding: 1rem; text-align: right; font-weight: 600; color: #495057;">Total</th>
            <th style="padding: 1rem; text-align: right; font-weight: 600; color: #495057;">Pagado</th>
            <th style="padding: 1rem; text-align: right; font-weight: 600; color: #495057;">Saldo</th>
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #495057;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($ventasFiltradas)): ?>
            <?php foreach ($ventasFiltradas as $venta): ?>
              <?php
              $estadoBadge = [
                'CONFIRMADA' => ['bg' => '#d4edda', 'color' => '#155724', 'icon' => '✅'],
                'ANULADA' => ['bg' => '#f8d7da', 'color' => '#721c24', 'icon' => '❌'],
              ];
              $badge = $estadoBadge[$venta['estado']] ?? ['bg' => '#e9ecef', 'color' => '#495057', 'icon' => '❓'];

              $total = (float)($venta['total'] ?? 0);
              $pagado = (float)($venta['total_pagado'] ?? 0);
              $saldo = $total - $pagado;
              ?>
              <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                <td style="padding: 1rem; color: #495057; font-weight: 700;">#<?= e($venta['id']) ?></td>
                <td style="padding: 1rem; color: #495057;">
                  <div style="font-weight: 600;"><?= e($venta['cliente_nombre']) ?></div>
                  <div style="font-size: 0.85rem; color: #6c757d;">📞 <?= e($venta['cliente_telefono']) ?></div>
                  <?php if (!empty($venta['cotizacion_id'])): ?>
                    <div style="font-size: 0.8rem; color: #0a3d91; margin-top: 0.25rem;">
                      📝 De Cotización #<?= e($venta['cotizacion_id']) ?>
                    </div>
                  <?php endif; ?>
                  <?php if (!empty($venta['deuda_origen_id'])): ?>
                    <div style="font-size: 0.8rem; color: #dc3545; margin-top: 0.25rem;">
                      🧾 De Deuda #<?= e($venta['deuda_origen_id']) ?>
                    </div>
                  <?php endif; ?>
                  <?php if (!empty($venta['observaciones']) && strpos($venta['observaciones'], 'Deuda') !== false): ?>
                    <div style="font-size: 0.75rem; color: #6c757d; margin-top: 0.25rem; font-style: italic;">
                      💰 Venta generada automáticamente
                    </div>
                  <?php endif; ?>
                </td>
                <td style="padding: 1rem; color: #6c757d; font-size: 0.9rem;">
                  <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?>
                </td>
                <td style="padding: 1rem; text-align: center;">
                  <span style="display: inline-block; padding: 0.25rem 0.75rem; background: <?= $badge['bg'] ?>; color: <?= $badge['color'] ?>; border-radius: 1rem; font-size: 0.85rem; font-weight: 600;">
                    <?= $badge['icon'] ?> <?= e($venta['estado']) ?>
                  </span>
                </td>
                <td style="padding: 1rem; text-align: right; font-weight: 700; color: #28a745; font-size: 1.1rem;">
                  Q <?= number_format($total, 2) ?>
                </td>
                <td style="padding: 1rem; text-align: right; font-weight: 600; color: #0a3d91;">
                  Q <?= number_format($pagado, 2) ?>
                </td>
                <td style="padding: 1rem; text-align: right; font-weight: 600; color: <?= $saldo > 0 ? '#dc3545' : '#28a745' ?>;">
                  Q <?= number_format($saldo, 2) ?>
                </td>
                <td style="padding: 1rem; text-align: center;">
                  <div style="display: flex; gap: 0.5rem; justify-content: center;">
                    <a href="<?= url('/admin/ventas/ver?id=' . $venta['id']) ?>" style="padding: 0.5rem 0.75rem; background: #0a3d91; color: white; text-decoration: none; border-radius: 0.375rem; font-size: 0.85rem; font-weight: 600;" title="Ver Detalle">
                      👁️
                    </a>
                    <?php if ($venta['estado'] === 'CONFIRMADA'): ?>
                      <form method="POST" action="<?= url('/admin/ventas/anular') ?>" style="display: inline;" id="formAnular<?= $venta['id'] ?>">
                        <input type="hidden" name="venta_id" value="<?= $venta['id'] ?>">
                        <button type="button" onclick="mostrarModalAnular(<?= $venta['id'] ?>, '<?= addslashes($venta['cliente_nombre']) ?>', '<?= number_format($venta['total'], 2) ?>')" style="padding: 0.5rem 0.75rem; background: #dc3545; color: white; border: none; border-radius: 0.375rem; font-size: 0.85rem; font-weight: 600; cursor: pointer;" title="Anular Venta">
                          ❌
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="padding: 3rem; text-align: center; color: #6c757d;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
                <?php if (!empty($busqueda)): ?>
                  <p style="font-size: 1.1rem; margin: 0;">No se encontraron ventas que coincidan con "<?= htmlspecialchars($busqueda) ?>"</p>
                  <a href="<?= url('/admin/ventas') ?>" style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                    Ver todas las ventas
                  </a>
                <?php else: ?>
                  <p style="font-size: 1.1rem; margin: 0;">No hay ventas registradas.</p>
                  <a href="<?= url('/admin/ventas/crear') ?>" style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                    Crear primera venta
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal de Anular Venta -->
<div id="modalAnularVenta" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-icon">
        <svg width="28" height="28" fill="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
      </div>
      <h3 class="modal-title">¿Anular Venta?</h3>
    </div>
    <div class="modal-body">
      <p class="modal-message">¿Está seguro que desea anular esta venta?</p>
      <div class="venta-details">
        <div style="background: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin: 1rem 0; border: 1px solid #dee2e6;">
          <div style="margin-bottom: 0.5rem;"><strong>Cliente:</strong> <span id="clienteAnular"></span></div>
          <div><strong>Total:</strong> <span style="color: #dc3545; font-weight: 700;">Q <span id="totalAnular"></span></span></div>
        </div>
      </div>
      <p class="modal-submessage"><strong>⚠️ Esta acción revertirá el stock y actualizará el total del cliente. No se puede deshacer.</strong></p>
    </div>
    <div class="modal-actions">
      <button type="button" class="modal-btn modal-btn-cancel" onclick="cerrarModalAnular()">
        Cancelar
      </button>
      <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmarAnularVenta()">
        Sí, Anular Venta
      </button>
    </div>
  </div>
</div>

<style>
  .card {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background: white;
  }

  .table tr:last-child td {
    border-bottom: none;
  }

  /* Estilos para modales */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
  }

  .modal-overlay.show {
    display: flex;
    opacity: 1;
  }

  .modal-content {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    max-width: 500px;
    width: 90%;
    transform: translateY(-20px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 2px solid rgba(220, 53, 69, 0.1);
  }

  .modal-overlay.show .modal-content {
    transform: translateY(0) scale(1);
  }

  .modal-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    padding: 24px 28px;
    border-radius: 14px 14px 0 0;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .modal-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
  }

  @keyframes shimmer {

    0%,
    100% {
      transform: translate(-50%, -50%) rotate(0deg);
    }

    50% {
      transform: translate(-50%, -50%) rotate(180deg);
    }
  }

  .modal-icon {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    position: relative;
    z-index: 1;
  }

  .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
  }

  .modal-body {
    padding: 28px;
    text-align: center;
  }

  .modal-message {
    font-size: 1.1rem;
    color: #495057;
    line-height: 1.6;
    margin: 0 0 8px 0;
    font-weight: 500;
  }

  .modal-submessage {
    font-size: 0.95rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
  }

  .modal-actions {
    padding: 0 28px 28px;
    display: flex;
    gap: 12px;
    justify-content: center;
  }

  .modal-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-width: 120px;
  }

  .modal-btn-cancel {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #e9ecef;
  }

  .modal-btn-cancel:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    transform: translateY(-1px);
  }

  .modal-btn-confirm {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
  }

  .modal-btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
  }

  .modal-btn-confirm:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
  }

  /* Animaciones para búsqueda */
  @keyframes spin {
    0% {
      transform: translateY(-50%) rotate(0deg);
    }

    100% {
      transform: translateY(-50%) rotate(360deg);
    }
  }

  #busquedaInput:focus {
    box-shadow: 0 0 0 3px rgba(10, 61, 145, 0.1);
    border-color: #0a3d91;
  }

  #resultadosBusqueda {
    animation: slideIn 0.3s ease-out;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>

<script>
  // Variables globales para modal de anular
  let ventaIdAnular = null;

  function mostrarModalAnular(ventaId, clienteNombre, total) {
    ventaIdAnular = ventaId;

    // Establecer datos en el modal
    document.getElementById('clienteAnular').textContent = clienteNombre;
    document.getElementById('totalAnular').textContent = total;

    const modal = document.getElementById('modalAnularVenta');
    modal.classList.add('show');

    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';

    // Cerrar con ESC
    document.addEventListener('keydown', cerrarAnularConEscape);

    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        cerrarModalAnular();
      }
    });
  }

  function cerrarModalAnular() {
    const modal = document.getElementById('modalAnularVenta');
    modal.classList.remove('show');

    // Restaurar scroll del body
    document.body.style.overflow = '';

    // Remover listeners
    document.removeEventListener('keydown', cerrarAnularConEscape);

    // Limpiar variables
    ventaIdAnular = null;
  }

  function cerrarAnularConEscape(e) {
    if (e.key === 'Escape') {
      cerrarModalAnular();
    }
  }

  function confirmarAnularVenta() {
    if (ventaIdAnular) {
      // Enviar el formulario correspondiente
      document.getElementById('formAnular' + ventaIdAnular).submit();
    }
  }

  // Variables y funciones para búsqueda en tiempo real
  let timerBusqueda = null;
  let todasLasVentas = [];

  // Cargar todas las ventas al inicio
  document.addEventListener('DOMContentLoaded', function() {
    cargarTodasLasVentas();
  });

  function cargarTodasLasVentas() {
    const filas = document.querySelectorAll('tbody tr');
    todasLasVentas = [];

    filas.forEach(fila => {
      const celdas = fila.querySelectorAll('td');
      if (celdas.length > 1) {
        const venta = {
          id: celdas[0].textContent.trim(),
          clienteNombre: celdas[1].querySelector('div:first-child')?.textContent || '',
          clienteTelefono: celdas[1].querySelector('div:nth-child(2)')?.textContent || '',
          fecha: celdas[2].textContent.trim(),
          estado: celdas[3].textContent.trim(),
          total: celdas[4].textContent.trim(),
          pagado: celdas[5].textContent.trim(),
          saldo: celdas[6].textContent.trim(),
          filaHTML: fila.outerHTML
        };
        todasLasVentas.push(venta);
      }
    });
  }

  function busquedaEnTiempoReal(termino) {
    const indicador = document.getElementById('indicadorBusqueda');
    indicador.style.display = 'block';

    if (timerBusqueda) {
      clearTimeout(timerBusqueda);
    }

    timerBusqueda = setTimeout(() => {
      realizarBusquedaVentas(termino);
      indicador.style.display = 'none';
    }, 300);
  }

  function realizarBusquedaVentas(termino) {
    const tbody = document.querySelector('tbody');
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    const contadorResultados = document.getElementById('contadorResultados');

    if (!termino || termino.trim() === '') {
      tbody.innerHTML = todasLasVentas.map(venta => venta.filaHTML).join('');
      resultadosDiv.style.display = 'none';
      reactivarEventosFilas();
      return;
    }

    const terminoBusqueda = termino.toLowerCase().trim();
    const ventasFiltradas = todasLasVentas.filter(venta => {
      return venta.clienteNombre.toLowerCase().includes(terminoBusqueda) ||
        venta.clienteTelefono.toLowerCase().includes(terminoBusqueda) ||
        venta.id.toLowerCase().includes(terminoBusqueda);
    });

    if (ventasFiltradas.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="8" style="padding: 2rem; text-align: center; color: #6c757d; font-style: italic;">
            🔍 No se encontraron ventas que coincidan con: <strong>"${termino}"</strong>
            <br><small style="margin-top: 0.5rem; display: block;">Intenta con otro término de búsqueda</small>
          </td>
        </tr>
      `;
    } else {
      tbody.innerHTML = ventasFiltradas.map(venta => venta.filaHTML).join('');
    }

    const plural = ventasFiltradas.length === 1 ? 'resultado' : 'resultados';
    contadorResultados.innerHTML = `🔍 ${ventasFiltradas.length} ${plural} encontrado(s) para: <strong>"${termino}"</strong>`;
    resultadosDiv.style.display = 'block';

    reactivarEventosFilas();
  }

  function reactivarEventosFilas() {
    const filas = document.querySelectorAll('tbody tr');
    filas.forEach(fila => {
      fila.addEventListener('mouseenter', function() {
        this.style.background = '#f8f9fa';
      });
      fila.addEventListener('mouseleave', function() {
        this.style.background = 'white';
      });
    });
  }

  // Limpiar búsqueda con ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const input = document.getElementById('busquedaInput');
      if (input === document.activeElement) {
        input.value = '';
        realizarBusquedaVentas('');
      }
    }
  });
</script>

<!-- Ejecutar en tu base de datos
source app/sql/migracion_deuda_to_venta.sql; -->
