<?php
$resumenVentas = $resumenVentas ?? [];
$resumenCompras = $resumenCompras ?? [];
$resumenInventario = $resumenInventario ?? [];
$productosMasVendidos = $productosMasVendidos ?? [];
?>

<style>
.reportes-container {
    padding: 2rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    min-height: 100vh;
}

.reportes-header {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    padding: 1.75rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 20px rgba(10, 61, 145, 0.3);
}

.reportes-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: white;
    margin: 0 0 0.5rem 0;
}

.reportes-header p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-size: 0.95rem;
}

.reportes-nav {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.nav-card {
    background: white;
    padding: 1.25rem;
    border-radius: 0.75rem;
    text-decoration: none;
    transition: all 0.3s;
    border: 2px solid transparent;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.nav-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(10, 61, 145, 0.15);
    border-color: #0a3d91;
}

.nav-card-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.nav-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.75rem;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-left: 4px solid;
}

.stat-card.ventas {
    border-color: #10b981;
}

.stat-card.compras {
    border-color: #3b82f6;
}

.stat-card.inventario {
    border-color: #f59e0b;
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-card-icon {
    font-size: 2rem;
}

.stat-card-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0.5rem 0;
}

.stat-card-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.section-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0a3d91;
    margin: 2rem 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid #e0f2fe;
}

.section-title::before {
    content: '';
    width: 4px;
    height: 28px;
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    border-radius: 4px;
}

.productos-table {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.productos-table table {
    width: 100%;
    border-collapse: collapse;
}

.productos-table thead {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
}

.productos-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    color: white;
}

.productos-table thead tr th:first-child {
    border-radius: 0.5rem 0 0 0;
}

.productos-table thead tr th:last-child {
    border-radius: 0 0.5rem 0 0;
}

.productos-table td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.productos-table tbody tr:hover {
    background: #f8fafc;
}

.productos-table tbody tr:last-child td {
    border-bottom: none;
}

.badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.badge.success {
    background: #d1fae5;
    color: #065f46;
}

.badge.warning {
    background: #fef3c7;
    color: #92400e;
}

.badge.danger {
    background: #fee2e2;
    color: #991b1b;
}
</style>

<div class="reportes-container">
    <div class="reportes-header">
        <h1>📊 Centro de Reportes</h1>
        <p>Análisis y estadísticas del sistema</p>
    </div>

    <!-- Navegación rápida -->
    <div class="reportes-nav">
        <a href="<?= url('/admin/reportes/ventas') ?>" class="nav-card">
            <div class="nav-card-icon">💰</div>
            <div class="nav-card-title">Reporte de Ventas</div>
        </a>
        <a href="<?= url('/admin/reportes/compras') ?>" class="nav-card">
            <div class="nav-card-icon">📦</div>
            <div class="nav-card-title">Reporte de Compras</div>
        </a>
        <a href="<?= url('/admin/reportes/inventario') ?>" class="nav-card">
            <div class="nav-card-icon">📋</div>
            <div class="nav-card-title">Inventario</div>
        </a>
        <a href="<?= url('/admin/reportes/productos') ?>" class="nav-card">
            <div class="nav-card-icon">⭐</div>
            <div class="nav-card-title">Productos</div>
        </a>
        <a href="<?= url('/admin/reportes/balance') ?>" class="nav-card">
            <div class="nav-card-icon">💹</div>
            <div class="nav-card-title">Balance Financiero</div>
        </a>
    </div>

    <!-- Resumen General -->
    <div class="stats-grid">
        <!-- Ventas -->
        <div class="stat-card ventas">
            <div class="stat-card-header">
                <div class="stat-card-label">Ventas Totales</div>
                <div class="stat-card-icon">💰</div>
            </div>
            <div class="stat-card-value">Q <?= number_format($resumenVentas['monto_total'] ?? 0, 2) ?></div>
            <div class="stat-card-details">
                <div>📝 Total de ventas: <?= number_format($resumenVentas['total_ventas'] ?? 0) ?></div>
                <div>📊 Promedio por venta: Q <?= number_format($resumenVentas['promedio_venta'] ?? 0, 2) ?></div>
            </div>
        </div>

        <!-- Compras -->
        <div class="stat-card compras">
            <div class="stat-card-header">
                <div class="stat-card-label">Compras Totales</div>
                <div class="stat-card-icon">📦</div>
            </div>
            <div class="stat-card-value">Q <?= number_format($resumenCompras['monto_total'] ?? 0, 2) ?></div>
            <div class="stat-card-details">
                <div>📝 Total de compras: <?= number_format($resumenCompras['total_compras'] ?? 0) ?></div>
            </div>
        </div>

        <!-- Inventario -->
        <div class="stat-card inventario">
            <div class="stat-card-header">
                <div class="stat-card-label">Valor Inventario</div>
                <div class="stat-card-icon">📋</div>
            </div>
            <div class="stat-card-value">Q <?= number_format($resumenInventario['valor_inventario'] ?? 0, 2) ?></div>
            <div class="stat-card-details">
                <div>📦 Productos: <?= number_format($resumenInventario['total_productos'] ?? 0) ?></div>
                <div>⚠️ Bajo stock: <?= number_format($resumenInventario['productos_bajo_stock'] ?? 0) ?></div>
                <div>❌ Sin stock: <?= number_format($resumenInventario['productos_sin_stock'] ?? 0) ?></div>
            </div>
        </div>
    </div>

    <!-- Productos más vendidos -->
    <div class="section-title">⭐ Top 10 Productos Más Vendidos</div>
    <div class="productos-table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Stock Actual</th>
                    <th>Unidades Vendidas</th>
                    <th>Ingresos Generados</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productosMasVendidos)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">
                            No hay datos de ventas disponibles
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productosMasVendidos as $index => $prod): ?>
                        <tr>
                            <td style="font-weight: 700; color: #0a3d91;"><?= $index + 1 ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($prod['nombre']) ?></td>
                            <td style="font-family: 'Courier New', monospace; color: #64748b;">
                                <?= htmlspecialchars($prod['sku']) ?>
                            </td>
                            <td>
                                <?php
                                $stock = (int)$prod['stock'];
                                $badgeClass = $stock > 10 ? 'success' : ($stock > 0 ? 'warning' : 'danger');
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $stock ?> unidades</span>
                            </td>
                            <td style="font-weight: 700; color: #10b981;">
                                <?= number_format($prod['total_vendido']) ?> unidades
                            </td>
                            <td style="font-weight: 700; color: #10b981;">
                                Q <?= number_format($prod['ingresos_generados'] ?? 0, 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
