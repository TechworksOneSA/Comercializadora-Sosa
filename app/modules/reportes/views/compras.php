<?php
$compras = $compras ?? [];
$resumen = $resumen ?? ['total_compras' => 0, 'monto_total' => 0, 'promedio_compra' => 0];
$fechaInicio = $fechaInicio ?? date('Y-m-01');
$fechaFin = $fechaFin ?? date('Y-m-d');
?>

<style>
    .reportes-container {
        padding: 2rem;
        background: #f8fafc;
        min-height: 100vh;
    }

    .reportes-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .reportes-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .reportes-header p {
        opacity: 0.95;
        margin: 0;
    }

    .filtros-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .filtros-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .stats-grid {
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

    .stat-card.total {
        border-color: #667eea;
    }

    .stat-card.cantidad {
        border-color: #10b981;
    }

    .stat-card.promedio {
        border-color: #f59e0b;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-card.total .stat-value {
        color: #667eea;
    }

    .stat-card.cantidad .stat-value {
        color: #10b981;
    }

    .stat-card.promedio .stat-value {
        color: #f59e0b;
    }

    .table-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .reportes-table {
        width: 100%;
        border-collapse: collapse;
    }

    .reportes-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .reportes-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
    }

    .reportes-table thead tr th:first-child {
        border-radius: 0.5rem 0 0 0;
    }

    .reportes-table thead tr th:last-child {
        border-radius: 0 0.5rem 0 0;
    }

    .reportes-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .reportes-table tbody tr:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge.completada {
        background: #d1fae5;
        color: #065f46;
    }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .no-data-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>

<div class="reportes-container">
    <!-- Header -->
    <div class="reportes-header">
        <h1>📦 Reporte de Compras</h1>
        <p>Análisis detallado de compras y adquisiciones</p>
    </div>

    <!-- Filtros -->
    <div class="filtros-card">
        <form method="GET" action="<?= url('/admin/reportes/compras') ?>">
            <div class="filtros-grid">
                <div class="form-group">
                    <label>📅 Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>" required>
                </div>
                <div class="form-group">
                    <label>📅 Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" required>
                </div>
                <div>
                    <button type="submit" class="btn-primary">🔍 Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Estadísticas Resumen -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-label">💰 Total Invertido</div>
            <div class="stat-value">Q <?= number_format($resumen['monto_total'], 2) ?></div>
        </div>
        <div class="stat-card cantidad">
            <div class="stat-label">📦 Cantidad de Compras</div>
            <div class="stat-value"><?= number_format($resumen['total_compras']) ?></div>
        </div>
        <div class="stat-card promedio">
            <div class="stat-label">📊 Promedio por Compra</div>
            <div class="stat-value">Q <?= number_format($resumen['promedio_compra'], 2) ?></div>
        </div>
    </div>

    <!-- Tabla de Compras -->
    <div class="table-card">
        <div class="table-title">📋 Listado de Compras</div>

        <?php if (empty($compras)): ?>
            <div class="no-data">
                <div class="no-data-icon">📭</div>
                <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">No hay compras en este período</p>
                <p style="font-size: 0.9rem;">Ajusta las fechas del filtro para ver más resultados</p>
            </div>
        <?php else: ?>
            <table class="reportes-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>No. Factura</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $compra): ?>
                        <tr>
                            <td style="font-weight: 600;">#<?= $compra['id'] ?></td>
                            <td>📅 <?= date('d/m/Y', strtotime($compra['fecha'] ?? 'now')) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($compra['proveedor_nombre'] ?? 'Proveedor N/A') ?></strong><br>
                                <small style="color: #64748b;">NIT: <?= htmlspecialchars($compra['proveedor_nit'] ?? 'N/A') ?></small>
                            </td>
                            <td><?= htmlspecialchars($compra['numero_factura'] ?? 'N/A') ?></td>
                            <td style="font-weight: 700; color: #667eea; font-size: 1.1rem;">
                                Q <?= number_format($compra['total'] ?? 0, 2) ?>
                            </td>
                            <td>
                                <span class="badge completada">✅ <?= ucfirst($compra['estado'] ?? 'REGISTRADA') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
