<?php
// Ver deuda y registrar pagos
?>
<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Deuda #<?= $deuda['id'] ?> - <?= htmlspecialchars($deuda['cliente_nombre']) ?></h1>
        <a href="<?= url('/admin/deudores') ?>" class="btn btn-secondary">Volver</a>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="p-4 border rounded">
            <p><strong>Total:</strong> <?= number_format($deuda['total'],2) ?></p>
            <p><strong>Pagado:</strong> <?= number_format($deuda['total_pagado'] ?? 0,2) ?></p>
            <p><strong>Saldo:</strong> <?= number_format($deuda['saldo'] ?? ($deuda['total'] - ($deuda['total_pagado'] ?? 0)),2) ?></p>
            <p><strong>Estado:</strong> <?= $deuda['estado'] ?></p>
            <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($deuda['descripcion'] ?? '')) ?></p>
        </div>

        <div class="p-4 border rounded">
            <h3 class="font-semibold mb-2">Registrar Pago</h3>
            <form action="<?= url('/admin/deudores/registrarPago') ?>" method="post">
                <input type="hidden" name="deuda_id" value="<?= $deuda['id'] ?>">
                <div class="mb-2">
                    <label>Monto a pagar</label>
                    <input type="number" step="0.01" name="monto" class="w-full">
                </div>
                <button class="btn btn-primary">Registrar Pago</button>
            </form>

            <hr class="my-4">

            <h3 class="font-semibold mb-2">Ampliar Deuda</h3>
            <form action="<?= url('/admin/deudores/ampliar') ?>" method="post">
                <input type="hidden" name="deuda_id" value="<?= $deuda['id'] ?>">
                <div class="mb-2">
                    <label>Monto a añadir</label>
                    <input type="number" step="0.01" name="monto" class="w-full">
                </div>
                <button class="btn btn-warning">Ampliar</button>
            </form>
        </div>
    </div>

    <div class="p-4 border rounded">
        <h3 class="font-semibold mb-2">Pagos</h3>
        <table class="w-full">
            <thead>
                <tr><th>Id</th><th>Monto</th><th>Fecha</th><th>Usuario</th></tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= number_format($p['monto'],2) ?></td>
                        <td><?= $p['fecha'] ?></td>
                        <td><?= htmlspecialchars($p['usuario_nombre'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
