<?php
$productos = $productos ?? [];
$resumen = $resumen ?? [
    'total_productos' => 0,
    'total_stock' => 0,
    'valor_inventario' => 0,
    'productos_bajo_stock' => 0,
    'productos_sin_stock' => 0
];
$filtro = $filtro ?? 'todos';
?>

<style>
    .inventario-container {
        padding: 2rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
    }

    .inventario-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        padding: 1.75rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .inventario-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0;
    }

    .btn-volver {
        background: white;
        color: #3b82f6;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-volver:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        text-decoration: none;
        color: #3b82f6;
    }

    .filtros-container {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .filtros-grid {
        display: grid;
        grid-template-columns: auto auto auto;
        gap: 1rem;
        align-items: center;
    }

    .filtro-btn {
        padding: 0.75rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        background: white;
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        text-align: center;
        display: inline-block;
    }

    .filtro-btn:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        text-decoration: none;
    }

    .filtro-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: #3b82f6;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #6b7280;
        font-weight: 500;
    }

    .productos-table {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .table-container {
        overflow-x: auto;
        max-height: 70vh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-container::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.3);
        border-radius: 10px;
        border: 2px solid #f1f5f9;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.5);
    }

    .table-container {
        scrollbar-width: thin;
        scrollbar-color: rgba(59, 130, 246, 0.3) #f1f5f9;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    th {
        background: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    td {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        color: #4b5563;
    }

    tr:hover {
        background: #f9fafb;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge.success {
        background: #dcfce7;
        color: #166534;
    }

    .badge.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge.danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: #9ca3af;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .inventario-container {
            padding: 1rem;
        }

        .inventario-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .filtros-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="inventario-container">
    <div class="inventario-header">
        <h1>📋 Reporte de Inventario</h1>
        <a href="<?= url('/admin/reportes') ?>" class="btn-volver">← Volver a Reportes</a>
    </div>

    <!-- Filtros -->
    <div class="filtros-container">
        <div class="filtros-grid">
            <a href="<?= url('/admin/reportes/inventario?filtro=todos') ?>"
                class="filtro-btn <?= $filtro == 'todos' ? 'active' : '' ?>">
                📦 Todos los Productos
            </a>
            <a href="<?= url('/admin/reportes/inventario?filtro=bajo_stock') ?>"
                class="filtro-btn <?= $filtro == 'bajo_stock' ? 'active' : '' ?>">
                ⚠️ Bajo Stock
            </a>
            <a href="<?= url('/admin/reportes/inventario?filtro=sin_stock') ?>"
                class="filtro-btn <?= $filtro == 'sin_stock' ? 'active' : '' ?>">
                ❌ Sin Stock
            </a>
        </div>
    </div>

    <!-- Estadísticas del Inventario -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= number_format($resumen['total_productos'] ?? 0) ?></div>
            <div class="stat-label">Total Productos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= number_format($resumen['total_stock'] ?? 0) ?></div>
            <div class="stat-label">Stock Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">Q <?= number_format($resumen['valor_inventario'] ?? 0, 2) ?></div>
            <div class="stat-label">Valor del Inventario</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= number_format($resumen['productos_bajo_stock'] ?? 0) ?></div>
            <div class="stat-label">Productos Bajo Stock</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= number_format($resumen['productos_sin_stock'] ?? 0) ?></div>
            <div class="stat-label">Productos Sin Stock</div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="productos-table">
        <div class="table-header">
            <h3 class="table-title">
                <?php if ($filtro == 'bajo_stock'): ?>
                    ⚠️ Productos con Bajo Stock
                <?php elseif ($filtro == 'sin_stock'): ?>
                    ❌ Productos Sin Stock
                <?php else: ?>
                    📦 Inventario Completo
                <?php endif; ?>
                (<?= count($productos) ?> productos)
            </h3>
        </div>

        <div class="table-container">
            <?php if (!empty($productos)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                            <th>Estado Stock</th>
                            <th>Costo Actual</th>
                            <th>Precio Venta</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 600;">
                                    <?= htmlspecialchars($producto['sku'] ?? '') ?>
                                </td>
                                <td style="font-weight: 600; color: #1f2937;">
                                    <?= htmlspecialchars($producto['nombre'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($producto['categoria'] ?? 'Sin categoría') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($producto['marca'] ?? 'Sin marca') ?>
                                </td>
                                <td style="font-weight: 600; text-align: center;">
                                    <?= number_format($producto['stock'] ?? 0) ?>
                                </td>
                                <td style="text-align: center;">
                                    <?= number_format($producto['stock_minimo'] ?? 0) ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php
                                    $stock = (int)($producto['stock'] ?? 0);
                                    $stockMinimo = (int)($producto['stock_minimo'] ?? 0);

                                    if ($stock == 0) {
                                        echo '<span class="badge danger">Sin Stock</span>';
                                    } elseif ($stock <= $stockMinimo) {
                                        echo '<span class="badge warning">Bajo Stock</span>';
                                    } else {
                                        echo '<span class="badge success">Stock Normal</span>';
                                    }
                                    ?>
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    Q <?= number_format($producto['costo_actual'] ?? 0, 2) ?>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: #059669;">
                                    Q <?= number_format($producto['precio_venta'] ?? 0, 2) ?>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #3b82f6;">
                                    Q <?= number_format($producto['valor'] ?? 0, 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>📦 No se encontraron productos con los filtros aplicados</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
