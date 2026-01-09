<?php
// Formulario para crear deuda
?>
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Nueva Deuda</h1>
    <div class="mb-3 text-sm text-muted">Clientes disponibles: <strong><?= isset($clientes) ? count($clientes) : 0 ?></strong></div>
    <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
        <pre style="background:#111;color:#0f0;padding:8px;border-radius:4px;overflow:auto;max-height:200px;">
<?= htmlspecialchars(json_encode($clientes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) ?>
        </pre>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-4">
            <ul>
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= url('/admin/deudores/guardar') ?>" method="post">
        <div class="mb-3">
            <label class="block">Cliente</label>
            <?php if (empty($clientes)): ?>
                <div class="alert alert-warning mb-2">No se encontraron clientes para mostrar en el selector.</div>
                <div class="text-sm text-muted mb-3">Verifica la tabla <strong>clientess</strong> en la base de datos o revisa los logs de PHP/Apache.</div>
                <select name="cliente_id" class="w-full" disabled>
                    <option value="">-- Sin clientes --</option>
                </select>
            <?php else: ?>
                <select name="cliente_id" class="w-full">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (isset($old['cliente_id']) && $old['cliente_id']==$c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?> - <?= htmlspecialchars($c['telefono']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label>Monto</label>
            <input type="number" step="0.01" name="total" class="w-full" value="<?= htmlspecialchars($old['total'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label>Descripción (opcional)</label>
            <textarea name="descripcion" class="w-full"><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
        </div>

        <button class="btn btn-primary">Crear Deuda</button>
        <a href="<?= url('/admin/deudores') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
