<?php
$errors = $errors ?? [];
$old = $old ?? [];
$productos = $productos ?? [];
$proveedores = $proveedores ?? [];
$series_existentes = $series_existentes ?? []; // ✅ Series existentes

// Para JS: lista de productos
$productosJs = [];
foreach ($productos as $p) {
    $productoData = [
        'id'    => (int)$p['id'],
        'nombre' => $p['nombre'],
        'sku'   => $p['sku'] ?? '',
        'codigo_barra' => $p['codigo_barra'] ?? '',
        'tipo_producto' => $p['tipo_producto'] ?? 'MISC',
        'costo' => isset($p['costo_actual']) ? (float)$p['costo_actual'] : 0,
        'stock_actual' => (int)($p['stock'] ?? 0),
        'numero_serie' => '', // Inicializar vacío para búsqueda por serie
    ];

    // ✅ Agregar serie existente si tiene (guardada en productos.numero_serie)
    if (isset($series_existentes[$p['id']])) {
        $productoData['serie_actual'] = $series_existentes[$p['id']];
        $productoData['numero_serie'] = $series_existentes[$p['id']];
    }

    $productosJs[] = $productoData;
}
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
    }

    .form-container {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(10, 61, 145, 0.12);
        max-width: 1400px;
        margin: 0 auto;
    }

    .alert-errors {
        background: #fee2e2;
        border-left: 4px solid #ef4444;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-errors ul {
        margin: 0;
        padding-left: 1.5rem;
        color: #991b1b;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .form-label .required {
        color: #ef4444;
    }

    .form-input,
    .form-select {
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #0a3d91;
        box-shadow: 0 0 0 4px rgba(10, 61, 145, 0.1);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .productos-table thead {
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
        color: #ffffff;
    }

    .productos-table th {
        padding: 1.125rem 1rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        vertical-align: middle;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .productos-table td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }

    .productos-table tbody tr {
        transition: background-color 0.2s;
    }

    .productos-table tbody tr:hover {
        background: #f8fafc;
    }

    .productos-table tbody tr:last-child td {
        border-bottom: none;
    }

    .productos-table input {
        width: 100%;
        padding: 0.625rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.375rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .productos-table input:hover {
        border-color: #94a3b8;
    }

    .productos-table input:focus {
        outline: none;
        border-color: #0a3d91;
        box-shadow: 0 0 0 3px rgba(10, 61, 145, 0.1);
    }

    .btn-remove {
        background: #ef4444;
        color: white;
        border: none;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-remove:hover {
        background: #dc2626;
        transform: scale(1.05);
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

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #e2e8f0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
        color: white;
        padding: 1rem 2.5rem;
        border: none;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 1.05rem;
        box-shadow: 0 6px 20px rgba(10, 61, 145, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(10, 61, 145, 0.4);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        padding: 1rem 2rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
    }

    .producto-info {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .producto-nombre {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .producto-sku {
        font-size: 0.8rem;
        color: #64748b;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.3px;
    }

    .stock-badge {
        display: inline-block;
        padding: 0.25rem 0.625rem;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0369a1;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
        border: 1px solid #7dd3fc;
    }

    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #0a3d91;
        border-top: none;
        border-radius: 0 0 0.75rem 0.75rem;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        display: none;
    }

    .autocomplete-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #e2e8f0;
        transition: background 0.2s;
    }

    .autocomplete-item:hover {
        background: #f0f9ff;
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    /* Scanner */
    #productoScanner {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #0a3d91;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        background: #fff;
        transition: all 0.3s;
    }

    #productoScanner:focus {
        outline: none;
        border-color: #1565c0;
        box-shadow: 0 0 0 4px rgba(10, 61, 145, 0.15);
        background: #f0f9ff;
    }

    /* Facebook-style Selectors */
    .fbselect {
        position: relative;
    }

    .fbselect-input {
        width: 100% !important;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        font-weight: 500;
        color: #1e293b;
        transition: all .3s ease;
        background: white;
        font-family: inherit;
        cursor: pointer;
    }

    .fbselect-input:focus {
        outline: none;
        border-color: #0a3d91;
        box-shadow: 0 0 0 4px rgba(10, 61, 145, 0.1);
        cursor: text;
    }

    .fbselect-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .fbselect-open .fbselect-input {
        border-color: #0a3d91;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        box-shadow: 0 0 0 4px rgba(10, 61, 145, 0.1);
    }

    .fbselect-menu {
        display: none;
        position: absolute;
        left: 0;
        right: 0;
        z-index: 1000;
        background: white;
        border: 2px solid #0a3d91;
        border-top: none;
        border-radius: 0 0 0.75rem 0.75rem;
        box-shadow: 0 8px 20px rgba(10, 61, 145, 0.2);
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .fbselect-menu-up {
        top: auto !important;
        bottom: 100% !important;
        border-top: 2px solid #0a3d91;
        border-bottom: none;
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }

    .fbselect-menu-down {
        top: 100% !important;
        bottom: auto !important;
        border-top: none;
        border-bottom: 2px solid #0a3d91;
        border-radius: 0 0 0.75rem 0.75rem !important;
    }

    .fbselect-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        color: #374151;
        font-size: 0.95rem;
        font-weight: 500;
        border-bottom: 1px solid #f3f4f6;
        transition: all .2s ease;
        display: block;
        background: white;
    }

    .fbselect-item:last-child {
        border-bottom: none;
    }

    .fbselect-item:hover,
    .fbselect-item.highlighted {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        color: #0a3d91;
        font-weight: 600;
        padding-left: 1.25rem;
    }

    .fbselect-item:active {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    }

    /* Alerta emergente para productos con serie */
    .alert-serie {
        position: relative;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 2px solid #3b82f6;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: none;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        animation: slideDown 0.3s ease-out;
    }

    .alert-serie.show { display: flex; }

    .alert-serie-icon { font-size: 1.8rem; flex-shrink: 0; }
    .alert-serie-content { flex: 1; }

    .alert-serie-title {
        font-weight: 700;
        color: #1e40af;
        font-size: 1.05rem;
        margin-bottom: 0.25rem;
    }

    .alert-serie-text {
        color: #1e3a8a;
        font-size: 0.9rem;
    }

    .alert-serie-sku {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        background: white;
        padding: 0.125rem 0.5rem;
        border-radius: 0.25rem;
    }

    .alert-serie-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #1e40af;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        transition: background 0.2s;
    }

    .alert-serie-close:hover { background: rgba(255, 255, 255, 0.5); }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modal para ingresar serie (única) */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        animation: fadeIn 0.3s ease-out;
    }
    .modal-overlay.show { display: block; }

    .modal-series {
        display: none;
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow: hidden;
        opacity: 0;
        transition: all 0.3s ease-out;
    }
    .modal-series.show {
        display: flex;
        flex-direction: column;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .modal-header {
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
        padding: 1.5rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 700; }

    .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .modal-close:hover { background: rgba(255, 255, 255, 0.3); }

    .modal-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex: 1;
    }

    .series-input-group { margin-bottom: 1rem; }

    .series-input-label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .series-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-family: 'Courier New', monospace;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .series-input:focus {
        outline: none;
        border-color: #0a3d91;
        box-shadow: 0 0 0 3px rgba(10, 61, 145, 0.1);
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 2px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .btn-modal-cancel {
        background: #e2e8f0;
        color: #475569;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-modal-cancel:hover { background: #cbd5e1; }

    .btn-modal-save {
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(10, 61, 145, 0.3);
    }
    .btn-modal-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(10, 61, 145, 0.4);
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="compras-container">
    <div class="compras-header">
        <div>
            <h1>📦 Nueva Entrada de Inventario</h1>
            <p>Registre compras a proveedores y actualice el stock automáticamente</p>
        </div>
        <a href="<?= url('/admin/compras') ?>" class="btn-volver">← Volver</a>
    </div>

    <!-- Alerta para productos con serie -->
    <div class="alert-serie" id="alertSerie">
        <span class="alert-serie-icon">📦</span>
        <div class="alert-serie-content">
            <div class="alert-serie-title">Producto con Serie Detectado</div>
            <div class="alert-serie-text">
                <span id="alertSerieProducto"></span> | SKU: <span class="alert-serie-sku" id="alertSerieSku"></span>
            </div>
        </div>
        <button type="button" class="alert-serie-close" onclick="closeAlertSerie()">×</button>
    </div>

    <div class="form-container">
        <?php if (!empty($errors)): ?>
            <div class="alert-errors">
                <strong>⚠️ Errores encontrados:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/admin/compras/guardar') ?>" id="formCompra">
            <!-- Datos del encabezado -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Proveedor <span class="required">*</span></label>
                    <div class="fbselect" data-name="proveedor_id" data-required="true">
                        <input type="text"
                            placeholder="Buscar proveedor..."
                            class="fbselect-input"
                            autocomplete="off">
                        <input type="hidden" name="proveedor_id" value="<?= htmlspecialchars($old['proveedor_id'] ?? '') ?>">
                        <div class="fbselect-menu">
                            <?php foreach ($proveedores as $prov): ?>
                                <div class="fbselect-item"
                                    data-value="<?= (int)$prov['id'] ?>"
                                    data-search="<?= strtolower($prov['nombre'] . ' ' . $prov['nit']) ?>">
                                    <?= htmlspecialchars($prov['nombre']) ?> (<?= htmlspecialchars($prov['nit']) ?>)
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de Compra <span class="required">*</span></label>
                    <input type="date" name="fecha_compra" class="form-input"
                        value="<?= htmlspecialchars($old['fecha_compra'] ?? date('Y-m-d')) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Número de Documento</label>
                    <input type="text" name="numero_doc" class="form-input"
                        placeholder="Factura, recibo, etc."
                        value="<?= htmlspecialchars($old['numero_doc'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Notas</label>
                    <input type="text" name="notas" class="form-input"
                        placeholder="Observaciones adicionales"
                        value="<?= htmlspecialchars($old['notas'] ?? '') ?>">
                </div>
            </div>

            <div class="section-title">📋 Productos de la Compra</div>

            <!-- Búsqueda Manual -->
            <div style="position: relative; margin-bottom: 1rem;">
                <label class="form-label" style="margin-bottom: 0.5rem; display: block; font-weight: 600; color: #1e293b;">
                    🔍 Buscar Producto por Nombre
                </label>
                <input type="text" id="buscarProducto"
                    placeholder="🔍 Buscar por nombre"
                    autocomplete="off"
                    style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #0a3d91; border-radius: 0.5rem; font-size: 0.95rem; background: white;"
                    onfocus="this.style.borderColor='#0a3d91'; mostrarResultadosProductos()"
                    onblur="setTimeout(() => ocultarResultadosProductos(), 200)"
                    oninput="buscarProductos()">
                <div class="autocomplete-dropdown" id="resultadosProductos" style="display: none;"></div>
            </div>

            <!-- Scanner -->
            <div style="margin-bottom: 1.5rem; position: relative;">
                <label class="form-label" style="margin-bottom: 0.5rem; display: block; font-weight: 600; color: #1e293b;">
                    📷 Escanear Serie / Código de Barra / SKU
                </label>
                <input type="text" id="productoScanner"
                    placeholder="Escanear serie / código de barra / SKU y presione Enter"
                    autocomplete="off"
                    style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #0a3d91; border-radius: 0.5rem; font-size: 0.95rem; margin: 0 0 1rem; background: white;">
                <div class="autocomplete-dropdown" id="autocompleteDropdown" style="display: none;"></div>
            </div>

            <!-- Tabla de productos -->
            <table class="productos-table" id="tablaProductos">
                <thead>
                    <tr>
                        <th style="width: 35%;">Producto</th>
                        <th style="width: 15%;">Cantidad</th>
                        <th style="width: 15%;">Costo Unitario</th>
                        <th style="width: 15%;">Descuento</th>
                        <th style="width: 15%;">Subtotal</th>
                        <th style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="productosBody">
                    <!-- Filas se agregan dinámicamente -->
                </tbody>
            </table>

            <!-- Totales -->
            <div class="totales-panel">
                <div class="total-row">
                    <label>Total Bruto:</label>
                    <span class="value" id="totalBrutoView">Q 0.00</span>
                </div>
                <div class="total-row">
                    <label>Descuentos:</label>
                    <span class="value" id="totalDescuentoView">Q 0.00</span>
                </div>
                <div class="total-row">
                    <label>TOTAL NETO:</label>
                    <span class="value" id="totalNetoView">Q 0.00</span>
                </div>
            </div>

            <input type="hidden" name="productos_json" id="productosJson">

            <div class="form-actions">
                <a href="<?= url('/admin/compras') ?>" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">✅ Guardar Compra e Ingresar al Inventario</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ingresar serie única -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalSeries()"></div>
<div class="modal-series" id="modalSeries">
    <div class="modal-header">
        <h3>📦 Número de Serie del Producto</h3>
        <button type="button" class="modal-close" onclick="closeModalSeries()">×</button>
    </div>
    <div class="modal-body" id="modalSeriesBody"></div>
    <div class="modal-footer">
        <button type="button" class="btn-modal-cancel" onclick="closeModalSeries()">Cancelar</button>
        <button type="button" class="btn-modal-save" onclick="saveSeriesModal()">Guardar Serie</button>
    </div>
</div>

<!-- JavaScript del módulo -->
<script src="<?= url('/assets/js/facebook-selectors.js?v=' . time()) ?>"></script>
<script>
    const PRODUCTOS = <?= json_encode($productosJs, JSON_UNESCAPED_UNICODE) ?>;
    let productosEnCompra = [];
    let currentProductoIndex = null;

    // Toast simple para notificaciones
    function showToast(type, message) {
        const colors = {
            success: '#28a745',
            warning: '#ffc107',
            error:   '#dc3545',
            info:    '#17a2b8'
        };

        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.right = '18px';
        toast.style.top = '18px';
        toast.style.zIndex = '99999';
        toast.style.padding = '12px 14px';
        toast.style.borderRadius = '10px';
        toast.style.color = '#fff';
        toast.style.fontWeight = '700';
        toast.style.fontSize = '0.95rem';
        toast.style.boxShadow = '0 8px 18px rgba(0,0,0,0.18)';
        toast.style.background = colors[type] || colors.info;
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all .18s ease';

        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(() => toast.remove(), 200);
        }, 1600);
    }

    // Alerta de serie detectada
    function showAlertSerie(producto) {
        const alert = document.getElementById('alertSerie');
        document.getElementById('alertSerieProducto').textContent = producto.nombre;
        document.getElementById('alertSerieSku').textContent = producto.sku;
        alert.classList.add('show');

        setTimeout(() => closeAlertSerie(), 5000);
    }

    function closeAlertSerie() {
        document.getElementById('alertSerie').classList.remove('show');
    }

    // Modal de serie única
    function openModalSeries(index) {
        const producto = productosEnCompra[index];
        currentProductoIndex = index;

        const modalBody = document.getElementById('modalSeriesBody');
        const serieActual = producto.serie || '';

        modalBody.innerHTML = `
            <div class="series-input-group">
                <label class="series-input-label">Número de Serie del Producto</label>
                <input type="text"
                       class="series-input"
                       id="serie_unica"
                       value="${serieActual.replace(/"/g, '&quot;')}"
                       placeholder="Ingrese el número de serie"
                       autocomplete="off">
                <small style="color: #666; font-size: 0.85rem; margin-top: 8px; display: block;">
                    📝 Este número de serie se guardará para este producto.
                </small>
            </div>
        `;

        document.getElementById('modalOverlay').classList.add('show');
        document.getElementById('modalSeries').classList.add('show');

        setTimeout(() => document.getElementById('serie_unica')?.focus(), 100);
    }

    function closeModalSeries() {
        document.getElementById('modalOverlay').classList.remove('show');
        document.getElementById('modalSeries').classList.remove('show');
        currentProductoIndex = null;
    }

    function saveSeriesModal() {
        if (currentProductoIndex === null) return;
        const producto = productosEnCompra[currentProductoIndex];
        const input = document.getElementById('serie_unica');
        const serie = input?.value.trim() || '';

        producto.serie = serie; // ✅ string único

        closeModalSeries();
        renderizarTabla();
    }

    // Buscar productos
    function buscarProducto(query) {
        query = (query || '').toLowerCase().trim();
        if (!query) return [];

        return PRODUCTOS.filter(p =>
            (p.nombre || '').toLowerCase().includes(query) ||
            (p.sku || '').toLowerCase().includes(query) ||
            (p.codigo_barra || '').toLowerCase().includes(query) ||
            (p.numero_serie && (p.numero_serie || '').toLowerCase().includes(query))
        ).slice(0, 10);
    }

    // ====== BÚSQUEDA MANUAL ======
    function buscarProductos() {
        const buscarProductoInput = document.getElementById('buscarProducto');
        const resultadosProductosDiv = document.getElementById('resultadosProductos');
        if (!buscarProductoInput || !resultadosProductosDiv) return;

        const termino = (buscarProductoInput.value || '').toLowerCase().trim();

        if (!termino) {
            resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
            resultadosProductosDiv.style.display = 'block';
            return;
        }

        const resultados = buscarProducto(termino);

        if (resultados.length === 0) {
            resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">No se encontraron productos</div>';
            resultadosProductosDiv.style.display = 'block';
            return;
        }

        let html = '';
        resultados.forEach(producto => {
            let infoExtra = `SKU: ${producto.sku} | Stock: ${producto.stock_actual}`;
            if (producto.numero_serie) infoExtra += ` | Serie: ${producto.numero_serie}`;
            html += `
                <div class="autocomplete-item" onclick="seleccionarProductoManual(${producto.id})" style="cursor: pointer;">
                    <div class="producto-nombre">${producto.nombre}</div>
                    <div class="producto-sku">${infoExtra}</div>
                </div>
            `;
        });

        resultadosProductosDiv.innerHTML = html;
        resultadosProductosDiv.style.display = 'block';
    }

    function seleccionarProductoManual(productoId) {
        const producto = PRODUCTOS.find(p => p.id === productoId);
        if (producto) {
            agregarProducto(producto);
            const buscarProductoInput = document.getElementById('buscarProducto');
            const resultadosProductosDiv = document.getElementById('resultadosProductos');
            if (buscarProductoInput) buscarProductoInput.value = '';
            if (resultadosProductosDiv) resultadosProductosDiv.style.display = 'none';
        }
    }

    function mostrarResultadosProductos() {
        const buscarProductoInput = document.getElementById('buscarProducto');
        const resultadosProductosDiv = document.getElementById('resultadosProductos');
        if (!buscarProductoInput || !resultadosProductosDiv) return;

        if (buscarProductoInput.value) buscarProductos();
        else {
            resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
            resultadosProductosDiv.style.display = 'block';
        }
    }

    function ocultarResultadosProductos() {
        const resultadosProductosDiv = document.getElementById('resultadosProductos');
        if (resultadosProductosDiv) resultadosProductosDiv.style.display = 'none';
    }
    // ====== FIN BÚSQUEDA MANUAL ======

    // Autocompletado scanner
    const scanner = document.getElementById('productoScanner');
    const dropdown = document.getElementById('autocompleteDropdown');
    let processingScan = false;

    scanner.addEventListener('input', function() {
        const resultados = buscarProducto(this.value);

        if (resultados.length > 0) {
            dropdown.innerHTML = resultados.map(p => {
                let infoExtra = `SKU: ${p.sku} | Stock: ${p.stock_actual}`;
                if (p.numero_serie) infoExtra += ` | Serie: ${p.numero_serie}`;
                return `
                    <div class="autocomplete-item" data-id="${p.id}">
                        <div class="producto-nombre">${p.nombre}</div>
                        <div class="producto-sku">${infoExtra}</div>
                    </div>
                `;
            }).join('');
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });

    scanner.addEventListener('keydown', function(e) {
        if ((e.key === 'Enter' || e.key === 'Tab') && !e.repeat) {
            e.preventDefault();
            if (processingScan) return;

            const code = (scanner.value || '').trim();
            if (!code) return;

            processingScan = true;

            const producto = PRODUCTOS.find(p =>
                p.sku === code ||
                p.codigo_barra === code ||
                p.numero_serie === code
            );

            if (producto) {
                agregarProducto(producto);
                scanner.value = '';
                dropdown.style.display = 'none';
                showToast('success', '✅ Producto agregado');
            } else {
                dropdown.innerHTML = '<div style="padding: 1rem; color: #dc3545; text-align: center;">⚠️ Producto no encontrado</div>';
                dropdown.style.display = 'block';
                showToast('error', '❌ Producto no encontrado');
                setTimeout(() => {
                    dropdown.style.display = 'none';
                    scanner.value = '';
                }, 1500);
            }

            processingScan = false;
            scanner.focus();
        }
    });

    dropdown.addEventListener('click', function(e) {
        const item = e.target.closest('.autocomplete-item');
        if (item) {
            const productoId = parseInt(item.dataset.id, 10);
            const producto = PRODUCTOS.find(p => p.id === productoId);
            if (producto) {
                agregarProducto(producto);
                scanner.value = '';
                dropdown.style.display = 'none';
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (!scanner.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function agregarProducto(producto) {
        const existe = productosEnCompra.find(p => p.id === producto.id);
        if (existe) {
            existe.cantidad += 1;
        } else {
            const nuevoProducto = {
                id: producto.id,
                nombre: producto.nombre,
                sku: producto.sku,
                stock_actual: producto.stock_actual,
                tipo_producto: producto.tipo_producto || 'MISC',
                cantidad: 1,
                costo_unitario: producto.costo || 0,
                descuento: 0,
                serie: '' // ✅ string único
            };

            // ✅ Si aplica serie, cargar la serie existente o inicializar vacía
            if (nuevoProducto.tipo_producto === 'UNIDAD') {
                nuevoProducto.serie = producto.serie_actual || '';
                showAlertSerie(nuevoProducto);
            }

            productosEnCompra.push(nuevoProducto);
        }
        renderizarTabla();
    }

    function eliminarFila(index) {
        productosEnCompra.splice(index, 1);
        renderizarTabla();
    }

    function actualizarCantidad(index, valor) {
        const producto = productosEnCompra[index];
        producto.cantidad = parseFloat(valor) || 0;
        renderizarTabla();
    }

    function actualizarCosto(index, valor) {
        productosEnCompra[index].costo_unitario = parseFloat(valor) || 0;
        calcularTotales();
    }

    function actualizarDescuento(index, valor) {
        productosEnCompra[index].descuento = parseFloat(valor) || 0;
        calcularTotales();
    }

    function renderizarTabla() {
        const tbody = document.getElementById('productosBody');

        if (productosEnCompra.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">No hay productos agregados. Use el buscador arriba para agregar productos.</td></tr>';
            calcularTotales();
            return;
        }

        tbody.innerHTML = productosEnCompra.map((p, index) => {
            const subtotal = (p.cantidad * p.costo_unitario) - p.descuento;

            // ✅ Indicador de serie única (no array)
            let serieStatus = '';
            if (p.tipo_producto === 'UNIDAD') {
                const filled = (p.serie && p.serie.trim() !== '');
                const statusColor = filled ? '#10b981' : '#ef4444';
                const statusText = filled ? 'Completa' : 'Pendiente';

                serieStatus = `
                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: ${statusColor}; color: white; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">
                            📋 Serie: ${statusText}
                        </span>
                        <button type="button"
                                onclick="openModalSeries(${index})"
                                style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border: none; padding: 0.25rem 0.75rem; border-radius: 0.375rem; cursor: pointer; font-size: 0.75rem; font-weight: 600; transition: all 0.2s;"
                                onmouseover="this.style.transform='scale(1.05)'"
                                onmouseout="this.style.transform='scale(1)'">
                            ✏️ Editar Serie
                        </button>
                    </div>
                `;
            }

            return `
            <tr>
                <td>
                    <div class="producto-info">
                        <div>
                            <div class="producto-nombre">
                                ${p.nombre}
                                ${p.tipo_producto === 'UNIDAD' ? '<span style="margin-left: 0.5rem; background: #10b981; color: white; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">📦 Serie</span>' : ''}
                            </div>
                            <div class="producto-sku">SKU: ${p.sku}</div>
                            <span class="stock-badge">Stock actual: ${p.stock_actual}</span>
                            ${serieStatus}
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" step="1" min="0" value="${p.cantidad}"
                           onchange="actualizarCantidad(${index}, this.value)"
                           style="text-align: center; font-weight: 600;">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" value="${(parseFloat(p.costo_unitario) || 0).toFixed(2)}"
                           onchange="actualizarCosto(${index}, this.value)"
                           style="text-align: right;">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" value="${(parseFloat(p.descuento) || 0).toFixed(2)}"
                           onchange="actualizarDescuento(${index}, this.value)"
                           style="text-align: right;">
                </td>
                <td style="text-align: right; font-weight: 700; color: #10b981;">
                    Q ${subtotal.toFixed(2)}
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn-remove" onclick="eliminarFila(${index})">✕</button>
                </td>
            </tr>
        `;
        }).join('');

        calcularTotales();
    }

    function calcularTotales() {
        let totalBruto = 0;
        let totalDescuento = 0;

        productosEnCompra.forEach(p => {
            totalBruto += (parseFloat(p.cantidad) || 0) * (parseFloat(p.costo_unitario) || 0);
            totalDescuento += (parseFloat(p.descuento) || 0);
        });

        const totalNeto = totalBruto - totalDescuento;

        document.getElementById('totalBrutoView').textContent = 'Q ' + totalBruto.toFixed(2);
        document.getElementById('totalDescuentoView').textContent = 'Q ' + totalDescuento.toFixed(2);
        document.getElementById('totalNetoView').textContent = 'Q ' + totalNeto.toFixed(2);
    }

    // ✅ Submit robusto: NO deje enviar si proveedor_id vacío
    document.getElementById('formCompra').addEventListener('submit', function(e) {
        const proveedorHidden = document.querySelector('input[name="proveedor_id"]');
        const proveedorText = document.querySelector('.fbselect[data-name="proveedor_id"] .fbselect-input');

        // 1) Validar proveedor
        if (!proveedorHidden || !proveedorHidden.value || parseInt(proveedorHidden.value, 10) <= 0) {
            e.preventDefault();

            // Auto-match si el texto coincide exacto con un item
            const selector = document.querySelector('.fbselect[data-name="proveedor_id"]');
            if (selector && proveedorText) {
                const items = selector.querySelectorAll('.fbselect-item');
                const typed = (proveedorText.value || '').trim().toLowerCase();

                let matched = null;
                items.forEach(it => {
                    if (it.textContent.trim().toLowerCase() === typed) matched = it;
                });

                if (matched) {
                    proveedorHidden.value = matched.dataset.value;
                }
            }

            if (!proveedorHidden.value || parseInt(proveedorHidden.value, 10) <= 0) {
                showToast('error', '⚠️ Seleccione un proveedor válido antes de guardar.');
                proveedorText?.focus();
                return;
            }
        }

        // 2) Validar productos
        const productosValidos = productosEnCompra.filter(p => (parseFloat(p.cantidad) || 0) > 0);
        if (productosValidos.length === 0) {
            e.preventDefault();
            showToast('error', '⚠️ Agregue al menos un producto con cantidad > 0.');
            return;
        }

        // 3) Enviar JSON (serie opcional)
        document.getElementById('productosJson').value = JSON.stringify(productosValidos);
    });

    // Inicializar
    renderizarTabla();

    // Focus scanner
    scanner?.focus();
</script>
