<div class="card" style="max-width: 1200px; margin: 0 auto;">
  <!-- HEADER -->
  <div class="card-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h1 class="card-title" style="color: white; font-size: 1.75rem; font-weight: 700; margin: 0;">
          🧾 Deuda #<?= $deuda['id'] ?>
          <?php
          $estadoColor = ($deuda['estado'] ?? 'ACTIVA') === 'PAGADA' ? '#28a745' : (($deuda['estado'] ?? 'ACTIVA') === 'CONVERTIDA' ? '#ffc107' : (($deuda['saldo'] ?? 0) <= 0 ? '#28a745' : '#dc3545'));
          $estadoTexto = ($deuda['estado'] ?? 'ACTIVA') === 'PAGADA' ? '✅ PAGADA' : (($deuda['estado'] ?? 'ACTIVA') === 'CONVERTIDA' ? '🔄 CONVERTIDA' : (($deuda['saldo'] ?? 0) <= 0 ? '✅ PAGADA' : '⏳ ACTIVA'));
          ?>
          <span style="background: <?= $estadoColor ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.85rem; margin-left: 0.75rem;">
            <?= $estadoTexto ?>
          </span>
        </h1>
        <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
          Cliente: <?= htmlspecialchars($deuda['cliente_nombre']) ?>
        </p>
      </div>
      <a
        href="<?= url('/admin/deudores') ?>"
        style="padding: 0.75rem 1.5rem; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; border: 2px solid white;">
        ← Volver
      </a>
    </div>
  </div>

  <?php if (!empty($success)): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.5rem; color: #155724;">
      <strong>✓</strong> <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
      <strong>⚠️</strong> <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div style="padding: 2rem;">

    <!-- RESUMEN FINANCIERO -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
      <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; border-radius: 0.75rem; color: white;">
        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Deuda</div>
        <div style="font-size: 1.75rem; font-weight: 700;">Q <?= number_format($deuda['total'], 2) ?></div>
      </div>

      <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 1.5rem; border-radius: 0.75rem; color: white;">
        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.5rem;">Pagado</div>
        <div style="font-size: 1.75rem; font-weight: 700;">Q <?= number_format($deuda['total_pagado'] ?? 0, 2) ?></div>
      </div>

      <div style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); padding: 1.5rem; border-radius: 0.75rem; color: white;">
        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.5rem;">Saldo Pendiente</div>
        <div style="font-size: 1.75rem; font-weight: 700;">Q <?= number_format($deuda['saldo'] ?? 0, 2) ?></div>
      </div>

      <div style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); padding: 1.5rem; border-radius: 0.75rem; color: white;">
        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.5rem;">Estado</div>
        <div style="font-size: 1.5rem; font-weight: 700;"><?= $deuda['estado'] ?></div>
      </div>
    </div>

    <!-- BOTÓN DESTACADO ABONO -->
    <?php if (($deuda['saldo'] ?? 0) > 0 && !in_array(($deuda['estado'] ?? 'ACTIVA'), ['CONVERTIDA', 'PAGADA'])): ?>
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div style="color: white;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700;">💰 Realizar Abono</h3>
            <p style="margin: 0; opacity: 0.9; color: #ffffff !important;">Registra un pago para reducir el saldo de esta deuda</p>
          </div>
          <button
            onclick="document.getElementById('modalAbono').style.display='flex'"
            style="padding: 1rem 2rem; background: white; color: #28a745; border: none; border-radius: 0.5rem; font-weight: 700; cursor: pointer; font-size: 1.1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            Registrar Pago
          </button>
        </div>
      </div>
    <?php elseif (($deuda['estado'] ?? 'ACTIVA') === 'PAGADA'): ?>
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div style="color: white;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700;">✅ Deuda Pagada</h3>
            <p style="margin: 0; opacity: 0.9;">Esta deuda ha sido completamente pagada y se generó la venta #<?= $deuda['venta_generada_id'] ?? 'N/A' ?></p>
          </div>
          <a
            href="<?= url('/admin/ventas/ver?id=' . ($deuda['venta_generada_id'] ?? 0)) ?>"
            style="padding: 1rem 2rem; background: white; color: #28a745; border: none; border-radius: 0.5rem; font-weight: 700; text-decoration: none; font-size: 1.1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            Ver Venta
          </a>
        </div>
      </div>
    <?php elseif (($deuda['estado'] ?? 'ACTIVA') === 'CONVERTIDA'): ?>
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div style="color: white;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700;">🔄 Deuda Convertida</h3>
            <p style="margin: 0; opacity: 0.9;">Esta deuda fue convertida automáticamente a venta #<?= $deuda['venta_generada_id'] ?? 'N/A' ?></p>
          </div>
          <a
            href="<?= url('/admin/ventas/ver?id=' . ($deuda['venta_generada_id'] ?? 0)) ?>"
            style="padding: 1rem 2rem; background: white; color: #ffc107; border: none; border-radius: 0.5rem; font-weight: 700; text-decoration: none; font-size: 1.1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            Ver Venta
          </a>
        </div>
      </div>
    <?php else: ?>
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div style="color: white;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700;">✅ Deuda Saldada</h3>
            <p style="margin: 0; opacity: 0.9;">Esta deuda ha sido completamente pagada</p>
          </div>
          <span style="padding: 1rem 2rem; background: rgba(255,255,255,0.2); color: white; border-radius: 0.5rem; font-weight: 700; font-size: 1.1rem;">
            Completada
          </span>
        </div>
      </div>
    <?php endif; ?>

    <!-- INFO GENERAL -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📋 Información General</h3>
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
        <div>
          <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Cliente</div>
          <div style="font-weight: 600; color: #495057;"><?= htmlspecialchars($deuda['cliente_nombre']) ?></div>
        </div>
        <div>
          <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Teléfono</div>
          <div style="font-weight: 600; color: #495057;"><?= htmlspecialchars($deuda['cliente_telefono'] ?? 'N/A') ?></div>
        </div>
        <div>
          <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Fecha</div>
          <div style="font-weight: 600; color: #495057;">
            <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?>
            <?= htmlspecialchars($deuda['fecha'] ?? '') ?>
          </div>
        </div>
        <div>
          <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Descripción</div>
          <div style="font-weight: 600; color: #495057;"><?= htmlspecialchars($deuda['descripcion'] ?? 'Sin descripción') ?></div>
        </div>
      </div>
    </div>

    <!-- PRODUCTOS DE LA DEUDA -->
    <div style="margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📦 Productos</h3>
      <div style="border: 2px solid #e9ecef; border-radius: 0.75rem; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
          <thead style="background: #f8f9fa;">
            <tr>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057;">Producto</th>
              <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #495057; width: 100px;">Cantidad</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Precio Unit.</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($detalle)): ?>
              <tr>
                <td colspan="4" style="padding: 2rem; text-align: center; color: #6c757d;">
                  No hay productos registrados en esta deuda
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($detalle as $item): ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                  <td style="padding: 0.75rem; color: #495057; font-weight: 600;">
                    <?= htmlspecialchars($item['producto_nombre']) ?>
                    <br><small style="color: #6c757d; font-weight: 400;">SKU: <?= htmlspecialchars($item['producto_sku'] ?? 'N/A') ?></small>
                  </td>
                  <td style="padding: 0.75rem; text-align: center; color: #6c757d;"><?= $item['cantidad'] ?></td>
                  <td style="padding: 0.75rem; text-align: right; color: #6c757d;">Q <?= number_format($item['precio_unitario'], 2) ?></td>
                  <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #dc3545;">Q <?= number_format($item['subtotal'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- HISTORIAL DE PAGOS -->
    <div style="margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">💵 Historial de Pagos</h3>
      <div style="border: 2px solid #e9ecef; border-radius: 0.75rem; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
          <thead style="background: #f8f9fa;">
            <tr>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057; width: 60px;">ID</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Monto</th>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057; width: 120px;">Método</th>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057;">Fecha</th>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057;">Usuario</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pagos)): ?>
              <tr>
                <td colspan="5" style="padding: 2rem; text-align: center; color: #6c757d;">
                  💡 No hay pagos registrados aún
                  <br><small style="margin-top: 0.5rem; display: block; opacity: 0.7;">Los pagos aparecerán aquí una vez registrados</small>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($pagos as $pago): ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                  <td style="padding: 0.75rem; color: #6c757d; font-weight: 600;">#<?= $pago['id'] ?></td>
                  <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #28a745;">Q <?= number_format($pago['monto'], 2) ?></td>
                  <td style="padding: 0.75rem; color: #495057;">
                    <?php
                    $metodo = isset($pago['metodo_pago']) ? $pago['metodo_pago'] : 'Efectivo';
                    $iconos = ['Efectivo' => '💵', 'Tarjeta' => '💳', 'Transferencia' => '🏦', 'Cheque' => '📄', 'Deposito' => '💰'];
                    echo ($iconos[$metodo] ?? '💵') . ' ' . htmlspecialchars($metodo);
                    ?>
                  </td>
                  <td style="padding: 0.75rem; color: #6c757d;">
                    <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?>
                    <?= date('d/m/Y H:i', strtotime($pago['fecha'])) ?>
                  </td>
                  <td style="padding: 0.75rem; color: #6c757d;"><?= htmlspecialchars($pago['usuario_nombre'] ?? 'N/A') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- MODAL PARA ABONO -->
