<?php
$productos = $productos ?? [];
$resumen = $resumen ?? ['total_productos' => 0, 'total_vendidos' => 0, 'total_ingresos' => 0];
$limite = $limite ?? 20;
$orden = $orden ?? 'mas_vendidos';
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

    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .form-group select:focus {
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

    .stat-card.vendidos {
        border-color: #10b981;
    }

    .stat-card.ingresos {
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

    .stat-card.vendidos .stat-value {
        color: #10b981;
    }

    .stat-card.ingresos .stat-value {
        color: #f59e0b;
    }

    .table-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow-x: auto;
        overflow-y: auto;
        max-height: 70vh;
        -webkit-overflow-scrolling: touch;
    }

    .table-card::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .table-card::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .table-card::-webkit-scrollbar-thumb {
        background: rgba(102, 126, 234, 0.3);
        border-radius: 10px;
        border: 2px solid #f1f5f9;
    }

    .table-card::-webkit-scrollbar-thumb:hover {
        background: rgba(102, 126, 234, 0.5);
    }

    .table-card {
        scrollbar-width: thin;
        scrollbar-color: rgba(102, 126, 234, 0.3) #f1f5f9;
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
        min-width: 900px;
        border-collapse: collapse;
    }

    .reportes-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .reportes-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
        white-space: nowrap;
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

    .badge.destacado {
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
        <h1>🛒 Reporte de Productos Vendidos</h1>
        <p>Ranking y análisis de productos más vendidos</p>
    </div>

    <!-- Filtros -->
    <div class="filtros-card">
        <form method="GET" action="<?= url('/admin/reportes/productos') ?>">
            <div class="filtros-grid">
                <div class="form-group">
                    <label>🔢 Cantidad a mostrar</label>
                    <select name="limite">
                        <?php foreach ([10, 20, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limite == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>📊 Orden</label>
                    <select name="orden">
                        <option value="mas_vendidos" <?= $orden == 'mas_vendidos' ? 'selected' : '' ?>>Más vendidos</option>
                        <option value="mas_ingresos" <?= $orden == 'mas_ingresos' ? 'selected' : '' ?>>Más ingresos</option>
                    </select>
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
            <div class="stat-label">📦 Total de Productos</div>
            <div class="stat-value"><?= number_format($resumen['total_productos']) ?></div>
        </div>
        <div class="stat-card vendidos">
            <div class="stat-label">🛒 Total Vendidos</div>
            <div class="stat-value"><?= number_format($resumen['total_vendidos']) ?></div>
        </div>
        <div class="stat-card ingresos">
            <div class="stat-label">💰 Total Ingresos</div>
            <div class="stat-value">Q <?= number_format($resumen['total_ingresos'], 2) ?></div>
        </div>
    </div>

    <!-- Tabla de Productos Vendidos -->
    <div class="table-card">
        <div class="table-title">📋 Ranking de Productos</div>

        <?php if (empty($productos)): ?>
            <div class="no-data">
                <div class="no-data-icon">📭</div>
                <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">No hay productos vendidos en este período</p>
                <p style="font-size: 0.9rem;">Ajusta los filtros para ver más resultados</p>
            </div>
        <?php else: ?>
            <table class="reportes-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Vendidos</th>
                        <th>Ingresos</th>
                        <th>Destacado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $i => $prod): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($prod['nombre']) ?></td>
                            <td><?= htmlspecialchars($prod['sku']) ?></td>
                            <td style="font-weight: 700; color: #10b981; font-size: 1.1rem;">
                                <?= number_format($prod['total_vendido']) ?>
                            </td>
                            <td style="font-weight: 700; color: #f59e0b; font-size: 1.1rem;">
                                Q <?= number_format($prod['ingresos'] ?? 0, 2) ?>
                            </td>
                            <td>
                                <?php if ($i === 0): ?>
                                    <span class="badge destacado">🥇 Más vendido</span>
                                <?php elseif ($i === 1): ?>
                                    <span class="badge destacado">🥈 Segundo</span>
                                <?php elseif ($i === 2): ?>
                                    <span class="badge destacado">🥉 Tercero</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
