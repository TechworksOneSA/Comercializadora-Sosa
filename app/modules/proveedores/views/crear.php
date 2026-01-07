<?php
$errors = $errors ?? [];
$old    = $old ?? [];
?>

<div class="card productos-form-card" style="max-width: 780px; margin: 18px auto;">

  <div class="card-header flex justify-between items-center">
    <h1 class="card-title">Crear proveedor</h1>
    <a href="<?= url('/admin/proveedores') ?>" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card-body">
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <p class="text-muted" style="margin-bottom: 14px;">
      Registre proveedores que podrá usar luego en el módulo de compras.
    </p>

    <form method="POST" action="<?= url('/admin/proveedores/guardar') ?>">

      <div class="grid-2">
        <div class="form-group">
          <label>NIT *</label>
          <input
            type="text"
            name="nit"
            class="input"
            required
            placeholder="CF o NIT con guiones"
            value="<?= htmlspecialchars($old['nit'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Nombre *</label>
          <input
            type="text"
            name="nombre"
            class="input"
            required
            placeholder="Nombre del proveedor"
            value="<?= htmlspecialchars($old['nombre'] ?? '') ?>">
        </div>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label>Teléfono</label>
          <input
            type="text"
            name="telefono"
            class="input"
            value="<?= htmlspecialchars($old['telefono'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Correo</label>
          <input
            type="email"
            name="correo"
            class="input"
            value="<?= htmlspecialchars($old['correo'] ?? '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label>Dirección</label>
        <input
          type="text"
          name="direccion"
          class="input"
          value="<?= htmlspecialchars($old['direccion'] ?? '') ?>">
      </div>

      <div class="form-group" style="margin-top: 8px; flex-direction: row; align-items: center; gap: 8px;">
        <input
          id="activo"
          type="checkbox"
          name="activo"
          <?= (isset($old['activo']) ? 'checked' : 'checked') ?>>
        <label for="activo" style="margin:0; font-weight: 500;">
          Proveedor activo
        </label>
      </div>

      <div class="form-actions mt-4 flex justify-end gap-2">
        <a href="<?= url('/admin/proveedores') ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar proveedor</button>
      </div>
    </form>
  </div>
</div>