<div id="modalAbono" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 0.75rem; width: 90%; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
    <div style="padding: 2rem; border-bottom: 2px solid #e9ecef;">
      <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #495057;">💰 Registrar Pago</h3>
    </div>

    <form method="POST" action="<?= url('/admin/deudores/registrarPago') ?>" style="padding: 2rem;">
      <input type="hidden" name="deuda_id" value="<?= $deuda['id'] ?>">

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
          Monto a Pagar <span style="color: #dc3545;">*</span>
        </label>
        <input
          type="number"
          name="monto"
          step="0.01"
          min="0.01"
          max="<?= $deuda['saldo'] ?? 0 ?>"
          required
          placeholder="Ingrese el monto"
          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 1rem;">
        <small style="color: #6c757d; margin-top: 0.5rem; display: block;">Saldo pendiente: Q <?= number_format($deuda['saldo'] ?? 0, 2) ?></small>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
          Método de Pago <span style="color: #dc3545;">*</span>
        </label>
        <select
          name="metodo_pago"
          required
          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 1rem; background: white;">
          <option value="">Seleccione método de pago</option>
          <option value="Efectivo">💵 Efectivo</option>
          <option value="Tarjeta">💳 Tarjeta</option>
          <option value="Transferencia">🏦 Transferencia</option>
          <option value="Cheque">📄 Cheque</option>
          <option value="Deposito">💰 Depósito</option>
        </select>
      </div>

      <div style="display: flex; gap: 1rem; justify-content: flex-end;">
        <button
          type="button"
          onclick="document.getElementById('modalAbono').style.display='none'"
          style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
          Cancelar
        </button>
        <button
          type="submit"
          style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(40, 167, 69, 0.3);">
          Registrar Pago
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .card {
    animation: fadeIn 0.3s ease-in;
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

  #modalAbono {
    backdrop-filter: blur(5px);
    animation: fadeInModal 0.3s ease-out;
  }

  @keyframes fadeInModal {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }
