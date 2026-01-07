<?php
$isModal = isset($_GET['modal']) && $_GET['modal'] == '1';
$errors     = $errors ?? [];
$producto   = $producto ?? [];
$categorias = $categorias ?? [];
$marcas     = $marcas ?? [];

// Mapear ID → nombre
$mapCat = [];
foreach ($categorias as $c) {
    $mapCat[(int)$c['id']] = $c['nombre'];
}
$mapMar = [];
foreach ($marcas as $m) {
    $mapMar[(int)$m['id']] = $m['nombre'];
}

$catId   = (int)($producto['categoria_id'] ?? 0);
$marcaId = (int)($producto['marca_id'] ?? 0);

$catText   = $catId && isset($mapCat[$catId])   ? $mapCat[$catId]   : '';
$marcaText = $marcaId && isset($mapMar[$marcaId]) ? $mapMar[$marcaId] : '';
?>

<style>
/* ========================================
   INVENTARIO - EDITAR PRODUCTO (MODERN PRO)
======================================== */

.inventario-editar-container {
    min-height: auto;
    padding: 0;
}

.inventario-editar-container.bm-embed {
    background: transparent;
    padding: 0;
    min-height: auto;
    max-height: 85vh;
    overflow-y: auto;
}

.inventario-editar-container.bm-embed .inventario-header {
    display: none;
}

.inventario-header {
    background: linear-gradient(135deg, #0a2463 0%, #0f3a7a 35%, #1565c0 70%, #0284c7 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 24px;
    box-shadow:
        0 25px 60px rgba(10, 61, 145, .40),
        0 12px 25px rgba(10, 61, 145, .25),
        inset 0 1px 0 rgba(255, 255, 255, .08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
    border: 1.5px solid rgba(255, 255, 255, .12);
}

.inventario-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 85% 15%, rgba(255, 255, 255, .12) 0%, transparent 50%),
        radial-gradient(circle at 15% 85%, rgba(255, 255, 255, .08) 0%, transparent 50%);
}

.inventario-header h1 {
    font-size: 2rem;
    font-weight: 950;
    color: #ffffff;
    margin: 0 0 8px 0;
    letter-spacing: -0.8px;
    text-shadow:
        0 4px 15px rgba(0, 0, 0, .30),
        0 2px 5px rgba(0, 0, 0, .20);
    position: relative;
    z-index: 1;
}

.inventario-header p {
    color: rgba(165, 243, 252, 0.96);
    margin: 0;
    font-size: 1.05rem;
    font-weight: 500;
    letter-spacing: 0.3px;
    text-shadow: 0 2px 6px rgba(0, 0, 0, .25);
    position: relative;
    z-index: 1;
}

.btn-volver {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    color: #0a2463;
    padding: 14px 24px;
    border-radius: 14px;
    font-weight: 900;
    font-size: 1rem;
    text-decoration: none;
    border: 2px solid rgba(255, 255, 255, .4);
    cursor: pointer;
    transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:
        0 12px 25px rgba(255, 255, 255, .20),
        0 12px 35px rgba(2, 8, 23, .25),
        inset 0 1px 0 rgba(255, 255, 255, .8);
    position: relative;
    z-index: 1;
    letter-spacing: 0.3px;
}

.btn-volver:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow:
        0 18px 35px rgba(255, 255, 255, .25),
        0 18px 45px rgba(2, 8, 23, .30),
        0 0 40px rgba(255, 255, 255, .15);
}

.form-container {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow:
        0 20px 50px rgba(10, 61, 145, .15),
        0 8px 20px rgba(10, 61, 145, .08);
    max-width: 1400px;
    margin: 0 auto;
    border: 1px solid rgba(10, 61, 145, .08);
}

.alert-errors {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-left: 5px solid #ef4444;
    padding: 18px 20px;
    border-radius: 14px;
    margin-bottom: 24px;
    box-shadow: 0 4px 12px rgba(239, 68, 68, .15);
}

