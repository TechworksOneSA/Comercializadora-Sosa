<?php
$resumenCaja = $resumenCaja ?? [];
$ultimosMovimientos = $ultimosMovimientos ?? [];
$success = $_SESSION['flash_success'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<style>
    .caja-container {
        padding: 2rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
    }

    .caja-header {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        padding: 1.75rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
    }

    .caja-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: white;
        margin: 0 0 0.5rem 0;
    }

    .caja-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-size: 0.95rem;
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
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid;
    }

    .stat-card.ingresos {
        border-color: #059669;
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
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-card.ingresos .stat-value {
        color: #059669;
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
        color: #059669;
        margin: 2rem 0 1.5rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid #d1fae5;
    }

    .btn-primary {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
    }

    .table-container {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .caja-table {
        width: 100%;
        border-collapse: collapse;
    }

    .caja-table thead {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .caja-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
    }

    .caja-table thead tr th:first-child {
        border-radius: 0.5rem 0 0 0;
    }

    .caja-table thead tr th:last-child {
        border-radius: 0 0.5rem 0 0;
    }

    .caja-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .caja-table tbody tr:hover {
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

    .badge.ingreso {
        background: #d1fae5;
        color: #065f46;
    }

    .badge.gasto {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge.retiro {
        background: #fed7aa;
        color: #9a3412;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
</style>

<div class="caja-container">
    <!-- Header -->
    <div class="caja-header">
        <h1>🏦 Gestión de Caja</h1>
        <p>Control de efectivo, gastos y movimientos de caja</p>
    </div>

    <!-- Mensajes Flash -->
    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Resumen de Caja del Día -->
    <div class="stats-cards">
        <?php if ($user['rol'] === 'ADMIN'): ?>
        <div class="stat-card ingresos">
            <div class="stat-label">💰 Ganancias Totales</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['ganancias_totales'], 2) ?></div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">
                Efectivo + Tarjeta + Transferencia
            </div>
        </div>
        <?php endif; ?>

        <div class="stat-card saldo">
            <div class="stat-label">💵 Efectivo en Caja</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['efectivo_en_caja'], 2) ?></div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">
                Solo efectivo físico disponible
            </div>
        </div>

        <div class="stat-card gastos">
            <div class="stat-label">📤 Gastos del Día</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['total_gastos'], 2) ?></div>
        </div>

        <div class="stat-card retiros">
            <div class="stat-label">🏦 Retiros del Día</div>
            <div class="stat-value">Q <?= number_format($resumenCaja['total_retiros'], 2) ?></div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="section-title">
        <span>💼 Acciones de Caja</span>
    </div>
    <div style="margin-bottom: 1.5rem;">
        <a href="<?= url('/admin/caja/movimientos') ?>" class="btn-primary" style="font-size:1.1rem;">
            📊 Movimientos de Caja
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <a href="<?= url('/admin/caja/nuevo-movimiento') ?>" style="background: white; padding: 1.5rem; border-radius: 1rem; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #ef4444; transition: all 0.3s;"
            onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📤</div>
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">Registrar Gasto</div>
            <div style="font-size: 0.875rem; color: #64748b;">Gastos operativos y retiros</div>
        </a>

        <a href="<?= url('/admin/reportes/balance') ?>" style="background: white; padding: 1.5rem; border-radius: 1rem; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #3b82f6; transition: all 0.3s;"
            onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📊</div>
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">Ver Balance</div>
            <div style="font-size: 0.875rem; color: #64748b;">Reporte financiero</div>
        </a>

        <a href="<?= url('/admin/pos') ?>" style="background: white; padding: 1.5rem; border-radius: 1rem; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #059669; transition: all 0.3s;"
            onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💰</div>
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">Punto de Venta</div>
            <div style="font-size: 0.875rem; color: #64748b;">Cobros pendientes</div>
        </a>
    </div>

    <!-- Últimos Movimientos -->
    <?php if (!empty($ultimosMovimientos)): ?>
        <div class="section-title">
            <span>🕐 Últimos 10 Movimientos</span>
        </div>
        <div>
            (Si desea ver todos los Movimientos ve a Movimientos de Caja)
        </div>
        <div class="table-container">
            <table class="caja-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimosMovimientos as $mov): ?>
                        <tr>
                            <td>📅 <?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                            <td>
                                <span class="badge <?= $mov['tipo'] ?>">
                                    <?php
                                    echo match ($mov['tipo']) {
                                        'ingreso' => '💵 Ingreso',
                                        'gasto' => '📤 Gasto',
                                        'retiro' => '🏦 Retiro',
                                        default => $mov['tipo']
                                    };
                                    ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($mov['concepto']) ?></td>
                            <td><?= htmlspecialchars($mov['metodo_pago']) ?></td>
                            <td style="font-weight: 700; color: <?= $mov['tipo'] === 'ingreso' ? '#10b981' : '#ef4444' ?>;">
                                <?= $mov['tipo'] === 'ingreso' ? '+' : '-' ?>Q <?= number_format($mov['monto'], 2) ?>
                            </td>
                            <td><?= htmlspecialchars($mov['usuario_nombre']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
