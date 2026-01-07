<?php
$isModal = isset($_GET['modal']) && $_GET['modal'] == '1';

$errors = $errors ?? [];
$old = $old ?? [];
$categorias = $categorias ?? [];

// Obtener marcas
require_once __DIR__ . '/../../marcas/MarcasModel.php';
$marcasModel = new MarcasModel();
$marcasLista = $marcasModel->listarActivas();
?>

<!-- CSS del módulo -->
<link rel="stylesheet" href="<?= url('/assets/css/crear-productos.css') ?>"

    <div class="inventario-crear-container <?= $isModal ? 'bm-embed' : '' ?>">
<div class="inventario-header">
    <div>
        <h1>Nuevo Producto - Inventario</h1>
        <p>Complete el formulario para registrar un nuevo producto en el sistema</p>
    </div>

    <?php if (!$isModal): ?>
        <a href="<?= url('/admin/productos') ?>" class="btn-volver">Volver al Inventario</a>
    <?php else: ?>
        <button type="button" class="btn-volver" onclick="window.parent?.BMModal?.close?.()">Cerrar</button>
    <?php endif; ?>
</div>

<div class="form-container">
    <?php if ($errors): ?>
        <div class="alert-errors">
            <strong>Errores encontrados:</strong>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('/admin/productos/guardar') ?>" enctype="multipart/form-data" id="formProducto">

        <div class="form-section">
            <div class="section-title">Tipo de Producto</div>

            <div class="tipo-producto-grid">
                <div class="tipo-card selected" onclick="selectTipo(this, 'UNIDAD')">
                    <input type="radio" name="tipo_producto" value="UNIDAD" id="tipo_unidad" checked>
                    <div class="tipo-icon">📦</div>
                    <div class="tipo-label">Por Unidad</div>
                    <div class="tipo-description">Con código de barras</div>
                </div>

                <div class="tipo-card" onclick="selectTipo(this, 'PESO')">
                    <input type="radio" name="tipo_producto" value="PESO" id="tipo_peso">
                    <div class="tipo-icon">⚖️</div>
                    <div class="tipo-label">Por Peso</div>
                    <div class="tipo-description">Libras / Kilos</div>
                </div>

                <div class="tipo-card" onclick="selectTipo(this, 'LONGITUD')">
                    <input type="radio" name="tipo_producto" value="LONGITUD" id="tipo_longitud">
                    <div class="tipo-icon">📏</div>
                    <div class="tipo-label">Por Longitud</div>
                    <div class="tipo-description">Metros / Pies</div>
                </div>

                <div class="tipo-card" onclick="selectTipo(this, 'VOLUMEN')">
                    <input type="radio" name="tipo_producto" value="VOLUMEN" id="tipo_volumen">
                    <div class="tipo-icon">🪣</div>
                    <div class="tipo-label">Por Volumen</div>
                    <div class="tipo-description">Litros / Galones</div>
                </div>

                <div class="tipo-card" onclick="selectTipo(this, 'MISC')">
                    <input type="radio" name="tipo_producto" value="MISC" id="tipo_misc">
                    <div class="tipo-icon">🔩</div>
                    <div class="tipo-label">Misceláneo</div>
                    <div class="tipo-description">Clavos, tornillos</div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO TIPO UNIDAD -->
        <div id="form_unidad" class="tipo-form-section">
            <div class="section-title">Producto por Unidad</div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input type="text" name="sku" class="form-input" required
                        placeholder="Ej: MART-16OZ" value="<?= htmlspecialchars($old['sku'] ?? '') ?>">
                    <span class="form-help">Código único del producto</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Código de Barras / QR</label>
                    <input type="text" name="codigo_barra" class="form-input"
                        placeholder="Escanee el código" value="<?= htmlspecialchars($old['codigo_barra'] ?? '') ?>">
                </div>

                <div class="form-group form-grid-full">
                    <label class="form-label">Nombre del Producto <span class="required">*</span></label>
                    <input type="text" name="nombre" class="form-input" required
                        placeholder="Ej: Martillo 16oz Mango de Fibra" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- FORMULARIO TIPO PESO -->
        <div id="form_peso" class="tipo-form-section" style="display: none;">
            <div class="section-title">Producto por Peso</div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input type="text" name="sku" class="form-input" required placeholder="Ej: CLAVOS-2LB" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción del Producto <span class="required">*</span></label>
                    <input type="text" name="nombre" class="form-input" required placeholder="Ej: Clavos de 2 pulgadas" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Peso por Unidad</label>
                    <div class="inline-flex-gap">
                        <input type="number" id="peso_cantidad" class="form-input"
                            step="0.01" placeholder="1.0" oninput="calcularPrecioPeso()">
                        <select id="peso_unidad" class="form-select" onchange="calcularPrecioPeso()">
                            <option value="lb">Libras</option>
                            <option value="kg">Kilos</option>
                        </select>
                    </div>
                    <span class="form-help">Peso que se vende por unidad</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Precio por Libra/Kilo <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <span class="input-icon">Q</span>
                        <input type="number" id="precio_peso_unitario" class="form-input"
                            step="0.01" placeholder="5.00"
                            oninput="calcularPrecioPeso()" onchange="calcularPrecioPeso()">
                    </div>
                    <span class="form-help">Precio que cobra por cada libra/kilo</span>
                </div>
            </div>

            <div class="precio-calculado-box">
                <div class="precio-calculado-label">Precio Total Calculado:</div>
                <div class="precio-calculado-value">
                    Q <span id="precio_peso_total">0.00</span>
                </div>
                <div class="precio-calculado-detalle">
                    (<span id="peso_calculo_detalle">0 lb × Q 0.00</span>)
                </div>
            </div>
        </div>

        <!-- FORMULARIO TIPO LONGITUD -->
        <div id="form_longitud" class="tipo-form-section" style="display: none;">
            <div class="section-title">Producto por Longitud</div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input type="text" name="sku" class="form-input" required placeholder="Ej: CANAL-8IN" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción del Producto <span class="required">*</span></label>
                    <input type="text" name="nombre" class="form-input" required placeholder="Ej: Canales de 8 pulgadas para cielo falso" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Cantidad de Longitud</label>
                    <div class="inline-flex-gap">
                        <input type="number" id="longitud_cantidad" class="form-input"
                            step="0.01" placeholder="12" oninput="calcularPrecioLongitud()">
                        <select id="longitud_unidad" class="form-select" onchange="calcularPrecioLongitud()">
                            <option value="pie">Pies</option>
                            <option value="m">Metros</option>
                        </select>
                    </div>
                    <span class="form-help">Ejemplo: 12 pies</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Precio por Pie/Metro <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <span class="input-icon">Q</span>
                        <input type="number" id="precio_longitud_unitario" class="form-input"
                            step="0.01" placeholder="15.00"
                            oninput="calcularPrecioLongitud()" onchange="calcularPrecioLongitud()">
                    </div>
                    <span class="form-help">Precio que cobra por cada pie/metro</span>
                </div>
            </div>

            <div class="precio-calculado-box">
                <div class="precio-calculado-label">Precio Total Calculado:</div>
                <div class="precio-calculado-value">
                    Q <span id="precio_longitud_total">0.00</span>
                </div>
                <div class="precio-calculado-detalle">
                    (<span id="longitud_calculo_detalle">0 pies × Q 0.00</span>)
                </div>
            </div>
        </div>

        <!-- FORMULARIO TIPO VOLUMEN -->
        <div id="form_volumen" class="tipo-form-section" style="display: none;">
            <div class="section-title">Producto por Volumen</div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input type="text" name="sku" class="form-input" required placeholder="Ej: PINTURA-5GAL" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción del Producto <span class="required">*</span></label>
                    <input type="text" name="nombre" class="form-input" required placeholder="Ej: Pintura latex blanca" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Cantidad de Volumen</label>
                    <div class="inline-flex-gap">
                        <input type="number" id="volumen_cantidad" class="form-input"
                            step="0.01" placeholder="5" oninput="calcularPrecioVolumen()">
                        <select id="volumen_unidad" class="form-select" onchange="calcularPrecioVolumen()">
                            <option value="gal">Galones</option>
                            <option value="L">Litros</option>
                        </select>
                    </div>
                    <span class="form-help">Ejemplo: 5 galones</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Precio por Galón/Litro <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <span class="input-icon">Q</span>
                        <input type="number" id="precio_volumen_unitario" class="form-input"
                            step="0.01" placeholder="45.00"
                            oninput="calcularPrecioVolumen()" onchange="calcularPrecioVolumen()">
                    </div>
                    <span class="form-help">Precio que cobra por cada galón/litro</span>
                </div>
            </div>

            <div class="precio-calculado-box">
                <div class="precio-calculado-label">Precio Total Calculado:</div>
                <div class="precio-calculado-value">
                    Q <span id="precio_volumen_total">0.00</span>
                </div>
                <div class="precio-calculado-detalle">
                    (<span id="volumen_calculo_detalle">0 gal × Q 0.00</span>)
                </div>
            </div>
        </div>

        <!-- FORMULARIO TIPO MISC -->
        <div id="form_misc" class="tipo-form-section" style="display: none;">
            <div class="section-title">Producto Misceláneo</div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input type="text" name="sku" class="form-input" required placeholder="Ej: TORN-1/2" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre del Producto <span class="required">*</span></label>
                    <input type="text" name="nombre" class="form-input" required placeholder="Ej: Tornillos 1/2 pulgada (caja 100 unidades)" disabled>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        <div class="section-title">Clasificación del Producto</div>

        <div class="form-grid">
            <div class="categoria-selector">
                <div class="form-group">
                    <label class="form-label">Categoría <span class="required">*</span></label>
                    <div class="fbselect" data-name="categoria_id" data-required="true">
                        <input type="text"
                            placeholder="Buscar categoría..."
                            class="fbselect-input"
                            autocomplete="off">
                        <input type="hidden" name="categoria_id" value="<?= $old['categoria_id'] ?? '' ?>">
                        <div class="fbselect-menu">
                            <?php foreach ($categorias as $cat): ?>
                                <div class="fbselect-item"
                                    data-value="<?= (int)$cat['id'] ?>"
                                    data-search="<?= strtolower($cat['nombre']) ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <span class="form-help">Tipo de trabajo: Plomería, Construcción, Electricidad, etc.</span>
                </div>
                <button type="button" class="btn-add-new" onclick="openModal('modalCategoria')">Nueva Categoría</button>
            </div>

            <div class="categoria-selector">
                <div class="form-group">
                    <label class="form-label">Marca</label>
                    <div class="fbselect" data-name="marca_id" data-required="false">
                        <input type="text"
                            placeholder="Buscar marca..."
                            class="fbselect-input"
                            autocomplete="off">
                        <input type="hidden" name="marca_id" value="<?= $old['marca_id'] ?? '' ?>">
                        <div class="fbselect-menu">
                            <div class="fbselect-item" data-value="" data-search="">Sin marca</div>
                            <?php foreach ($marcasLista as $marca): ?>
                                <div class="fbselect-item"
                                    data-value="<?= (int)$marca['id'] ?>"
                                    data-search="<?= strtolower($marca['nombre']) ?>">
                                    <?= htmlspecialchars($marca['nombre']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <span class="form-help">Fabricante o marca del producto (opcional)</span>
                </div>
                <button type="button" class="btn-add-new" onclick="openModal('modalMarca')">Nueva Marca</button>
            </div>
        </div>

        <hr class="section-divider">

        <div class="section-title">Precios e Inventario</div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Costo de Compra <span class="required">*</span></label>
                <div class="input-with-icon">
                    <span class="input-icon">Q</span>
                    <input type="number" name="costo" id="costo_compra" class="form-input" required
                        min="0" step="1" placeholder="0"
                        value="<?= htmlspecialchars($old['costo'] ?? '0') ?>"
                        onchange="calcularPrecioVenta()">
                </div>
                <span class="form-help">Precio al que compra</span>
            </div>

            <div class="form-group">
                <label class="form-label">Precio de Venta <span class="required">*</span></label>
                <div class="input-with-icon">
                    <span class="input-icon">Q</span>
                    <input type="number" name="precio" id="precio_venta" class="form-input" required
                        min="0" step="1" placeholder="0"
                        value="<?= htmlspecialchars($old['precio'] ?? '0') ?>">
                </div>
                <span class="form-help" id="margen_info">Precio al que vende</span>
            </div>

            <div class="form-group">
                <label class="form-label">Stock Mínimo <span class="required">*</span></label>
                <input type="number" name="stock_minimo" class="form-input" required
                    min="0" step="1" placeholder="5"
                    value="<?= htmlspecialchars($old['stock_minimo'] ?? '5') ?>">
                <span class="form-help">Alerta cuando el stock baje de este número</span>
            </div>

            <div class="form-group">
                <label class="form-label">Nota sobre Stock</label>
                <div class="stock-note">
                    <p>
                        ℹ️ El stock inicial será <strong>0</strong>. Para ingresar productos al inventario,
                        utilice el módulo de <strong>Compras</strong>.
                    </p>
                </div>
            </div>

            <div class="form-group form-grid-full">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-textarea" rows="3"
                    placeholder="Descripción detallada..."><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="form-group form-grid-full">
                <label class="form-label">Imagen del Producto</label>
                <input type="file" name="imagen" class="form-input" accept="image/jpeg,image/png,image/webp">
                <span class="form-help">JPG, PNG o WEBP - Máximo 2MB</span>
            </div>
        </div>

        <div class="form-actions">
            <?php if (!$isModal): ?>
                <a href="<?= url('/admin/productos') ?>" class="btn-secondary">Cancelar</a>
            <?php else: ?>
                <button type="button" class="btn-secondary" onclick="window.parent?.BMModal?.close?.()">Cancelar</button>
            <?php endif; ?>

            <button type="submit" class="btn-primary">Guardar Producto</button>
        </div>
    </form>
</div>
</div>

<!-- Modales internos (categoría / marca) -->
<div id="modalCategoria" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Nueva Categoría</h2>
            <button type="button" class="modal-close" onclick="closeModal('modalCategoria')">&times;</button>
        </div>
        <form onsubmit="guardarCategoria(event)">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" id="nueva_categoria" class="form-input" required placeholder="Ej: Herramientas Eléctricas">
            </div>
            <div class="form-actions modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('modalCategoria')">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalMarca" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Nueva Marca</h2>
            <button type="button" class="modal-close" onclick="closeModal('modalMarca')">&times;</button>
        </div>
        <form onsubmit="guardarMarca(event)">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" id="nueva_marca" class="form-input" required placeholder="Ej: DeWalt, Bosch, Stanley">
            </div>
            <div class="form-actions modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('modalMarca')">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript del módulo -->
<script src="<?= url('/assets/js/facebook-selectors.js') ?>"></script>
<script>
    let currentTipo = 'UNIDAD';

    function selectTipo(card, tipo) {
        document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        currentTipo = tipo;

        // Ocultar y deshabilitar campos de todos los formularios específicos
        document.querySelectorAll('.tipo-form-section').forEach(section => {
            section.style.display = 'none';
            section.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = true;
            });
        });

        // Mostrar y habilitar el formulario correspondiente
        const formMap = {
            'UNIDAD': 'form_unidad',
            'PESO': 'form_peso',
            'LONGITUD': 'form_longitud',
            'VOLUMEN': 'form_volumen',
            'MISC': 'form_misc'
        };

        const formId = formMap[tipo];
        if (formId) {
            const selectedForm = document.getElementById(formId);
            selectedForm.style.display = 'block';
            selectedForm.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = false;
            });
        }
    }

    function calcularPrecioPeso() {
        const cantidad = parseFloat(document.getElementById('peso_cantidad')?.value || 0);
        const unidad = document.getElementById('peso_unidad')?.value || 'lb';
        const precioUnitario = parseFloat(document.getElementById('precio_peso_unitario')?.value || 0);
        const precioTotal = cantidad * precioUnitario;

        document.getElementById('precio_peso_total').textContent = precioTotal.toFixed(2);
        document.getElementById('peso_calculo_detalle').textContent = `${cantidad} ${unidad} × Q ${precioUnitario.toFixed(2)}`;

        const precioVentaInput = document.getElementById('precio_venta');
        if (precioVentaInput) precioVentaInput.value = precioTotal.toFixed(2);
    }

    function calcularPrecioLongitud() {
        const cantidad = parseFloat(document.getElementById('longitud_cantidad')?.value || 0);
        const unidad = document.getElementById('longitud_unidad')?.value || 'pie';
        const precioUnitario = parseFloat(document.getElementById('precio_longitud_unitario')?.value || 0);
        const precioTotal = cantidad * precioUnitario;

        document.getElementById('precio_longitud_total').textContent = precioTotal.toFixed(2);
        document.getElementById('longitud_calculo_detalle').textContent =
            `${cantidad} ${unidad === 'pie' ? 'pies' : 'metros'} × Q ${precioUnitario.toFixed(2)}`;

        const precioVentaInput = document.getElementById('precio_venta');
        if (precioVentaInput) precioVentaInput.value = precioTotal.toFixed(2);
    }

    function calcularPrecioVolumen() {
        const cantidad = parseFloat(document.getElementById('volumen_cantidad')?.value || 0);
        const unidad = document.getElementById('volumen_unidad')?.value || 'gal';
        const precioUnitario = parseFloat(document.getElementById('precio_volumen_unitario')?.value || 0);
        const precioTotal = cantidad * precioUnitario;

        document.getElementById('precio_volumen_total').textContent = precioTotal.toFixed(2);
        document.getElementById('volumen_calculo_detalle').textContent =
            `${cantidad} ${unidad === 'gal' ? 'galones' : 'litros'} × Q ${precioUnitario.toFixed(2)}`;

        const precioVentaInput = document.getElementById('precio_venta');
        if (precioVentaInput) precioVentaInput.value = precioTotal.toFixed(2);
    }

    function calcularPrecioVenta() {
        const costo = parseFloat(document.getElementById('costo_compra').value) || 0;
        const precioVentaInput = document.getElementById('precio_venta');
        const margenInfo = document.getElementById('margen_info');

        // Si no hay subcategoría en este formulario, no forzamos cálculo por margen
        if (!precioVentaInput || !margenInfo) return;

        margenInfo.textContent = 'Precio al que vende';
        margenInfo.style.color = '#64748b';
    }

    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });
    });

    async function guardarCategoria(e) {
        e.preventDefault();
        const nombre = document.getElementById('nueva_categoria').value.trim();
        if (!nombre) return alert('⚠️ Ingrese un nombre');

        try {
            // Usar la ruta del router principal
            const baseUrl = window.location.origin;
            const url = baseUrl + '/admin/catalogos/categorias/guardar';

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nombre
                })
            });

            const text = await response.text();

            // Validar respuesta antes de parsear JSON para categorías
            if (!response.ok) {
                if (response.status === 409) {
                    alert('❌ Ya existe una categoría con ese nombre');
                    return;
                }
                if (response.status === 422) {
                    alert('❌ El nombre de la categoría es requerido');
                    return;
                }
                alert(`❌ Error del servidor: ${response.status} - ${response.statusText}`);
                return;
            }

            const data = JSON.parse(text);

            if (data.success) {
                // Buscar el componente fbselect de categorías
                const categoriaSelector = document.querySelector('.fbselect[data-name="categoria_id"]');
                if (categoriaSelector) {
                    const menu = categoriaSelector.querySelector('.fbselect-menu');
                    const hiddenInput = categoriaSelector.querySelector('input[type="hidden"]');
                    const textInput = categoriaSelector.querySelector('.fbselect-input');

                    // Crear nuevo item
                    const newItem = document.createElement('div');
                    newItem.className = 'fbselect-item';
                    newItem.setAttribute('data-value', data.categoria.id);
                    newItem.setAttribute('data-search', data.categoria.nombre.toLowerCase());
                    newItem.textContent = data.categoria.nombre;

                    // Agregar evento click
                    newItem.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        textInput.value = data.categoria.nombre;
                        hiddenInput.value = data.categoria.id;
                        categoriaSelector.classList.remove('fbselect-open');
                        menu.style.display = 'none';
                        hiddenInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    });

                    // Agregar al menú
                    menu.appendChild(newItem);

                    // Seleccionar la nueva categoría
                    textInput.value = data.categoria.nombre;
                    hiddenInput.value = data.categoria.id;
                    hiddenInput.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }

                document.getElementById('nueva_categoria').value = '';
                closeModal('modalCategoria');
            } else {
                alert('❌ Error: ' + (data.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error(error);
            alert('❌ Error de conexión. Revise consola.');
        }
    }

    async function guardarMarca(e) {
        e.preventDefault();
        const nombre = document.getElementById('nueva_marca').value.trim();
        if (!nombre) return alert('⚠️ Ingrese un nombre');

        try {
            // Usar la ruta del router principal
            const baseUrl = window.location.origin;
            const url = baseUrl + '/admin/catalogos/marcas/guardar';

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nombre
                })
            });

            const text = await response.text();

            // Validar respuesta antes de parsear JSON para marcas
            if (!response.ok) {
                if (response.status === 409) {
                    alert('❌ Ya existe una marca con ese nombre');
                    return;
                }
                if (response.status === 422) {
                    alert('❌ El nombre de la marca es requerido');
                    return;
                }
                alert(`❌ Error del servidor: ${response.status} - ${response.statusText}`);
                return;
            }

            const data = JSON.parse(text);

            if (data.success) {
                // Buscar el componente fbselect de marcas
                const marcaSelector = document.querySelector('.fbselect[data-name="marca_id"]');
                if (marcaSelector) {
                    const menu = marcaSelector.querySelector('.fbselect-menu');
                    const hiddenInput = marcaSelector.querySelector('input[type="hidden"]');
                    const textInput = marcaSelector.querySelector('.fbselect-input');

                    // Crear nuevo item
                    const newItem = document.createElement('div');
                    newItem.className = 'fbselect-item';
                    newItem.setAttribute('data-value', data.marca.id);
                    newItem.setAttribute('data-search', data.marca.nombre.toLowerCase());
                    newItem.textContent = data.marca.nombre;

                    // Agregar evento click
                    newItem.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        textInput.value = data.marca.nombre;
                        hiddenInput.value = data.marca.id;
                        marcaSelector.classList.remove('fbselect-open');
                        menu.style.display = 'none';
                        hiddenInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    });

                    // Agregar al menú
                    menu.appendChild(newItem);

                    // Seleccionar la nueva marca
                    textInput.value = data.marca.nombre;
                    hiddenInput.value = data.marca.id;
                    hiddenInput.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }

                document.getElementById('nueva_marca').value = '';
                closeModal('modalMarca');
            } else {
                alert('❌ Error: ' + (data.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error(error);
            alert('❌ Error de conexión. Revise consola.');
        }
    }

    // Validación precio < costo
    document.getElementById('formProducto').addEventListener('submit', function(e) {
        const precioVenta = parseFloat(document.getElementById('precio_venta').value) || 0;
        const costo = parseFloat(document.getElementById('costo_compra').value) || 0;

        if (precioVenta < costo) {
            if (!confirm('⚠️ El precio de venta es menor al costo. ¿Continuar?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        const card = document.querySelector('.tipo-card.selected');
        if (card) selectTipo(card, 'UNIDAD');
    });
</script>