.alert-errors strong {
    color: #991b1b;
    font-weight: 800;
    display: block;
    margin-bottom: 8px;
    font-size: 1.05rem;
}

.alert-errors ul {
    margin: 8px 0 0 0;
    padding-left: 24px;
    color: #b91c1c;
    font-weight: 500;
}

.alert-errors li {
    margin: 4px 0;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0a2463;
    margin: 0 0 20px 0;
    letter-spacing: -0.5px;
    padding-bottom: 12px;
    border-bottom: 3px solid #e0f2fe;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #1565c0, #0284c7);
    border-radius: 3px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

.form-grid-full {
    grid-column: 1 / -1;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
    letter-spacing: -0.2px;
}

.form-label .required {
    color: #ef4444;
    font-weight: 900;
    margin-left: 2px;
}

.form-input,
.form-select {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 500;
    color: #1e293b;
    transition: all .3s ease;
    background: white;
    font-family: inherit;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: #1565c0;
    box-shadow:
        0 0 0 4px rgba(21, 101, 192, .12),
        0 4px 12px rgba(21, 101, 192, .08);
    transform: translateY(-1px);
}

.form-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
}

.lookup-wrapper {
    position: relative;
}

.lookup-input {
    width: 100%;
}

.lookup-results {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #1565c0;
    border-top: none;
    border-radius: 0 0 12px 12px;
    max-height: 240px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 12px 30px rgba(21, 101, 192, .20);
    margin-top: -2px;
}

.lookup-item {
    padding: 12px 16px;
    cursor: pointer;
    transition: all .2s ease;
    font-size: 0.95rem;
    font-weight: 500;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
}

.lookup-item:last-child {
    border-bottom: none;
}

.lookup-item:hover {
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    color: #0a2463;
    font-weight: 700;
    padding-left: 20px;
}

.tipo-producto-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-top: 16px;
}

.tipo-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.tipo-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(21, 101, 192, .03), rgba(2, 132, 199, .03));
    opacity: 0;
    transition: opacity .3s ease;
}

.tipo-card:hover {
    transform: translateY(-4px);
    border-color: #1565c0;
    box-shadow:
        0 12px 30px rgba(21, 101, 192, .20),
        0 5px 15px rgba(21, 101, 192, .10);
}

.tipo-card:hover::before {
    opacity: 1;
}

.tipo-card.selected {
    background: linear-gradient(135deg, #1565c0 0%, #0284c7 100%);
    border-color: #0f3a7a;
    transform: translateY(-4px) scale(1.02);
    box-shadow:
        0 15px 40px rgba(21, 101, 192, .35),
        0 8px 20px rgba(21, 101, 192, .20),
        inset 0 1px 0 rgba(255, 255, 255, .2);
}

.tipo-card.selected .tipo-icon {
    transform: scale(1.2);
}

.tipo-card.selected .tipo-label,
.tipo-card.selected .tipo-description {
    color: white;
}

.tipo-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.tipo-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
    display: block;
    transition: transform .3s ease;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, .1));
}

.tipo-label {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
    transition: color .3s ease;
}

.tipo-description {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
    transition: color .3s ease;
}

.form-actions {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid #f1f5f9;
}

.btn-primary {
    background: linear-gradient(135deg, #1565c0 0%, #0284c7 100%);
    color: white;
    border: none;
    padding: 16px 32px;
    border-radius: 14px;
    font-weight: 900;
    font-size: 1.05rem;
    cursor: pointer;
    transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:
        0 12px 28px rgba(21, 101, 192, .35),
        inset 0 1px 0 rgba(255, 255, 255, .2);
    letter-spacing: 0.3px;
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, .2) 50%, transparent 100%);
    transform: translateX(-100%);
    transition: transform .6s ease;
}

.btn-primary:hover::before {
    transform: translateX(100%);
}

.btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow:
        0 18px 40px rgba(21, 101, 192, .45),
        inset 0 1px 0 rgba(255, 255, 255, .3),
        0 0 40px rgba(21, 101, 192, .2);
}

