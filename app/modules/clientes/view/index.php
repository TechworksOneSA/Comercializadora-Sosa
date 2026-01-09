<div class="card">
  <!-- HEADER -->
  <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div>
      <h1 class="card-title" style="color: white; font-size: 1.75rem; font-weight: 700;">Clientes</h1>
      <p class="card-subtitle" style="color: rgba(255,255,255,0.9); margin-top: 0.25rem;">Gestión de clientes registrados</p>
    </div>
    <a href="<?= url('/admin/clientes/crear') ?>" class="btn btn-primary" style="background: white; color: #667eea; border: none; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s;">
      ➕ Nuevo Cliente
    </a>
  </div>

  <!-- BARRA DE BÚSQUEDA -->
  <div style="padding: 1.5rem; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
    <form method="GET" action="<?= url('/admin/clientes') ?>" style="display: flex; gap: 1rem; align-items: center;">
      <input
        type="text"
        name="q"
        value="<?= e($q ?? '') ?>"
        placeholder="Buscar por NIT o nombre..."
        class="input"
        style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem;"
      >
      <button type="submit" class="btn btn-primary" style="background: #667eea; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; border: none;">
        🔍 Buscar
      </button>
      <?php if (!empty($q)): ?>
        <a href="<?= url('/admin/clientes') ?>" class="btn" style="background: #6c757d; color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; text-decoration: none;">
          ✖ Limpiar
        </a>
      <?php endif; ?>
    </form>
  </div>

  <!-- MENSAJE DE ÉXITO -->
  <?php if (isset($_GET['ok']) && $_GET['ok'] === 'creado'): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.5rem; color: #155724;">
      ✅ Cliente creado exitosamente
    </div>
  <?php endif; ?>

  <!-- TABLA -->
  <div class="card-body" style="padding: 0;">
    <div style="overflow-x: auto;">
      <table class="table" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
          <tr>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">ID</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">NIT</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Nombre</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Dirección</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Teléfono</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #495057;">Correo</th>
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #495057;">Estado</th>
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #495057;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($clientes)): ?>
            <?php foreach ($clientes as $c): ?>
              <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                <td style="padding: 1rem; color: #6c757d;"><?= e($c['id']) ?></td>
                <td style="padding: 1rem; font-weight: 600; color: #495057;"><?= e($c['nit']) ?></td>
                <td style="padding: 1rem; color: #495057;"><?= e($c['nombre']) ?></td>
                <td style="padding: 1rem; color: #6c757d; font-size: 0.9rem;"><?= e($c['direccion']) ?></td>
                <td style="padding: 1rem; color: #6c757d;"><?= e($c['telefono']) ?></td>
                <td style="padding: 1rem; color: #6c757d;"><?= e($c['correo']) ?></td>
                <td style="padding: 1rem; text-align: center;">
                  <?php if ($c['activo']): ?>
                    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #d4edda; color: #155724; border-radius: 1rem; font-size: 0.85rem; font-weight: 600;">
                      ✓ Activo
                    </span>
                  <?php else: ?>
                    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f8d7da; color: #721c24; border-radius: 1rem; font-size: 0.85rem; font-weight: 600;">
                      ✗ Inactivo
                    </span>
                  <?php endif; ?>
                </td>
                <td style="padding: 1rem; text-align: center;">
                  <a 
                    href="<?= url('/admin/clientes/editar/' . $c['id']) ?>" 
                    style="display: inline-block; padding: 0.5rem 1rem; background: #667eea; color: white; text-decoration: none; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600; transition: all 0.2s;"
                    onmouseover="this.style.background='#5568d3'"
                    onmouseout="this.style.background='#667eea'"
                  >
                    ✏️ Editar
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="padding: 3rem; text-align: center; color: #6c757d;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                <p style="font-size: 1.1rem; margin: 0;">
                  <?= !empty($q) ? 'No se encontraron clientes con ese criterio.' : 'No hay clientes registrados aún.' ?>
                </p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
