<?php
$categorias = $categorias ?? [];
$marcas     = $marcas ?? [];
?>

<div class="card" style="margin-top: 18px;">
  <div class="card-header flex justify-between items-center">
    <div>
      <h1 class="card-title">Categorías & marcas</h1>
      <p class="text-muted">Administre los catálogos base para productos.</p>
    </div>
  </div>

  <div class="card-body">

    <div class="catalogos-grid">

      <!-- BLOQUE CATEGORÍAS -->
      <div class="catalogos-card">
        <div class="catalogos-card-header">
          <h2>Categorías</h2>
        </div>

        <form class="catalogos-form" method="POST" action="<?= url('/admin/catalogos/categorias/guardar') ?>">
          <input
            type="text"
            name="nombre"
            class="input"
            placeholder="Nueva categoría (ej: Herramientas)"
            required
          >
          <button type="submit" class="btn btn-primary">
            Guardar categoría
          </button>
        </form>

        <table class="table compras-table catalogos-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Estado</th>
              <th style="text-align:center;">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($categorias)): ?>
            <tr>
              <td colspan="4">No hay categorías registradas.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($categorias as $c): ?>
              <tr>
                <td><?= (int)$c['id'] ?></td>
                <td><?= htmlspecialchars($c['nombre']) ?></td>
                <td>
                  <?php if (!empty($c['activo'])): ?>
                    <span class="badge-status-proveedor badge-status-proveedor--activo">
                      ACTIVO
                    </span>
                  <?php else: ?>
                    <span class="badge-status-proveedor badge-status-proveedor--inactivo">
                      INACTIVO
                    </span>
                  <?php endif; ?>
                </td>
                <td style="text-align:center;">
                  <form method="POST"
                        action="<?= url('/admin/catalogos/categorias/cambiar-estado/' . (int)$c['id']) ?>"
                        style="display:inline;">
                    <input type="hidden" name="activo" value="<?= !empty($c['activo']) ? 0 : 1 ?>">
                    <?php if (!empty($c['activo'])): ?>
                      <button type="submit" class="btn-estado">
                        Desactivar
                      </button>
                    <?php else: ?>
                      <button type="submit" class="btn-estado btn-estado--activar">
                        Activar
                      </button>
                    <?php endif; ?>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- BLOQUE MARCAS -->
      <div class="catalogos-card">
        <div class="catalogos-card-header">
          <h2>Marcas</h2>
        </div>

        <form class="catalogos-form" method="POST" action="<?= url('/admin/catalogos/marcas/guardar') ?>">
          <input
            type="text"
            name="nombre"
            class="input"
            placeholder="Nueva marca (ej: Truper)"
            required
          >
          <button type="submit" class="btn btn-primary">
            Guardar marca
          </button>
        </form>

        <table class="table compras-table catalogos-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Estado</th>
              <th style="text-align:center;">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($marcas)): ?>
            <tr>
              <td colspan="4">No hay marcas registradas.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($marcas as $m): ?>
              <tr>
                <td><?= (int)$m['id'] ?></td>
                <td><?= htmlspecialchars($m['nombre']) ?></td>
                <td>
                  <?php if (!empty($m['activo'])): ?>
                    <span class="badge-status-proveedor badge-status-proveedor--activo">
                      ACTIVO
                    </span>
                  <?php else: ?>
                    <span class="badge-status-proveedor badge-status-proveedor--inactivo">
                      INACTIVO
                    </span>
                  <?php endif; ?>
                </td>
                <td style="text-align:center;">
                  <form method="POST"
                        action="<?= url('/admin/catalogos/marcas/cambiar-estado/' . (int)$m['id']) ?>"
                        style="display:inline;">
                    <input type="hidden" name="activo" value="<?= !empty($m['activo']) ? 0 : 1 ?>">
                    <?php if (!empty($m['activo'])): ?>
                      <button type="submit" class="btn-estado">
                        Desactivar
                      </button>
                    <?php else: ?>
                      <button type="submit" class="btn-estado btn-estado--activar">
                        Activar
                      </button>
                    <?php endif; ?>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

