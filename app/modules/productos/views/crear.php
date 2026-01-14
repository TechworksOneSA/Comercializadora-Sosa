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
<link rel="stylesheet" href="<?= url('/assets/css/crear-productos.css') ?>">

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
                        <div class="tipo-label">Aplica Serie</div>
                        <div class="tipo-description">Productos con inventario controlado</div>
                    </div>

                    <div class="tipo-card" onclick="selectTipo(this, 'MISC')">
                        <input type="radio" name="tipo_producto" value="MISC" id="tipo_misc">
                        <div class="tipo-icon">🔩</div>
                        <div class="tipo-label">Misceláneo</div>
                        <div class="tipo-description">Productos sin código de barras</div>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO TIPO UNIDAD (APLICA SERIE) -->
            <div id="form_unidad" class="tipo-form-section">
                <div class="section-title">Producto con Serie</div>

                <div class="form-grid">
                    <div class="form-group form-grid-full">
                        <label class="form-label">Nombre del Producto <span class="required">*</span></label>
                        <input type="text" name="nombre" id="nombre_unidad" class="form-input" required
                            placeholder="Ej: Martillo 16oz Mango de Fibra" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" autofocus>
                        <span class="form-help">El SKU se generará automáticamente</span>
                    </div>

                    <div class="form-group form-grid-full">
                        <label class="form-label">Número de Serie</label>
                        <input type="text" name="numero_serie" id="numero_serie_unidad" class="form-input"
                            placeholder="Ej: SN123456789" value="<?= htmlspecialchars($old['numero_serie'] ?? '') ?>">
                        <span class="form-help">📝 Ingresa el número de serie del producto (opcional). Si no lo ingresas ahora, podrás hacerlo después.</span>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO TIPO MISC -->
            <div id="form_misc" class="tipo-form-section" style="display: none;">
                <div class="section-title">Producto Misceláneo</div>

                <div class="form-grid">
                    <div class="form-group form-grid-full">
                        <label class="form-label">Nombre del Producto <span class="required">*</span></label>
                        <input type="text" name="nombre" id="nombre_misc" class="form-input" required
                            placeholder="Ej: Tornillos 1/2 pulgada (caja 100 unidades)" disabled>
                        <span class="form-help">El SKU se generará automáticamente (no requiere código de barras)</span>
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
                            min="0" step="any" placeholder="0.00"
                            value="<?= htmlspecialchars($old['costo'] ?? '0') ?>"
                            onchange="calcularPrecioVenta()">
                    </div>
                    <span class="form-help">Precio al que compra (puede usar decimales como 15.50)</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Precio de Venta <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <span class="input-icon">Q</span>
                        <input type="number" name="precio" id="precio_venta" class="form-input" required
                            min="0" step="any" placeholder="0.00"
                            value="<?= htmlspecialchars($old['precio'] ?? '0') ?>">
                    </div>
                    <span class="form-help" id="margen_info">Precio al que vende (puede usar decimales como 25.99)</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Stock Mínimo <span class="required">*</span></label>
                    <input type="number" name="stock_minimo" class="form-input" required
                        min="0" step="any" placeholder="5"
                        value="<?= htmlspecialchars($old['stock_minimo'] ?? '5') ?>">
                    <span class="form-help">Alerta cuando el stock baje de este número (puede usar decimales como 3.5)</span>
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
                input.removeAttribute('required');
            });
        });

        // Mostrar y habilitar el formulario correspondiente
        const formMap = {
            'UNIDAD': 'form_unidad',
            'MISC': 'form_misc'
        };

        const formId = formMap[tipo];
        if (formId) {
            const selectedForm = document.getElementById(formId);
            if (selectedForm) {
                selectedForm.style.display = 'block';
                selectedForm.querySelectorAll('input, select, textarea').forEach(input => {
                    input.disabled = false;
                    // El nombre siempre es required
                    if (input.name === 'nombre') {
                        input.setAttribute('required', 'required');
                    }
                });
            }
        }

        // Actualizar el radio button correspondiente
        const radioButton = document.getElementById('tipo_' + tipo.toLowerCase());
        if (radioButton) {
            radioButton.checked = true;
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
            // Usar la función url() de PHP para generar la URL correcta
            const url = '<?= url('/admin/catalogos/categorias/guardar') ?>';

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
            // Usar la función url() de PHP para generar la URL correcta
            const url = '<?= url('/admin/catalogos/marcas/guardar') ?>';

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
        // Debug: verificar qué campos se están enviando
        const formData = new FormData(this);
        console.log('=== DATOS DEL FORMULARIO ===');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        console.log('numero_serie en FormData:', formData.get('numero_serie'));
        
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
