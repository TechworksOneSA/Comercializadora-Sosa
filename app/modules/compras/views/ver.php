<?php
$compra = $compra ?? null;
$detalles = $detalles ?? [];

if (!$compra) {
    echo '<div class="alert alert-danger">Compra no encontrada</div>';
    return;
}

// Calcular totales
$totalBruto = 0;
$totalDescuento = 0;
foreach ($detalles as $det) {
    $totalBruto += (float)$det['cantidad'] * (float)$det['costo_unitario'];
    $totalDescuento += (float)($det['descuento'] ?? 0);
}
$totalNeto = $totalBruto - $totalDescuento;
?>

<style>
.compras-container {
    padding: 2rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    min-height: 100vh;
}

.compras-header {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    padding: 1.75rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 20px rgba(10, 61, 145, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.compras-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: white;
    margin: 0;
}

.compras-header p {
    color: rgba(255, 255, 255, 0.9);
    margin-top: 0.5rem;
    font-size: 0.95rem;
}

.btn-volver {
    background: white;
    color: #0a3d91;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-volver:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.compra-details-container {
    background: white;
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(10, 61, 145, 0.12);
    max-width: 1200px;
    margin: 0 auto;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
    border-radius: 1rem;
    border: 2px solid #e0f2fe;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
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
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    overflow: hidden;
}

.productos-table thead {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    color: white;
}

.productos-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
}

.productos-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.productos-table tbody tr:hover {
    background: #f8fafc;
}

.productos-table tbody tr:last-child td {
    border-bottom: none;
}

.producto-nombre {
    font-weight: 600;
    color: #1e293b;
}

.producto-sku {
    font-size: 0.85rem;
    color: #64748b;
    font-family: 'Courier New', monospace;
    margin-top: 0.25rem;
}

.totales-panel {
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
    border: 2px solid #0a3d91;
    border-radius: 0.75rem;
    padding: 1.5rem;
    max-width: 400px;
    margin-left: auto;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.total-row:last-child {
    border-bottom: none;
    margin-top: 0.5rem;
    padding-top: 1rem;
    border-top: 2px solid #0a3d91;
}

.total-row label {
    font-weight: 600;
    color: #1e293b;
}

.total-row .value {
    font-weight: 700;
    color: #0a3d91;
    font-size: 1.1rem;
}

.total-row:last-child .value {
    font-size: 1.5rem;
    color: #10b981;
}

.actions-bar {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #e2e8f0;
}

.btn-action {
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
}

.btn-editar {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-editar:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 2px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #e2e8f0;
}
</style>

<div class="compras-container">
    <div class="compras-header">
        <div>
            <h1>👁️ Detalle de Compra #<?= htmlspecialchars($compra['id']) ?></h1>
            <p>Visualice la información completa de esta compra</p>
        </div>
        <a href="<?= url('/admin/compras') ?>" class="btn-volver">← Volver al listado</a>
    </div>

    <div class="compra-details-container">
        <!-- Información general -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">📅 Fecha de Compra</div>
                <div class="info-value"><?= htmlspecialchars($compra['fecha_compra']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-label">📄 Número de Documento</div>
                <div class="info-value"><?= htmlspecialchars($compra['numero_doc'] ?: 'Sin número') ?></div>
            </div>

            <div class="info-item">
                <div class="info-label">🏢 Proveedor</div>
                <div class="info-value">
                    <?php
                    // Obtener info del proveedor si existe
                    $proveedorId = $compra['proveedor_id'] ?? 0;
                    if ($proveedorId > 0) {
                        require_once __DIR__ . '/../../proveedores/ProveedoresModel.php';
                        $provModel = new ProveedoresModel();
                        $prov = $provModel->obtenerPorId($proveedorId);
                        echo htmlspecialchars($prov['nombre'] ?? 'N/A');
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">💰 Total de Compra</div>
                <div class="info-value" style="color: #10b981;">Q <?= number_format($totalNeto, 2) ?></div>
            </div>
        </div>

        <div class="section-title">📋 Productos de la Compra</div>

        <!-- Tabla de productos -->
        <table class="productos-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Producto</th>
                    <th style="width: 15%;">Cantidad</th>
                    <th style="width: 15%;">Costo Unitario</th>
                    <th style="width: 15%;">Descuento</th>
                    <th style="width: 15%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detalles)): ?>
                    <?php foreach ($detalles as $det): ?>
                        <?php
                        $cantidad = (float)$det['cantidad'];
                        $costo = (float)$det['costo_unitario'];
                        $descuento = (float)($det['descuento'] ?? 0);
                        $subtotal = ($cantidad * $costo) - $descuento;
                        ?>
                        <tr>
                            <td>
                                <div class="producto-nombre"><?= htmlspecialchars($det['producto_nombre'] ?? 'N/A') ?></div>
                                <?php if (isset($det['producto_sku'])): ?>
                                    <div class="producto-sku">SKU: <?= htmlspecialchars($det['producto_sku']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-weight: 600;"><?= number_format($cantidad, 2) ?></td>
                            <td style="text-align: right;">Q <?= number_format($costo, 2) ?></td>
                            <td style="text-align: right; color: #ef4444;">
                                <?= $descuento > 0 ? '- Q ' . number_format($descuento, 2) : 'Q 0.00' ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #10b981;">
                                Q <?= number_format($subtotal, 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">
                            No hay productos en esta compra
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totales-panel">
            <div class="total-row">
                <label>Total Bruto:</label>
                <span class="value">Q <?= number_format($totalBruto, 2) ?></span>
            </div>
            <div class="total-row">
                <label>Descuentos:</label>
                <span class="value" style="color: #ef4444;">- Q <?= number_format($totalDescuento, 2) ?></span>
            </div>
            <div class="total-row">
                <label>TOTAL NETO:</label>
                <span class="value">Q <?= number_format($totalNeto, 2) ?></span>
            </div>
        </div>

        <!-- Acciones -->
        <div class="actions-bar">
            <a href="<?= url('/admin/compras') ?>" class="btn-action btn-secondary">Volver al listado</a>
            <a href="<?= url('/admin/compras/editar/' . $compra['id']) ?>" class="btn-action btn-editar">
                ✏️ Editar Compra
            </a>
        </div>
    </div>
</div>