.btn-primary:active {
    transform: translateY(-1px) scale(0.98);
}

.btn-secondary {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #475569;
    border: 2px solid #cbd5e1;
    padding: 16px 32px;
    border-radius: 14px;
    font-weight: 800;
    font-size: 1.05rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all .3s ease;
    letter-spacing: 0.2px;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    border-color: #94a3b8;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(71, 85, 105, .15);
}

@media (max-width: 768px) {
    .inventario-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .btn-volver {
        width: 100%;
        text-align: center;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
        text-align: center;
    }
}
</style>

<div class="inventario-editar-container <?= $isModal ? 'bm-embed' : '' ?>">
    <div class="inventario-header">
        <div>
            <h1>Editar Producto - Inventario</h1>
            <p>Actualice la información del producto en el sistema</p>
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

        <form method="POST" action="<?= url('/admin/productos/actualizar/' . (int)$producto['id']) ?>">

            <div class="section-title">Información Básica</div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input
                        type="text"
                        name="sku"
                        class="form-input"
                        required
                        value="<?= htmlspecialchars($producto['sku'] ?? '') ?>"
                        placeholder="Código único del producto">
                </div>

                <div class="form-group">
                    <label class="form-label">Código de Barras / QR</label>
                    <input
                        type="text"
                        name="codigo_barra"
                        class="form-input"
                        value="<?= htmlspecialchars($producto['codigo_barra'] ?? '') ?>"
                        placeholder="Código de barras o QR">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group form-grid-full">
                    <label class="form-label">Nombre del Producto <span class="required">*</span></label>
                    <input
                        type="text"
                        name="nombre"
                        class="form-input"
                        required
                        value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>"
                        placeholder="Nombre descriptivo del producto">
                </div>
            </div>

            <div class="section-title">Tipo de Producto</div>

            <div class="tipo-producto-grid">
                <?php
                $tipoActual = strtoupper($producto['tipo_producto'] ?? 'UNIDAD');
                $tipos = [
                    'UNIDAD' => ['icon' => '📦', 'desc' => 'Productos individuales'],
                    'PESO' => ['icon' => '⚖️', 'desc' => 'Productos por peso'],
                    'LONGITUD' => ['icon' => '📏', 'desc' => 'Productos por longitud'],
                    'VOLUMEN' => ['icon' => '🪣', 'desc' => 'Productos por volumen'],
                    'MISC' => ['icon' => '🔧', 'desc' => 'Otros productos']
                ];

                foreach ($tipos as $tipo => $info):
                    $selected = ($tipoActual === $tipo) ? 'selected' : '';
                ?>
                    <div class="tipo-card <?= $selected ?>" onclick="selectTipo(this, '<?= $tipo ?>')">
                        <input type="radio" name="tipo_producto" value="<?= $tipo ?>" <?= $selected ? 'checked' : '' ?>>
                        <span class="tipo-icon"><?= $info['icon'] ?></span>
                        <div class="tipo-label"><?= ucfirst(strtolower($tipo)) ?></div>
                        <div class="tipo-description"><?= $info['desc'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-title">Clasificación</div>

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Categoría <span class="required">*</span></label>
                    <div class="lookup-wrapper">
                        <input type="hidden" name="categoria_id" id="categoria_id" value="<?= $catId ?>">
                        <input
                            type="text"
                            id="categoria_buscar"
                            class="form-input lookup-input"
                            placeholder="Escriba para buscar categoría..."
                            autocomplete="off"
                            value="<?= htmlspecialchars($catText) ?>"
                        >
                        <div class="lookup-results" id="categoria_results">
                            <?php foreach ($categorias as $c): ?>
                                <?php
                                  $id   = (int)$c['id'];
                                  $text = htmlspecialchars($c['nombre']);
                                ?>
                                <div class="lookup-item" data-id="<?= $id ?>" data-label="<?= $text ?>">
                                    <?= $text ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Marca <span class="required">*</span></label>
                    <div class="lookup-wrapper">
                        <input type="hidden" name="marca_id" id="marca_id" value="<?= $marcaId ?>">
                        <input
                            type="text"
                            id="marca_buscar"
                            class="form-input lookup-input"
                            placeholder="Escriba para buscar marca..."
                            autocomplete="off"
                            value="<?= htmlspecialchars($marcaText) ?>"
                        >
                        <div class="lookup-results" id="marca_results">
                            <?php foreach ($marcas as $m): ?>
                                <?php
                                  $id   = (int)$m['id'];
                                  $text = htmlspecialchars($m['nombre']);
                                ?>
                                <div class="lookup-item" data-id="<?= $id ?>" data-label="<?= $text ?>">
                                    <?= $text ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-title">Precios e Inventario</div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Costo de Compra <span class="required">*</span></label>
                    <input
                        type="number"
                        name="costo"
                        class="form-input"
                        required
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars($producto['costo_actual'] ?? '0') ?>"
                        placeholder="0.00">
                </div>

                <div class="form-group">
                    <label class="form-label">Precio de Venta <span class="required">*</span></label>
                    <input
                        type="number"
                        name="precio"
                        class="form-input"
                        required
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars($producto['precio_venta'] ?? '0') ?>"
                        placeholder="0.00">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Stock Actual</label>
                    <input
                        type="number"
                        name="stock"
                        class="form-input"
                        min="0"
                        value="<?= htmlspecialchars($producto['stock'] ?? '0') ?>"
                        placeholder="0">
                </div>

                <div class="form-group">
                    <label class="form-label">Stock Mínimo</label>
                    <input
                        type="number"
                        name="stock_minimo"
                        class="form-input"
                        min="0"
                        value="<?= htmlspecialchars($producto['stock_minimo'] ?? '0') ?>"
                        placeholder="5">
                </div>
            </div>

            <div class="form-actions">
                <?php if (!$isModal): ?>
                    <a href="<?= url('/admin/productos') ?>" class="btn-secondary">Cancelar</a>
                <?php else: ?>
                    <button type="button" class="btn-secondary" onclick="window.parent?.BMModal?.close?.()">Cancelar</button>
                <?php endif; ?>
                <button type="submit" class="btn-primary">Actualizar Producto</button>
            </div>

        </form>
    </div>
</div>

<script>
function selectTipo(card, tipo) {
    // Remover selección previa
    document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('selected'));

    // Seleccionar la nueva
    card.classList.add('selected');

    // Marcar el radio button correspondiente
    const radio = card.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}

