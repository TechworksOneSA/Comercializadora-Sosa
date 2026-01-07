<?php
$busqueda = $_GET['busqueda'] ?? '';

// Filtrar cotizaciones si hay búsqueda
$cotizacionesFiltradas = $cotizaciones;
if (!empty($busqueda)) {
  $cotizacionesFiltradas = array_filter($cotizaciones, function ($cot) use ($busqueda) {
    $busquedaLower = strtolower($busqueda);
    $nombreCliente = strtolower($cot['cliente_nombre'] ?? '');
    $nitCliente = strtolower($cot['cliente_nit'] ?? '');
    $telefonoCliente = strtolower($cot['cliente_telefono'] ?? '');

    return strpos($nombreCliente, $busquedaLower) !== false ||
      strpos($nitCliente, $busquedaLower) !== false ||
      strpos($telefonoCliente, $busquedaLower) !== false;
  });
}
?>

<div class="card">
  <!-- ESTADÍSTICAS -->
  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; padding: 1.5rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); border-radius: 0.5rem 0.5rem 0 0;">
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Total</div>
      <div style="color: white; font-size: 2rem; font-weight: 700; line-height: 1;"><?= $stats['total_cotizaciones'] ?? 0 ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Cotizaciones</div>
    </div>
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Activas</div>
      <div style="color: white; font-size: 2rem; font-weight: 700; line-height: 1;"><?= $stats['activas'] ?? 0 ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Vigentes</div>
    </div>
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Vencidas</div>
      <div style="color: white; font-size: 2rem; font-weight: 700; line-height: 1;"><?= $stats['vencidas'] ?? 0 ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Expiradas</div>
    </div>
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Valor</div>
      <div style="color: white; font-size: 1.5rem; font-weight: 700; line-height: 1;">Q <?= number_format($stats['total_activas'] ?? 0, 2) ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Activas</div>
    </div>
  </div>

  <!-- HEADER -->
  <div class="card-header" style="background: white; border-bottom: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1 class="card-title" style="color: #495057; font-size: 1.75rem; font-weight: 700; margin: 0;">Cotizaciones</h1>
      <p class="card-subtitle" style="color: #6c757d; margin: 0.25rem 0 0 0;">Gestiona cotizaciones y conviértelas en ventas</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
      <?php if (($stats['vencidas'] ?? 0) > 0): ?>
        <form method="POST" action="<?= url('/admin/cotizaciones/limpiar-vencidas') ?>" style="display: inline;" id="formLimpiarVencidas">
          <button type="button" onclick="mostrarModalConfirmacion()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: #dc3545; color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Limpiar Vencidas
          </button>
        </form>
      <?php endif; ?>
      <a href="<?= url('/admin/cotizaciones/crear') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.875rem; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(10, 61, 145, 0.2);">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva Cotización
      </a>
    </div>
  </div>

  <!-- MENSAJES FLASH -->
  <?php if ($success): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 0.375rem; color: #2e7d32; display: flex; align-items: center; gap: 0.75rem;">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span><?= e($success) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
      ⚠️ <?= e($error) ?>
    </div>
  <?php endif; ?>

  <!-- BARRA DE BÚSQUEDA -->
  <div style="padding: 1.5rem; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
    <form method="GET" action="<?= url('/admin/cotizaciones') ?>" id="busquedaForm" style="display: flex; gap: 0.75rem; max-width: 600px;">
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
        <a href="<?= url('/admin/cotizaciones') ?>" style="padding: 0.75rem 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; display: flex; align-items: center;">
          ✖️ Limpiar
        </a>
      <?php endif; ?>
    </form>
    <?php if (!empty($busqueda)): ?>
      <p style="margin: 0.75rem 0 0 0; color: #6c757d; font-size: 0.9rem;">
        📊 Mostrando <?= count($cotizacionesFiltradas) ?> resultado(s) para: <strong><?= htmlspecialchars($busqueda) ?></strong>
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
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #495057;">Vence En</th>
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #495057;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($cotizacionesFiltradas)): ?>
            <?php foreach ($cotizacionesFiltradas as $cot): ?>
              <?php
              $estadoBadge = [
                'ACTIVA' => ['bg' => '#e8f5e9', 'color' => '#2e7d32', 'dot' => '#4caf50'],
                'VENCIDA' => ['bg' => '#ffebee', 'color' => '#c62828', 'dot' => '#ef5350'],
                'CONVERTIDA' => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'dot' => '#2196f3'],
              ];
              $badge = $estadoBadge[$cot['estado']] ?? ['bg' => '#f5f5f5', 'color' => '#616161', 'dot' => '#9e9e9e'];

              $diasRestantes = (int)$cot['dias_restantes'];
              $venceTexto = $diasRestantes > 0 ? "{$diasRestantes} día(s)" : "HOY";
              $venceColor = $diasRestantes <= 2 ? '#dc3545' : ($diasRestantes <= 5 ? '#ffc107' : '#28a745');
              ?>
              <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                <td style="padding: 1rem; color: #495057; font-weight: 700;">#<?= e($cot['id']) ?></td>
                <td style="padding: 1rem; color: #495057;">
                  <div style="font-weight: 600;"><?= e($cot['cliente_nombre']) ?></div>
                  <div style="font-size: 0.85rem; color: #6c757d;">📞 <?= e($cot['cliente_telefono']) ?></div>
                </td>
                <td style="padding: 1rem; color: #6c757d; font-size: 0.9rem;">
                  <?= date('d/m/Y', strtotime($cot['fecha'])) ?>
                </td>
                <td style="padding: 1rem; text-align: center;">
                  <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.85rem; background: <?= $badge['bg'] ?>; color: <?= $badge['color'] ?>; border-radius: 0.35rem; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.02em;">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: <?= $badge['dot'] ?>;"></span>
                    <?= e($cot['estado']) ?>
                  </span>
                </td>
                <td style="padding: 1rem; text-align: right; font-weight: 700; color: #28a745; font-size: 1.1rem;">
                  Q <?= number_format($cot['total'], 2) ?>
                </td>
                <td style="padding: 1rem; text-align: center;">
                  <?php if ($cot['estado'] === 'ACTIVA'): ?>
                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; background: rgba(<?= $venceColor === '#28a745' ? '40, 167, 69' : ($venceColor === '#ffc107' ? '255, 193, 7' : '220, 53, 69') ?>, 0.12); color: <?= $venceColor ?>; border-radius: 0.35rem; font-weight: 600; font-size: 0.8rem; border: 1px solid rgba(<?= $venceColor === '#28a745' ? '40, 167, 69' : ($venceColor === '#ffc107' ? '255, 193, 7' : '220, 53, 69') ?>, 0.2);">
                      <?= $venceTexto ?>
                    </span>
                  <?php else: ?>
                    <span style="color: #bdbdbd; font-size: 0.85rem;">—</span>
                  <?php endif; ?>
                </td>
                <td style="padding: 1rem; text-align: center;">
                  <div style="display: flex; gap: 0.5rem; justify-content: center;">
                    <a href="<?= url('/admin/cotizaciones/ver/' . $cot['id']) ?>" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem; background: #0a3d91; color: white; text-decoration: none; border-radius: 0.375rem; transition: all 0.2s;" title="Ver detalles">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </a>
                    <?php if ($cot['estado'] === 'ACTIVA'): ?>
                      <form method="POST" action="<?= url('/admin/cotizaciones/convertir/' . $cot['id']) ?>" style="display: inline;" class="form-convertir" data-cotizacion-id="<?= $cot['id'] ?>">
                        <button type="button" onclick="mostrarModalConvertir(<?= $cot['id'] ?>)" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem; background: #28a745; color: white; border: none; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s;" title="Convertir a venta">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          </svg>
                        </button>
                      </form>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('/admin/cotizaciones/eliminar/' . $cot['id']) ?>" style="display: inline;" id="formEliminar<?= $cot['id'] ?>">
                      <button type="button" onclick="mostrarModalEliminar(<?= $cot['id'] ?>, '<?= addslashes($cot['cliente_nombre']) ?>')" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem; background: #dc3545; color: white; border: none; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s;" title="Eliminar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="padding: 4rem; text-align: center; color: #757575;">
                <svg class="w-16 h-16 mx-auto mb-4" style="color: #bdbdbd;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <?php if (!empty($busqueda)): ?>
                  <p style="font-size: 1.1rem; font-weight: 500; margin: 0 0 0.5rem 0; color: #424242;">Sin resultados</p>
                  <p style="margin: 0 0 1.5rem 0;">No se encontraron cotizaciones que coincidan con "<?= htmlspecialchars($busqueda) ?>"</p>
                  <a href="<?= url('/admin/cotizaciones') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #757575; color: white; text-decoration: none; border-radius: 0.375rem; font-weight: 600; transition: all 0.2s;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Ver todas las cotizaciones
                  </a>
                <?php else: ?>
                  <p style="font-size: 1.1rem; font-weight: 500; margin: 0 0 0.5rem 0; color: #424242;">No hay cotizaciones registradas</p>
                  <p style="margin: 0 0 1.5rem 0;">Comienza creando tu primera cotización</p>
                  <a href="<?= url('/admin/cotizaciones/crear') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; text-decoration: none; border-radius: 0.375rem; font-weight: 600; box-shadow: 0 2px 4px rgba(10, 61, 145, 0.2); transition: all 0.2s;">
                    Crear primera cotización
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

