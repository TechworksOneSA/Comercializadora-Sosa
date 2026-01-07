<?php
$ventasPendientes = $ventasPendientes ?? [];
$resumenCaja = $resumenCaja ?? [];
$ultimosMovimientos = $ultimosMovimientos ?? [];
?>

<style>
    .pos-container {
        margin: 2rem;
        padding: 2rem;
        background: #f8fafc;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .pos-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .pos-header h1 {
        margin: 0 0 0.5rem 0;
        font-size: 2rem;
    }

    .pos-header p {
        margin: 0;
        opacity: 0.9;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
    }

    .stat-card.ingresos {
        border-color: #10b981;
    }

    .stat-card.gastos {
        border-color: #ef4444;
    }

    .stat-card.retiros {
        border-color: #f59e0b;
    }

    .stat-card.saldo {
        border-color: #3b82f6;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-card.ingresos .stat-value {
        color: #10b981;
    }

    .stat-card.gastos .stat-value {
        color: #ef4444;
    }

    .stat-card.retiros .stat-value {
        color: #f59e0b;
    }

    .stat-card.saldo .stat-value {
        color: #3b82f6;
    }

    .section-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1e293b;
        margin: 2rem 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-content {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-container {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .modern-table th,
    .modern-table td {
        padding: 0.875rem 1rem;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .modern-table th {
        background: #f8fafc;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.8rem;
    }

    .modern-table tbody tr:hover {
        background: #f1f5f9;
    }

    .badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge.pendiente {
        background: #fef3c7;
        color: #92400e;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-1px);
    }

    .actions-bar {
        padding: 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .no-data {
        padding: 2rem;
        text-align: center;
        color: #64748b;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }

    .section-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .pos-container {
            margin: 1rem;
            padding: 1rem;
        }

        .stats-cards {
            grid-template-columns: 1fr;
        }

        .section-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="pos-container">
    <!-- Header -->
    <div class="pos-header">
        <h1>💳 Punto de Venta - Caja</h1>
        <p>Control integral de ventas, cobros y movimientos de caja</p>
    </div>

    <!-- Alertas -->
    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success">
            ✅ <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-error">
            ❌ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Resumen de Caja -->
    <div class="stats-cards">
        <div class="stat-card ingresos">
            <div class="stat-label">Ingresos Totales Hoy</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['ganancias_totales'] ?? 0, 2) ?></div>
            <small style="color: #10b981;">Efectivo + Otros métodos</small>
        </div>

        <div class="stat-card gastos">
            <div class="stat-label">Gastos Hoy</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['total_gastos'] ?? 0, 2) ?></div>
            <small style="color: #ef4444;">Total egresos</small>
        </div>

        <div class="stat-card retiros">
            <div class="stat-label">Retiros Hoy</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['total_retiros'] ?? 0, 2) ?></div>
            <small style="color: #f59e0b;">Salidas de efectivo</small>
        </div>

        <div class="stat-card saldo">
            <div class="stat-label">Efectivo en Caja</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['efectivo_en_caja'] ?? 0, 2) ?></div>
            <small style="color: #3b82f6;">Saldo disponible</small>
        </div>
    </div>

    <!-- Ventas Pendientes de Cobro -->
    <div class="section-content">
        <div class="actions-bar">
            <h2 class="section-title" style="margin: 0;">
                🧾 Ventas Pendientes de Cobro
            </h2>
        </div>

        <?php if (!empty($ventasPendientes)): ?>
            <div class="table-container" style="padding: 0;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Venta #</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Pendiente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventasPendientes as $venta): ?>
                            <tr>
                                <td><strong>#<?= $venta['id'] ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($venta['cliente_nombre']) ?><br>
                                    <small style="color: #64748b;">Tel: <?= htmlspecialchars($venta['cliente_telefono'] ?? 'N/A') ?></small>
                                </td>
                                <td><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                                <td><strong>Q <?= number_format($venta['total'], 2) ?></strong></td>
                                <td>Q <?= number_format($venta['total_pagado'], 2) ?></td>
                                <td>
                                    <span class="badge pendiente">Q <?= number_format($venta['saldo_pendiente'], 2) ?></span>
                                </td>
                                <td>
                                    <a href="<?= url('/admin/pos/cobrar/' . $venta['id']) ?>"
                                        class="btn-primary">
                                        💳 Cobrar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <h3>No hay ventas pendientes de cobro</h3>
                <p>Todas las ventas están completamente pagadas</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Botones de Acciones Principales -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="<?= url('/admin/pos/nuevo-gasto') ?>" class="btn-primary" style="margin-right: 1rem;">
            📝 Registrar Gasto/Retiro
        </a>
        <a href="<?= url('/admin/pos/gastos') ?>" class="btn-secondary" style="margin-right: 1rem;">
            📊 Ver Todos los Movimientos
        </a>
        <a href="<?= url('/admin/ventas/crear') ?>" class="btn-primary">
            🛒 Nueva Venta
        </a>
    </div>
</div>