function initLookup(inputId, hiddenId, resultsId) {
  const input   = document.getElementById(inputId);
  const hidden  = document.getElementById(hiddenId);
  const results = document.getElementById(resultsId);

  if (!input || !hidden || !results) return;

  const items = Array.from(results.querySelectorAll('.lookup-item'));

  function filtrar() {
    const term = input.value.trim().toLowerCase();
    let visible = 0;

    items.forEach(item => {
      const label = item.dataset.label.toLowerCase();
      if (!term || label.includes(term)) {
        item.style.display = 'block';
        visible++;
      } else {
        item.style.display = 'none';
      }
    });

    results.style.display = visible > 0 ? 'block' : 'none';
  }

  input.addEventListener('focus', () => {
    filtrar();
  });

  input.addEventListener('input', () => {
    hidden.value = '';
    filtrar();
  });

  items.forEach(item => {
    item.addEventListener('click', () => {
      const id    = item.dataset.id;
      const label = item.dataset.label;

      hidden.value = id;
      input.value  = label;
      results.style.display = 'none';
    });
  });

  document.addEventListener('click', (e) => {
    if (!results.contains(e.target) && e.target !== input) {
      results.style.display = 'none';
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  initLookup('categoria_buscar', 'categoria_id', 'categoria_results');
  initLookup('marca_buscar',     'marca_id',     'marca_results');
});
</script>

