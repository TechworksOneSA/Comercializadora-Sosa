<div class="card" style="max-width: 1400px; margin: 0 auto;">
  <!-- ESTADÍSTICAS -->
  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; padding: 1.5rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 0.5rem 0.5rem 0 0;">
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Total Deudas</div>
      <div style="color: white; font-size: 2rem; font-weight: 700; line-height: 1;"><?= count($deudas ?? []) ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Registradas</div>
    </div>
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Deudas Activas</div>
      <div style="color: white; font-size: 2rem; font-weight: 700; line-height: 1;"><?= count(array_filter($deudas ?? [], function ($d) {
                                                                                      return $d['estado'] === 'ACTIVA';
                                                                                    })) ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Pendientes</div>
    </div>
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Total Adeudado</div>
      <div style="color: white; font-size: 1.5rem; font-weight: 700; line-height: 1;">Q <?= number_format(array_sum(array_map(function ($d) {
                                                                                          return ($d['total'] - ($d['total_pagado'] ?? 0)) > 0 ? ($d['total'] - ($d['total_pagado'] ?? 0)) : 0;
                                                                                        }, $deudas ?? [])), 2) ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Por cobrar</div>
    </div>
    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.18);">
      <div style="color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">Total Cobrado</div>
      <div style="color: white; font-size: 1.5rem; font-weight: 700; line-height: 1;">Q <?= number_format(array_sum(array_map(function ($d) {
                                                                                          return $d['total_pagado'] ?? 0;
                                                                                        }, $deudas ?? [])), 2) ?></div>
      <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem; margin-top: 0.25rem;">Recuperado</div>
    </div>
  </div>

  <!-- HEADER -->
  <div class="card-header" style="background: white; border-bottom: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1 class="card-title" style="color: #495057; font-size: 1.75rem; font-weight: 700; margin: 0;">🧾 Deudores</h1>
      <p style="color: #6c757d; margin: 0.25rem 0 0 0; font-size: 0.95rem;">Gestión de deudas y pagos de clientes</p>
    </div>
    <a
      href="<?= url('/admin/deudores/crear') ?>"
      style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 700; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);">
      ➕ Nueva Deuda
    </a>
  </div>

  <!-- BARRA DE BÚSQUEDA -->
  <div style="padding: 1.5rem; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
    <div style="display: flex; gap: 0.75rem; max-width: 600px;">
      <div style="flex: 1; position: relative;">
        <input
          type="text"
          id="busquedaInput"
          placeholder="🔍 Buscar en tiempo real por nombre del cliente o teléfono..."
          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #dee2e6; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;"
          onfocus="this.style.borderColor='#dc3545'"
          onblur="this.style.borderColor='#dee2e6'"
          oninput="busquedaEnTiempoReal(this.value)"
          autocomplete="off">
        <!-- Indicador de búsqueda -->
        <div id="indicadorBusqueda" style="display: none; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #dc3545;">
          <div style="width: 16px; height: 16px; border: 2px solid #e9ecef; border-top: 2px solid #dc3545; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        </div>
      </div>
      <button type="button" onclick="limpiarBusqueda()" style="padding: 0.75rem 1rem; background: #6c757d; color: white; border: none; border-radius: 0.5rem; font-weight: 600;">
        ✖️ Limpiar
      </button>
    </div>
    <!-- Resultados de búsqueda en tiempo real -->
    <div id="resultadosBusqueda" style="margin-top: 0.75rem; color: #6c757d; font-size: 0.9rem; display: none;">
      <span id="contadorResultados"></span>
    </div>
  </div>

  <div style="padding: 2rem;">

    <!-- TABLA DE DEUDAS -->
    <div style="border: 2px solid #e9ecef; border-radius: 0.75rem; overflow: hidden;">
      <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8f9fa;">
          <tr>
            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057; width: 60px;">ID</th>
            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057;">Cliente</th>
            <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Total</th>
            <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Pagado</th>
            <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Saldo</th>
            <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #495057; width: 100px;">Estado</th>
            <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #495057; width: 120px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($deudas)): ?>
            <tr>
              <td colspan="7" style="padding: 3rem; text-align: center; color: #6c757d;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">No hay deudas registradas</div>
                <div style="font-size: 0.95rem;">Crea una nueva deuda usando el botón "Nueva Deuda"</div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($deudas as $d): ?>
              <?php
              $saldo = $d['saldo'] ?? ($d['total'] - ($d['total_pagado'] ?? 0));
              $porcentajePagado = $d['total'] > 0 ? (($d['total_pagado'] ?? 0) / $d['total']) * 100 : 0;

              // Auto-cambio de estado cuando saldo es 0
              $estadoActual = $d['estado'];
              if ($saldo <= 0 && $estadoActual === 'ACTIVA') {
                $estadoActual = 'PAGADA';
              }

              $estadoColors = [
                'ACTIVA' => ['bg' => '#dc3545', 'text' => 'ACTIVA'],
                'PAGADA' => ['bg' => '#28a745', 'text' => 'PAGADA'],
                'CONVERTIDA' => ['bg' => '#17a2b8', 'text' => 'CONVERTIDA'],
                'CANCELADA' => ['bg' => '#6c757d', 'text' => 'CANCELADA']
              ];
              $estadoStyle = $estadoColors[$estadoActual] ?? $estadoColors['ACTIVA'];
              $saldoColor = $saldo > 0 ? '#dc3545' : '#28a745';
              ?>
              <tr style="border-bottom: 1px solid #e9ecef;">
                <td style="padding: 0.75rem; color: #6c757d; font-weight: 600;">#<?= $d['id'] ?></td>
                <td style="padding: 0.75rem; color: #495057;">
                  <div style="font-weight: 600;"><?= htmlspecialchars($d['cliente_nombre']) ?></div>
                  <div style="font-size: 0.85rem; color: #6c757d;">📞 <?= htmlspecialchars($d['cliente_telefono'] ?? 'N/A') ?></div>
                </td>
                <td style="padding: 0.75rem; text-align: right; color: #495057; font-weight: 600;">
                  Q <?= number_format($d['total'], 2) ?>
                </td>
                <td style="padding: 0.75rem; text-align: right; color: #28a745; font-weight: 600;">
                  Q <?= number_format($d['total_pagado'] ?? 0, 2) ?>
                  <div style="font-size: 0.75rem; color: #6c757d;"><?= number_format($porcentajePagado, 1) ?>%</div>
                </td>
                <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: <?= $saldoColor ?>;">
                  Q <?= number_format($saldo, 2) ?>
                </td>
                <td style="padding: 0.75rem; text-align: center;">
                  <span style="display: inline-block; padding: 0.25rem 0.75rem; background: <?= $estadoStyle['bg'] ?>; color: white; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">
                    <?= $estadoStyle['text'] ?><?= $saldo <= 0 && $d['estado'] === 'ACTIVA' ? ' 🔄' : '' ?>
                  </span>
                  <?php if ($saldo <= 0 && $d['estado'] === 'ACTIVA'): ?>
                    <div style="font-size: 0.7rem; color: #28a745; margin-top: 0.25rem;">✅ Auto-completada</div>
                  <?php endif; ?>
                </td>
                <td style="padding: 0.75rem; text-align: center;">
                  <a
                    href="<?= url('/admin/deudores/ver?id=' . $d['id']) ?>"
                    style="display: inline-block; padding: 0.5rem 1rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; text-decoration: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.85rem; box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);">
                    Ver Detalle
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

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
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    border-color: #dc3545;
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
  // Variables y funciones para búsqueda en tiempo real
  let timerBusqueda = null;
  let todasLasDeudas = [];

  // Cargar todas las deudas al inicio
  document.addEventListener('DOMContentLoaded', function() {
    cargarTodasLasDeudas();
  });

  function cargarTodasLasDeudas() {
    const filas = document.querySelectorAll('tbody tr');
    todasLasDeudas = [];

    filas.forEach(fila => {
      const celdas = fila.querySelectorAll('td');
      if (celdas.length > 1) {
        const deuda = {
          id: celdas[0].textContent.trim(),
          clienteNombre: celdas[1].querySelector('div:first-child')?.textContent || '',
          clienteTelefono: celdas[1].querySelector('div:last-child')?.textContent || '',
          total: celdas[2].textContent.trim(),
          pagado: celdas[3].textContent.trim(),
          saldo: celdas[4].textContent.trim(),
          estado: celdas[5].textContent.trim(),
          filaHTML: fila.outerHTML
        };
        todasLasDeudas.push(deuda);
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
      realizarBusquedaDeudas(termino);
      indicador.style.display = 'none';
    }, 300);
  }

  function realizarBusquedaDeudas(termino) {
    const tbody = document.querySelector('tbody');
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    const contadorResultados = document.getElementById('contadorResultados');

    if (!termino || termino.trim() === '') {
      tbody.innerHTML = todasLasDeudas.map(deuda => deuda.filaHTML).join('');
      resultadosDiv.style.display = 'none';
      reactivarEventosFilas();
      return;
    }

    const terminoBusqueda = termino.toLowerCase().trim();
    const deudasFiltradas = todasLasDeudas.filter(deuda => {
      return deuda.clienteNombre.toLowerCase().includes(terminoBusqueda) ||
        deuda.clienteTelefono.toLowerCase().includes(terminoBusqueda) ||
        deuda.id.toLowerCase().includes(terminoBusqueda);
    });

    if (deudasFiltradas.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" style="padding: 2rem; text-align: center; color: #6c757d; font-style: italic;">
            🔍 No se encontraron deudas que coincidan con: <strong>"${termino}"</strong>
            <br><small style="margin-top: 0.5rem; display: block;">Intenta con otro término de búsqueda</small>
          </td>
        </tr>
      `;
    } else {
      tbody.innerHTML = deudasFiltradas.map(deuda => deuda.filaHTML).join('');
    }

    const plural = deudasFiltradas.length === 1 ? 'resultado' : 'resultados';
    contadorResultados.innerHTML = `🔍 ${deudasFiltradas.length} ${plural} encontrado(s) para: <strong>"${termino}"</strong>`;
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

  function limpiarBusqueda() {
    const input = document.getElementById('busquedaInput');
    input.value = '';
    realizarBusquedaDeudas('');
    input.focus();
  }

  // Limpiar búsqueda con ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const input = document.getElementById('busquedaInput');
      if (input === document.activeElement) {
        limpiarBusqueda();
      }
    }
  });
</script>