</style>

<script>
  // Cerrar modal con tecla ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const modal = document.getElementById('modalAbono');
      if (modal && modal.style.display === 'flex') {
        modal.style.display = 'none';
      }
    }
  });

  // Cerrar modal al hacer clic fuera de él
  document.getElementById('modalAbono')?.addEventListener('click', function(e) {
    if (e.target === this) {
      this.style.display = 'none';
    }
  });

  // Validación en tiempo real del monto
  document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.querySelector('input[name="monto"]');
    const saldoPendiente = <?= $deuda['saldo'] ?? 0 ?>;

    if (montoInput) {
      montoInput.addEventListener('input', function() {
        const valor = parseFloat(this.value) || 0;
        const submitBtn = this.form.querySelector('button[type="submit"]');

        if (valor > saldoPendiente) {
          this.style.borderColor = '#dc3545';
          this.style.background = '#fff5f5';
          submitBtn.disabled = true;
          submitBtn.style.opacity = '0.6';

          // Mostrar mensaje de error
          let errorMsg = this.nextElementSibling?.nextElementSibling;
          if (!errorMsg || !errorMsg.classList.contains('error-msg')) {
            errorMsg = document.createElement('small');
            errorMsg.classList.add('error-msg');
            errorMsg.style.color = '#dc3545';
            errorMsg.style.display = 'block';
            errorMsg.style.marginTop = '0.25rem';
            this.parentNode.appendChild(errorMsg);
          }
          errorMsg.textContent = '⚠️ El monto no puede ser mayor al saldo pendiente';

        } else if (valor <= 0) {
          this.style.borderColor = '#dc3545';
          this.style.background = '#fff5f5';
          submitBtn.disabled = true;
          submitBtn.style.opacity = '0.6';
        } else {
          this.style.borderColor = '#28a745';
          this.style.background = '#f8fff8';
          submitBtn.disabled = false;
          submitBtn.style.opacity = '1';

          // Remover mensaje de error
          const errorMsg = this.parentNode.querySelector('.error-msg');
          if (errorMsg) errorMsg.remove();
        }
      });
    }
  });
</script>
