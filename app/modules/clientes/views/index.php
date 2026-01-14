<div class="card">
  <!-- RESUMEN ESTADÍSTICO -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem 0.5rem 0 0;">
    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2);">
      <div style="color: rgba(255,255,255,0.9); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
        👥 CLIENTES REGISTRADOS
      </div>
      <div style="color: white; font-size: 2.5rem; font-weight: 700;">
        <?= number_format($stats['total_clientes'] ?? 0) ?>
      </div>
    </div>
    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2);">
      <div style="color: rgba(255,255,255,0.9); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
        💰 TOTAL GASTADO (GLOBAL)
      </div>
      <div style="color: white; font-size: 2.5rem; font-weight: 700;">
        Q <?= number_format($stats['total_gastado_global'] ?? 0, 2) ?>
      </div>
    </div>
  </div>

  <!-- HEADER -->
  <div class="card-header" style="background: white; border-bottom: 2px solid #e9ecef;">
    <div>
      <h1 class="card-title" style="color: #495057; font-size: 1.75rem; font-weight: 700;">Gestión de Clientes</h1>
      <p class="card-subtitle" style="color: #6c757d; margin-top: 0.25rem;">Administra la información de tus clientes</p>
    </div>
    <a href="<?= url('/admin/clientes/crear') ?>" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3); transition: all 0.3s;">
      ➕ Nuevo Cliente
    </a>
  </div>

  <!-- BUSCADOR -->
  <div style="padding: 1.5rem; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
    <div style="position: relative; max-width: 500px;">
      <input
        type="text"
        id="searchInput"
        placeholder="🔍 Buscar por nombre, teléfono, NIT o dirección..."
        style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 2px solid #dee2e6; border-radius: 0.5rem; font-size: 1rem; transition: all 0.3s;"
        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
        onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'"
      />
      <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 1.25rem;">🔍</span>
    </div>
    <p id="searchResults" style="margin-top: 0.75rem; color: #6c757d; font-size: 0.9rem;"></p>
  </div>

  <!-- MENSAJES DE ÉXITO Y ERROR -->
  <?php if (isset($_GET['ok']) && $_GET['ok'] === 'creado'): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.5rem; color: #155724;">
      ✅ Cliente creado exitosamente
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['clientes_success'])): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.5rem; color: #155724;">
      ✅ <?= htmlspecialchars($_SESSION['clientes_success']) ?>
    </div>
    <?php unset($_SESSION['clientes_success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['clientes_error'])): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
      ⚠️ <?= htmlspecialchars($_SESSION['clientes_error']) ?>
    </div>
    <?php unset($_SESSION['clientes_error']); ?>
  <?php endif; ?>

  <!-- TABLA -->
  <div class="card-body" style="padding: 0;">
    <div style="overflow-x: auto;">
      <table class="table" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
          <tr>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057; width: 60px;">ID</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Nombre Completo</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Teléfono</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Dirección</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Método de Pago Favorito</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">NIT</th>
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #495057;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($clientes)): ?>
            <?php foreach ($clientes as $c): ?>
              <tr
                class="cliente-row"
                data-nombre="<?= e($c['nombre'] . ' ' . $c['apellido']) ?>"
                data-telefono="<?= e($c['telefono']) ?>"
                data-nit="<?= e($c['nit'] ?? '') ?>"
                data-direccion="<?= e($c['direccion'] ?? '') ?>"
                style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;"
                onmouseover="this.style.background='#f8f9fa'"
                onmouseout="this.style.background='white'">
                <td style="padding: 1rem; color: #6c757d; font-weight: 600;">#<?= e($c['id']) ?></td>
                <td style="padding: 1rem; color: #495057; font-weight: 600;">
                  <?= e($c['nombre']) ?> <?= e($c['apellido']) ?>
                </td>
                <td style="padding: 1rem; color: #6c757d;">
                  <span style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    📞 <?= e($c['telefono']) ?>
                  </span>
                </td>
                <td style="padding: 1rem; color: #6c757d; font-size: 0.9rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  <?= e($c['direccion']) ?: '<span style="color: #adb5bd;">—</span>' ?>
                </td>
                <td style="padding: 1rem;">
                  <?php
                  $metodoBadges = [
                    'Efectivo' => ['bg' => '#d4edda', 'color' => '#155724', 'icon' => '💵'],
                    'Transferencia' => ['bg' => '#d1ecf1', 'color' => '#0c5460', 'icon' => '🏦'],
                    'Tarjeta' => ['bg' => '#fff3cd', 'color' => '#856404', 'icon' => '💳'],
                    'Crédito' => ['bg' => '#f8d7da', 'color' => '#721c24', 'icon' => '📝'],
                  ];
                  $metodo = $c['preferencia_metodo_pago'] ?? '';
                  $badge = $metodoBadges[$metodo] ?? ['bg' => '#e9ecef', 'color' => '#495057', 'icon' => '💰'];
                  ?>
                  <span style="display: inline-block; padding: 0.25rem 0.75rem; background: <?= $badge['bg'] ?>; color: <?= $badge['color'] ?>; border-radius: 1rem; font-size: 0.85rem; font-weight: 600;">
                    <?= $badge['icon'] ?> <?= e($metodo) ?>
                  </span>
                </td>
                <td style="padding: 1rem; color: #6c757d; font-family: monospace;">
                  <?= e($c['nit']) ?: '<span style="color: #adb5bd;">—</span>' ?>
                </td>
                <td style="padding: 1rem; text-align: center;">
                  <div style="display: flex; gap: 0.5rem; justify-content: center;">
                    <a
                      href="<?= url('/admin/clientes/editar/' . $c['id']) ?>"
                      style="display: inline-block; padding: 0.5rem 0.75rem; background: #667eea; color: white; text-decoration: none; border-radius: 0.375rem; font-size: 0.85rem; font-weight: 600; transition: all 0.2s;"
                      title="Editar Cliente">
                      ✏️
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="padding: 3rem; text-align: center; color: #6c757d;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                <p style="font-size: 1.1rem; margin: 0;">No hay clientes registrados aún.</p>
                <a href="<?= url('/admin/clientes/crear') ?>" style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #667eea; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                  Crear primer cliente
                </a>
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

  .cliente-row {
    transition: all 0.2s;
  }

  .cliente-row.hidden {
    display: none;
  }
</style>

<script>
  // Búsqueda en tiempo real
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const tableRows = document.querySelectorAll('tbody tr');

    // Filtrar solo las filas con clase cliente-row (excluir el mensaje de "no hay clientes")
    const clienteRows = Array.from(tableRows).filter(row => row.classList.contains('cliente-row'));
    const totalClientes = clienteRows.length;

    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase().trim();

      if (!searchTerm) {
        // Mostrar todos
        clienteRows.forEach(row => row.classList.remove('hidden'));
        searchResults.textContent = '';
        return;
      }

      let visibleCount = 0;

      clienteRows.forEach(row => {
        const nombre = row.getAttribute('data-nombre') || '';
        const telefono = row.getAttribute('data-telefono') || '';
        const nit = row.getAttribute('data-nit') || '';
        const direccion = row.getAttribute('data-direccion') || '';

        const searchText = `${nombre} ${telefono} ${nit} ${direccion}`.toLowerCase();

        if (searchText.includes(searchTerm)) {
          row.classList.remove('hidden');
          visibleCount++;
        } else {
          row.classList.add('hidden');
        }
      });

      // Actualizar contador
      if (visibleCount === 0) {
        searchResults.innerHTML = '<span style="color: #dc3545;">❌ No se encontraron resultados</span>';
      } else if (visibleCount === totalClientes) {
        searchResults.textContent = '';
      } else {
        searchResults.innerHTML = `<span style="color: #28a745;">✅ Mostrando ${visibleCount} de ${totalClientes} clientes</span>`;
      }
    });
  });
</script>