<style>
  .card {
    animation: fadeIn 0.4s ease-in;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Modal de confirmación */
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
    max-width: 450px;
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
</style>

<!-- Modal de Confirmación -->
<div id="modalConfirmacion" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-icon">
        <svg width="28" height="28" fill="white" viewBox="0 0 24 24">
          <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </div>
      <h3 class="modal-title">¿Eliminar Cotizaciones Vencidas?</h3>
    </div>
    <div class="modal-body">
      <p class="modal-message">Esta acción eliminará todas las cotizaciones vencidas de forma permanente.</p>
      <p class="modal-submessage">Se eliminarán <strong><?= $stats['vencidas'] ?? 0 ?> cotizaciones</strong>. Esta acción no se puede deshacer.</p>
    </div>
    <div class="modal-actions">
      <button type="button" class="modal-btn modal-btn-cancel" onclick="cerrarModalConfirmacion()">
        Cancelar
      </button>
      <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmarLimpiarVencidas()">
        Sí, Eliminar
      </button>
    </div>
  </div>
</div>

<!-- Modal de Eliminar Cotización Individual -->
<div id="modalEliminarCotizacion" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-icon">
        <svg width="28" height="28" fill="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </div>
      <h3 class="modal-title">¿Eliminar Cotización?</h3>
    </div>
    <div class="modal-body">
      <p class="modal-message">Estás a punto de eliminar la cotización de <strong id="nombreClienteEliminar"></strong>.</p>
      <p class="modal-submessage">Esta acción no se puede deshacer y toda la información se perderá permanentemente.</p>
    </div>
    <div class="modal-actions">
      <button type="button" class="modal-btn modal-btn-cancel" onclick="cerrarModalEliminar()">
        Cancelar
      </button>
      <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmarEliminarCotizacion()">
        Sí, Eliminar
      </button>
    </div>
  </div>
</div>

<script>
  function mostrarModalConfirmacion() {
    const modal = document.getElementById('modalConfirmacion');
    modal.classList.add('show');

    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';

    // Cerrar con ESC
    document.addEventListener('keydown', cerrarConEscape);

    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        cerrarModalConfirmacion();
      }
    });
  }

  function cerrarModalConfirmacion() {
    const modal = document.getElementById('modalConfirmacion');
    modal.classList.remove('show');

    // Restaurar scroll del body
    document.body.style.overflow = '';

    // Remover listeners
    document.removeEventListener('keydown', cerrarConEscape);
  }

  function cerrarConEscape(e) {
    if (e.key === 'Escape') {
      cerrarModalConfirmacion();
    }
  }

  function confirmarLimpiarVencidas() {
    // Enviar el formulario
    document.getElementById('formLimpiarVencidas').submit();
  }

  // Variables globales para el modal de eliminar cotización
  let cotizacionIdEliminar = null;

  function mostrarModalEliminar(cotizacionId, nombreCliente) {
    cotizacionIdEliminar = cotizacionId;

    // Establecer el nombre del cliente en el modal
    document.getElementById('nombreClienteEliminar').textContent = nombreCliente;

    const modal = document.getElementById('modalEliminarCotizacion');
    modal.classList.add('show');

    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';

    // Cerrar con ESC
    document.addEventListener('keydown', cerrarEliminarConEscape);

    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        cerrarModalEliminar();
      }
    });
  }

  function cerrarModalEliminar() {
    const modal = document.getElementById('modalEliminarCotizacion');
    modal.classList.remove('show');

    // Restaurar scroll del body
    document.body.style.overflow = '';

    // Remover listeners
    document.removeEventListener('keydown', cerrarEliminarConEscape);

    // Limpiar variables
    cotizacionIdEliminar = null;
  }

  function cerrarEliminarConEscape(e) {
    if (e.key === 'Escape') {
      cerrarModalEliminar();
    }
  }

  function confirmarEliminarCotizacion() {
    if (cotizacionIdEliminar) {
      // Enviar el formulario correspondiente
      document.getElementById('formEliminar' + cotizacionIdEliminar).submit();
    }
  }

  // Variables y funciones para búsqueda en tiempo real
  let timerBusqueda = null;
  let todasLasCotizaciones = []; // Array para almacenar todas las cotizaciones

  // Cargar todas las cotizaciones al inicio
  document.addEventListener('DOMContentLoaded', function() {
    cargarTodasLasCotizaciones();
  });

  function cargarTodasLasCotizaciones() {
    // Extraer datos de las filas existentes
    const filas = document.querySelectorAll('tbody tr');
    todasLasCotizaciones = [];

    filas.forEach(fila => {
      const celdas = fila.querySelectorAll('td');
      if (celdas.length > 1) {
        const cotizacion = {
          id: celdas[0].textContent.trim(),
          clienteNombre: celdas[1].querySelector('div:first-child')?.textContent || '',
          clienteTelefono: celdas[1].querySelector('div:last-child')?.textContent || '',
          fecha: celdas[2].textContent.trim(),
          estado: celdas[3].textContent.trim(),
          total: celdas[4].textContent.trim(),
          vence: celdas[5].textContent.trim(),
          filaHTML: fila.outerHTML
        };
        todasLasCotizaciones.push(cotizacion);
      }
    });
  }

  function busquedaEnTiempoReal(termino) {
    // Mostrar indicador de búsqueda
    const indicador = document.getElementById('indicadorBusqueda');
    indicador.style.display = 'block';

    // Limpiar timer anterior
    if (timerBusqueda) {
      clearTimeout(timerBusqueda);
    }

    // Establecer nuevo timer
    timerBusqueda = setTimeout(() => {
      realizarBusqueda(termino);
      indicador.style.display = 'none';
    }, 300); // Esperar 300ms antes de buscar
  }

  function realizarBusqueda(termino) {
    const tbody = document.querySelector('tbody');
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    const contadorResultados = document.getElementById('contadorResultados');

    // Si no hay término de búsqueda, mostrar todas las cotizaciones
    if (!termino || termino.trim() === '') {
      tbody.innerHTML = todasLasCotizaciones.map(cot => cot.filaHTML).join('');
      resultadosDiv.style.display = 'none';
      return;
    }

    // Filtrar cotizaciones
    const terminoBusqueda = termino.toLowerCase().trim();
    const cotizacionesFiltradas = todasLasCotizaciones.filter(cot => {
      return cot.clienteNombre.toLowerCase().includes(terminoBusqueda) ||
        cot.clienteTelefono.toLowerCase().includes(terminoBusqueda) ||
        cot.id.toLowerCase().includes(terminoBusqueda);
    });

    // Mostrar resultados filtrados
    if (cotizacionesFiltradas.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" style="padding: 2rem; text-align: center; color: #6c757d; font-style: italic;">
            🔍 No se encontraron cotizaciones que coincidan con: <strong>"${termino}"</strong>
            <br><small style="margin-top: 0.5rem; display: block;">Intenta con otro término de búsqueda</small>
          </td>
        </tr>
      `;
    } else {
      tbody.innerHTML = cotizacionesFiltradas.map(cot => cot.filaHTML).join('');
    }

    // Mostrar contador de resultados
    const plural = cotizacionesFiltradas.length === 1 ? 'resultado' : 'resultados';
    contadorResultados.innerHTML = `🔍 ${cotizacionesFiltradas.length} ${plural} encontrado(s) para: <strong>"${termino}"</strong>`;
    resultadosDiv.style.display = 'block';

    // Reactivar eventos de hover en las nuevas filas
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

  // Limpiar búsqueda cuando se presiona ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const input = document.getElementById('busquedaInput');
      if (input === document.activeElement) {
        input.value = '';
        realizarBusqueda('');
      }
    }
  });
</script>

<!-- Estilos adicionales para la animación del indicador -->
<style>
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

  /* Mejorar la experiencia visual de la búsqueda */
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

  /* Resaltar texto encontrado */
  .highlight {
    background: linear-gradient(120deg, #fef7cd 0%, #fff3a0 100%);
    padding: 0.1rem 0.2rem;
    border-radius: 0.2rem;
    font-weight: 600;
  }
</style>

<!-- Modal de Confirmación para Convertir a Venta -->
<div id="modal-convertir" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
  <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3); max-width: 400px; width: 90%; text-align: center;">
    <div style="margin-bottom: 1.5rem;">
      <div style="width: 60px; height: 60px; background: #28a745; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
        <svg style="width: 30px; height: 30px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <h3 style="margin: 0; color: #333; font-size: 1.25rem; font-weight: 600;">Convertir a Venta</h3>
      <p style="margin: 0.5rem 0 0 0; color: #666;">¿Está seguro que desea convertir esta cotización a venta? Esta acción no se puede deshacer.</p>
    </div>
    <div style="display: flex; gap: 1rem; justify-content: center;">
      <button onclick="cerrarModalConvertir()" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
        Cancelar
      </button>
      <button onclick="confirmarConversion()" style="padding: 0.75rem 1.5rem; background: #28a745; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
        Confirmar
      </button>
    </div>
  </div>
</div>

<script>
  let cotizacionIdActual = null;

  function mostrarModalConvertir(cotizacionId) {
    cotizacionIdActual = cotizacionId;
    const modal = document.getElementById('modal-convertir');
    modal.style.display = 'flex';
  }

  function cerrarModalConvertir() {
    const modal = document.getElementById('modal-convertir');
    modal.style.display = 'none';
    cotizacionIdActual = null;
  }

  function confirmarConversion() {
    if (cotizacionIdActual) {
      const form = document.querySelector(`form[data-cotizacion-id="${cotizacionIdActual}"]`);
      if (form) {
        form.submit();
      }
    }
  }

  // Cerrar modal si se hace clic fuera de él
  document.getElementById('modal-convertir').addEventListener('click', function(e) {
    if (e.target === this) {
      cerrarModalConvertir();
    }
  });

  // Cerrar modal con tecla Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      cerrarModalConvertir();
    }
  });
</script>
