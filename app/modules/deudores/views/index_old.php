<?php
// Listado de deudores
?>
<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Deudores</h1>
        <a href="<?= url('/admin/deudores/crear') ?>" class="btn btn-primary">Nueva Deuda</a>
    </div>

    <table class="w-full table-auto">
        <thead>
            <tr>
                <th>Id</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Saldo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deudas as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= htmlspecialchars($d['cliente_nombre']) ?></td>
                    <td><?= number_format($d['total'],2) ?></td>
                    <td><?= number_format($d['total_pagado'] ?? 0,2) ?></td>
                    <td><?= number_format($d['saldo'] ?? ($d['total'] - ($d['total_pagado'] ?? 0)),2) ?></td>
                    <td><?= $d['estado'] ?></td>
                    <td>
                        <a href="<?= url('/admin/deudores/ver?id=' . $d['id']) ?>" class="btn btn-sm">Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
