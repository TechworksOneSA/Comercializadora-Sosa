<?php
$gastos = $gastos ?? [];
?>

<style>
.gastos-container {
    padding: 2rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    min-height: 100vh;
}

.gastos-header {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    padding: 1.75rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.gastos-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: white;
    margin: 0;
}

.btn-primary {
    background: white;
    color: #ef4444;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.table-container {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.gastos-table {
    width: 100%;
    border-collapse: collapse;
}

.gastos-table thead {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.gastos-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    color: white;
}

.gastos-table thead tr th:first-child {
    border-radius: 0.5rem 0 0 0;
}

.gastos-table thead tr th:last-child {
    border-radius: 0 0.5rem 0 0;
}

.gastos-table td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.gastos-table tbody tr:hover {
    background: #f8fafc;
}

.badge {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.badge.gasto {
    background: #fee2e2;
    color: #991b1b;
}

.badge.retiro {
    background: #fed7aa;
    color: #9a3412;
}

.btn-delete {
    background: #ef4444;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-delete:hover {
    background: #dc2626;
}
</style>

<div class="gastos-container">
    <!-- Header -->
    <div class="gastos-header">
        <div>
            <h1>📤 Gastos y Movimientos de Caja</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
                Registro de gastos operativos y retiros
            </p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="<?= url('/admin/pos') ?>" class="btn-primary">← Volver a POS</a>
            <a href="<?= url('/admin/pos/nuevo-gasto') ?>" class="btn-primary">➕ Nuevo Movimiento</a>
        </div>
    </div>

    <!-- Tabla de Gastos -->
    <div class="table-container">
        <?php if (empty($gastos)): ?>
            <p style="text-align: center; padding: 2rem; color: #64748b;">No hay movimientos registrados</p>
        <?php else: ?>
            <table class="gastos-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Método de Pago</th>
                        <th>Monto</th>
                        <th>Usuario</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gastos as $gasto): ?>
                        <tr>
                            <td>📅 <?= date('d/m/Y H:i', strtotime($gasto['fecha'])) ?></td>
                            <td>
                                <span class="badge <?= $gasto['tipo'] ?>">
                                    <?= $gasto['tipo'] === 'gasto' ? '📤 Gasto' : '🏦 Retiro' ?>
                                </span>
                            </td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($gasto['concepto']) ?></td>
                            <td><?= htmlspecialchars($gasto['metodo_pago']) ?></td>
                            <td style="font-weight: 700; color: #ef4444;">-Q <?= number_format($gasto['monto'], 2) ?></td>
                            <td><?= htmlspecialchars($gasto['usuario_nombre'] ?? 'N/A') ?></td>
                            <td>
                                <small style="color: #64748b;">
                                    <?= htmlspecialchars($gasto['observaciones'] ?: '-') ?>
                                </small>
                            </td>
                            <td>
                                <form method="POST" action="<?= url("/admin/pos/eliminar-gasto/{$gasto['id']}") ?>"
                                      onsubmit="return confirm('¿Está seguro de eliminar este movimiento?')"
                                      style="display: inline;">
                                    <button type="submit" class="btn-delete">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: #fee2e2; font-weight: bold;">
                        <td colspan="4" style="text-align: right; padding-right: 2rem;">TOTAL EGRESOS:</td>
                        <td style="color: #ef4444; font-size: 1.125rem;">
                            -Q <?= number_format(array_sum(array_column($gastos, 'monto')), 2) ?>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
