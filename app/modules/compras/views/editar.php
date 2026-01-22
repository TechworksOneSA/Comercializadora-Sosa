<?php
$errors = $errors ?? [];
$old = $old ?? [];
$productos = $productos ?? [];
$proveedores = $proveedores ?? [];
$compra = $compra ?? null;
$detalles = $detalles ?? [];

// Para JS: lista de productos disponibles
$productosJs = [];
foreach ($productos as $p) {
    $productosJs[] = [
        'id'    => (int)$p['id'],
        'nombre' => $p['nombre'],
        'sku'   => $p['sku'] ?? '',
        'codigo_barra' => $p['codigo_barra'] ?? '',
        'costo' => isset($p['costo_actual']) ? (float)$p['costo_actual'] : 0,
        'stock_actual' => (int)($p['stock'] ?? 0),
    ];
}

// Productos ya en la compra
$productosEnCompraInicial = [];
foreach ($detalles as $det) {
    $productosEnCompraInicial[] = [
        'id' => (int)$det['producto_id'],
        'nombre' => $det['producto_nombre'] ?? '',
        'sku' => $det['producto_sku'] ?? '',
        'stock_actual' => (int)($det['stock_actual'] ?? 0),
        'cantidad' => (float)($det['cantidad'] ?? 0),
        'costo_unitario' => (float)($det['costo_unitario'] ?? 0),
        'descuento' => (float)($det['descuento'] ?? 0),
    ];
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

    .alert-warning {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        color: #92400e;
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

    .scanner-input {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .scanner-input input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #0a3d91;
        border-radius: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
    }

    .scanner-input::before {
        content: '🔍';
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.25rem;
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

    .productos-table thead th {
        color: white !important;
    }

    .productos-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .productos-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .productos-table tbody tr:hover {
        background: #f8fafc;
    }

    .productos-table input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        font-size: 0.9rem;
    }

    .productos-table input:focus {
        outline: none;
        border-color: #0a3d91;
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

    .btn-add-row {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 1.5rem;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-add-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
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
        align-items: center;
        gap: 0.5rem;
    }

    .producto-nombre {
        font-weight: 600;
        color: #1e293b;
    }

    .producto-sku {
        font-size: 0.85rem;
        color: #64748b;
        font-family: 'Courier New', monospace;
    }

    .stock-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
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

    .fbselect-menu::-webkit-scrollbar {
        width: 6px;
    }

    .fbselect-menu::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .fbselect-menu::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 3px;
        transition: background .3s ease;
    }

    .fbselect-menu::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>

<div class="compras-container">
    <div class="compras-header">
        <div>
            <h1>✏️ Editar Compra #<?= htmlspecialchars($compra['id'] ?? '') ?></h1>
            <p>Modifique los datos de la compra y los productos incluidos</p>
        </div>
        <a href="<?= url('/admin/compras') ?>" class="btn-volver">← Volver</a>
    </div>

    <div class="form-container">
        <?php if ($errors): ?>
            <div class="alert-errors">
                <strong>⚠️ Errores encontrados:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!$compra): ?>
            <div class="alert-errors">
                <strong>⚠️ Error:</strong> No se encontró la compra solicitada.
            </div>
        <?php else: ?>
            <div class="alert-warning">
                <strong>⚠️ Nota importante:</strong> Al modificar esta compra, el inventario se ajustará automáticamente.
                Los productos que se eliminen reducirán el stock, y los nuevos lo aumentarán.
            </div>

            <form method="POST" action="<?= url('/admin/compras/actualizar/' . $compra['id']) ?>" id="formCompra">
                <!-- Datos del encabezado -->
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Proveedor <span class="required">*</span></label>
                        <div class="fbselect" data-name="proveedor_id" data-required="true">
                            <input type="text"
                                placeholder="Buscar proveedor..."
                                class="fbselect-input"
                                autocomplete="off">
                            <input type="hidden" name="proveedor_id" value="<?= $compra['proveedor_id'] ?>">
                            <div class="fbselect-menu">
                                <?php foreach ($proveedores as $prov): ?>
                                    <div class="fbselect-item"
                                        data-value="<?= $prov['id'] ?>"
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
                            value="<?= htmlspecialchars($compra['fecha_compra'] ?? date('Y-m-d')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número de Documento</label>
                        <input type="text" name="numero_doc" class="form-input"
                            placeholder="Factura, recibo, etc."
                            value="<?= htmlspecialchars($compra['numero_doc'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notas</label>
                        <input type="text" name="notas" class="form-input"
                            placeholder="Observaciones adicionales"
                            value="<?= htmlspecialchars($compra['notas'] ?? '') ?>">
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
                    <button type="submit" class="btn-primary">💾 Actualizar Compra</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript del módulo -->
<script src="<?= url('/assets/js/facebook-selectors.js') ?>"></script>
<script>
    const PRODUCTOS = <?= json_encode($productosJs) ?>;
    let productosEnCompra = <?= json_encode($productosEnCompraInicial) ?>;

    // Función para buscar productos
    function buscarProducto(query) {
        query = query.toLowerCase().trim();
        if (!query) return [];

        return PRODUCTOS.filter(p =>
            p.nombre.toLowerCase().includes(query) ||
            p.sku.toLowerCase().includes(query) ||
            p.codigo_barra.toLowerCase().includes(query)
        ).slice(0, 10);
    }

    // ====== FUNCIONES PARA BÚSQUEDA MANUAL ======
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
            html += `
                <div class="autocomplete-item" onclick="seleccionarProductoManual(${producto.id})" style="cursor: pointer;">
                    <div class="producto-nombre">${producto.nombre}</div>
                    <div class="producto-sku">SKU: ${producto.sku} | Stock: ${producto.stock_actual}</div>
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

        if (buscarProductoInput.value) {
            buscarProductos();
        } else {
            resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
            resultadosProductosDiv.style.display = 'block';
        }
    }

    function ocultarResultadosProductos() {
        const resultadosProductosDiv = document.getElementById('resultadosProductos');
        if (resultadosProductosDiv) resultadosProductosDiv.style.display = 'none';
    }
    // ====== FIN FUNCIONES BÚSQUEDA MANUAL ======

    // Autocompletado
    const scanner = document.getElementById('productoScanner');
    const dropdown = document.getElementById('autocompleteDropdown');

    scanner.addEventListener('input', function() {
        const resultados = buscarProducto(this.value);

        if (resultados.length > 0) {
            dropdown.innerHTML = resultados.map(p => `
            <div class="autocomplete-item" data-id="${p.id}">
                <div class="producto-nombre">${p.nombre}</div>
                <div class="producto-sku">SKU: ${p.sku} | Stock: ${p.stock_actual}</div>
            </div>
        `).join('');
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });

    dropdown.addEventListener('click', function(e) {
        const item = e.target.closest('.autocomplete-item');
        if (item) {
            const productoId = parseInt(item.dataset.id);
            const producto = PRODUCTOS.find(p => p.id === productoId);
            if (producto) {
                agregarProducto(producto);
                scanner.value = '';
                dropdown.style.display = 'none';
            }
        }
    });

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!scanner.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function agregarProducto(producto) {
        // Verificar si ya existe
        const existe = productosEnCompra.find(p => p.id === producto.id);
        if (existe) {
            existe.cantidad += 1;
        } else {
            productosEnCompra.push({
                id: producto.id,
                nombre: producto.nombre,
                sku: producto.sku,
                stock_actual: producto.stock_actual,
                cantidad: 1,
                costo_unitario: producto.costo || 0,
                descuento: 0
            });
        }
        renderizarTabla();
    }

    function eliminarFila(index) {
        productosEnCompra.splice(index, 1);
        renderizarTabla();
    }

    function actualizarCantidad(index, valor) {
        productosEnCompra[index].cantidad = parseFloat(valor) || 0;
        calcularTotales();
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

            return `
            <tr>
                <td>
                    <div class="producto-info">
                        <div>
                            <div class="producto-nombre">${p.nombre}</div>
                            <div class="producto-sku">SKU: ${p.sku}</div>
                            <span class="stock-badge">Stock actual: ${p.stock_actual}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" step="1" min="0" value="${p.cantidad}"
                           onchange="actualizarCantidad(${index}, this.value)"
                           style="text-align: center; font-weight: 600;">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" value="${p.costo_unitario.toFixed(2)}"
                           onchange="actualizarCosto(${index}, this.value)"
                           style="text-align: right;">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" value="${p.descuento.toFixed(2)}"
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
            totalBruto += p.cantidad * p.costo_unitario;
            totalDescuento += p.descuento;
        });

        const totalNeto = totalBruto - totalDescuento;

        document.getElementById('totalBrutoView').textContent = 'Q ' + totalBruto.toFixed(2);
        document.getElementById('totalDescuentoView').textContent = 'Q ' + totalDescuento.toFixed(2);
        document.getElementById('totalNetoView').textContent = 'Q ' + totalNeto.toFixed(2);
    }

    // Submit del formulario
    document.getElementById('formCompra').addEventListener('submit', function(e) {
        // Filtrar solo productos válidos
        const productosValidos = productosEnCompra.filter(p => p.cantidad > 0);

        if (productosValidos.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto con cantidad mayor a 0');
            return;
        }

        // Convertir a JSON para enviar
        document.getElementById('productosJson').value = JSON.stringify(productosValidos);
    });

    // Inicializar
    renderizarTabla();
    scanner.focus();

    // Inicializar valor del proveedor
    document.addEventListener('DOMContentLoaded', function() {
        // Buscar el proveedor seleccionado y establecer el texto del input
        const proveedorId = '<?= $compra['proveedor_id'] ?>';
        if (proveedorId) {
            const proveedorItem = document.querySelector(`.fbselect-item[data-value="${proveedorId}"]`);
            if (proveedorItem) {
                const proveedorInput = document.querySelector('.fbselect[data-name="proveedor_id"] .fbselect-input');
                if (proveedorInput) {
                    proveedorInput.value = proveedorItem.textContent.trim();
                }
            }
        }
    });
</script>
