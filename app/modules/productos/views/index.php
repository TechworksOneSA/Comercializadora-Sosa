<?php
// Inicializar variables con valores por defecto
$filters = $filters ?? ["categoria_id" => 0, "marca_id" => 0, "stock" => "all", "estado" => "ALL"];
$categorias = $categorias ?? [];
$marcas = $marcas ?? [];
$q = $q ?? "";
$kpis = $kpis ?? [];
?>

<!-- CSS del módulo -->
<link rel="stylesheet" href="<?= url('/assets/css/productos.css?v=' . time()) ?>">

<!-- JavaScript del módulo -->
<script>
  // Configuración global para JavaScript
  window.productosTableUrl = "<?= url('/admin/productos/tabla') ?>";
  window.modalUrls = {
    crear: "<?= url('/admin/productos/crear') ?>",
    editar: "<?= url('/admin/productos/editar') ?>"
  };
  window.__categorias = <?= json_encode(array_values($categorias), JSON_UNESCAPED_UNICODE) ?>;
  window.__marcas = <?= json_encode(array_values($marcas), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="<?= url('/assets/js/productos.js') ?>"></script>
<script src="<?= url('/assets/js/modal.js') ?>"></script>

<div class="productos-modern">

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-ok">
      <p><?= htmlspecialchars($_GET['msg']) ?></p>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">
      <p><?= htmlspecialchars($_GET['error']) ?></p>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['ok'])): ?>
    <div class="alert alert-ok">
      <p><?= htmlspecialchars($_SESSION['ok']) ?></p>
    </div>
    <?php unset($_SESSION['ok']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['err'])): ?>
    <div class="alert alert-error">
      <p><?= htmlspecialchars($_SESSION['err']) ?></p>
    </div>
    <?php unset($_SESSION['err']); ?>
  <?php endif; ?>


  <!-- =======================
        HEADER (PRO)
  ======================== -->
  <div class="productos-header-modern">
    <div class="productos-header-flex">

      <div class="productos-header-left">
        <div class="productos-header-logo" aria-hidden="true">
          <!-- Icono inventario -->
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M4 7L12 3l8 4-8 4-8-4Z" stroke="white" stroke-width="1.6" />
            <path d="M4 7v10l8 4 8-4V7" stroke="white" stroke-width="1.6" />
            <path d="M12 11v10" stroke="white" stroke-width="1.6" />
          </svg>
        </div>

        <div>
          <h1 class="productos-title-modern">Inventario</h1>
          <p class="productos-subtitle-modern">Gestión completa de productos, stock y valorización</p>
        </div>
      </div>

      <!-- ✅ LINK A PÁGINA CREAR -->
      <a
        href="<?= url('/admin/productos/crear') ?>"
        class="btn-nuevo-producto">
        Nuevo Producto
      </a>

    </div>
  </div>


  <!-- =======================
        KPIs
  ======================== -->
  <div class="productos-kpis-grid">
    <div class="kpi-card-producto kpi-total">
      <div class="kpi-icon">📊</div>
      <div class="kpi-value"><?= number_format($kpis['total_productos'] ?? 0) ?></div>
      <div class="kpi-label">Total Productos</div>
    </div>

    <div class="kpi-card-producto kpi-inventario">
      <div class="kpi-icon">💵</div>
      <div class="kpi-value">Q <?= number_format($kpis['valor_inventario'] ?? 0, 2) ?></div>
      <div class="kpi-label">Valor Inventario</div>
    </div>

    <div class="kpi-card-producto kpi-ganancia">
      <div class="kpi-icon">📈</div>
      <div class="kpi-value">Q <?= number_format(($kpis['valor_inventario'] ?? 0) - ($kpis['costo_inversion'] ?? 0), 2) ?></div>
      <div class="kpi-label">Ganancia Potencial</div>
    </div>

    <div class="kpi-card-producto kpi-stock">
      <div class="kpi-icon">⚠️</div>
      <div class="kpi-value"><?= number_format($kpis['stock_bajo'] ?? 0) ?></div>
      <div class="kpi-label">Stock Bajo</div>
    </div>
  </div>


  <!-- =======================
        BUSCADOR + FILTROS
  ======================== -->
  <div class="productos-search-modern">

    <div class="search-main-bar">
      <div class="search-input-wrapper">
        <span class="search-icon">🔍</span>
        <input
          type="text"
          id="qLive"
          class="search-input-main"
          value="<?= htmlspecialchars($q) ?>"
          placeholder="Busque por SKU, Código de Barras o Nombre del Producto..."
          autocomplete="off"
          autofocus>
      </div>

      <button
        type="button"
        class="btn-clear-filters"
        id="btnClearFilters"
        onclick="clearAllFilters()"
        title="Limpiar filtros">
        <span class="clear-icon">✕</span>
        <span class="clear-text">Limpiar</span>
      </button>
    </div>

    <div class="filters-advanced-bar">

      <div class="filter-group">
        <span class="filter-label">🏷️ Categoría:</span>
        <div class="fbselect" id="fbCat">
          <input
            type="text"
            id="fCategoriaTxt"
            class="filter-input"
            autocomplete="off"
            placeholder="Todas">
          <input type="hidden" id="fCategoria" value="<?= (int)($filters['categoria_id'] ?? 0) ?>">
          <div class="fbselect-menu" id="fbCatMenu" role="listbox" aria-label="Categorías"></div>
        </div>
      </div>

      <div class="filter-group">
        <span class="filter-label">🔖 Marca:</span>
        <div class="fbselect" id="fbMarca">
          <input
            type="text"
            id="fMarcaTxt"
            class="filter-input"
            autocomplete="off"
            placeholder="Todas">
          <input type="hidden" id="fMarca" value="<?= (int)($filters['marca_id'] ?? 0) ?>">
          <div class="fbselect-menu" id="fbMarcaMenu" role="listbox" aria-label="Marcas"></div>
        </div>
      </div>

      <div class="filter-group">
        <span class="filter-label">📦 Stock:</span>
        <select id="fStock" class="filter-select">
          <option value="all" <?= (($filters['stock'] ?? 'all') === 'all') ? 'selected' : '' ?>>Todos</option>
          <option value="bajo" <?= (($filters['stock'] ?? 'all') === 'bajo') ? 'selected' : '' ?>>Bajo</option>
          <option value="cero" <?= (($filters['stock'] ?? 'all') === 'cero') ? 'selected' : '' ?>>En Cero</option>
        </select>
      </div>

      <!-- Filtro de estado eliminado -->

    </div>
  </div>


  <!-- =======================
        TABLA (AJAX)
  ======================== -->
  <div id="productosTableWrap" class="table-layer">
    <?php require __DIR__ . "/tabla.php"; ?>
  </div>


  <!-- =======================
        INFO FILTROS
  ======================== -->
  <div class="filters-info-bar" id="filtersInfoBar">
    <div class="filters-info-content">
      <span class="filters-info-icon">ℹ️</span>
      <span class="filters-info-text" id="filtersInfoText">Mostrando todos los productos</span>
    </div>
  </div>

</div>

<!-- Modal Global -->
<div id="bmModal" class="bm-modal" aria-hidden="true">
  <div class="bm-modal__backdrop" data-close="1"></div>

  <div class="bm-modal__panel" role="dialog" aria-modal="true" aria-labelledby="bmModalTitle">
    <div class="bm-modal__header">
      <h3 class="bm-modal__title" id="bmModalTitle">Cargando…</h3>
      <button type="button" class="bm-modal__close" aria-label="Cerrar" data-close="1">✕</button>
    </div>

    <div class="bm-modal__body" id="bmModalBody">
      <div class="bm-modal__loading">Cargando…</div>
    </div>
  </div>
</div>

<!-- Modal de Activar/Desactivar Producto -->
<div id="modalEstadoProducto" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-icon">
        <svg width="28" height="28" fill="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      <h3 class="modal-title" id="tituloEstadoProducto"></h3>
    </div>
    <div class="modal-body">
      <p class="modal-message" id="mensajeEstadoProducto"></p>
      <p class="modal-submessage">Esta acción cambiará la disponibilidad del producto en el sistema.</p>
    </div>
    <div class="modal-actions">
      <button type="button" class="modal-btn modal-btn-cancel" onclick="cerrarModalEstado()">
        Cancelar
      </button>
      <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmarCambioEstado()" id="btnConfirmarEstado">
        Confirmar
      </button>
    </div>
  </div>
</div>

<!-- Modal de Eliminar Producto -->
<div id="modalEliminarProducto" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-icon">
        <svg width="28" height="28" fill="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </div>
      <h3 class="modal-title">⛔ ¿Eliminar Permanentemente?</h3>
    </div>
    <div class="modal-body">
      <p class="modal-message">Estás a punto de <strong>eliminar permanentemente</strong> el producto <strong id="nombreProductoEliminar"></strong>.</p>
      <p class="modal-submessage">Esta acción NO se puede deshacer. El producto y todo su historial se perderán para siempre.</p>
    </div>
    <div class="modal-actions">
      <button type="button" class="modal-btn modal-btn-cancel" onclick="cerrarModalEliminarProducto()">
        Cancelar
      </button>
      <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmarEliminarProducto()">
        Sí, Eliminar
      </button>
    </div>
  </div>
</div>

<style>
  /* Estilos para los modales personalizados */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
  }

  .modal-overlay.show {
    display: flex;
    opacity: 1;
  }

  .modal-content {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    max-width: 450px;
    width: 90%;
    transform: translateY(-20px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 2px solid rgba(220, 53, 69, 0.1);
  }

  .modal-overlay.show .modal-content {
    transform: translateY(0) scale(1);
  }

  .modal-header {
    background: linear-gradient(135deg, #0a2463 0%, #1565c0 100%);
    padding: 24px 28px;
    border-radius: 14px 14px 0 0;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  #modalEliminarProducto .modal-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  }

  .modal-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
  }

  @keyframes shimmer {

    0%,
    100% {
      transform: translate(-50%, -50%) rotate(0deg);
    }

    50% {
      transform: translate(-50%, -50%) rotate(180deg);
    }
  }

  .modal-icon {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    position: relative;
    z-index: 1;
  }

  .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
  }

  .modal-body {
    padding: 28px;
    text-align: center;
  }

  .modal-message {
    font-size: 1.1rem;
    color: #495057;
    line-height: 1.6;
    margin: 0 0 8px 0;
    font-weight: 500;
  }

  .modal-submessage {
    font-size: 0.95rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
  }

  .modal-actions {
    padding: 0 28px 28px;
    display: flex;
    gap: 12px;
    justify-content: center;
  }

  .modal-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-width: 120px;
  }

  .modal-btn-cancel {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #e9ecef;
  }

  .modal-btn-cancel:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    transform: translateY(-1px);
  }

  .modal-btn-confirm {
    background: linear-gradient(135deg, #0a2463 0%, #1565c0 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
  }

  #modalEliminarProducto .modal-btn-confirm {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
  }

  .modal-btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(10, 36, 99, 0.4);
  }

  #modalEliminarProducto .modal-btn-confirm:hover {
    box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
  }

  .modal-btn-confirm:active {
    transform: translateY(0);
  }
</style>

<script>
  // Variables globales para los modales
  let accionFormElemento = null;
  let productoIdAccion = null;

  // Modal de cambio de estado
  function mostrarModalEstado(tipo, productoId, nombreProducto) {
    const modal = document.getElementById('modalEstadoProducto');
    const titulo = document.getElementById('tituloEstadoProducto');
    const mensaje = document.getElementById('mensajeEstadoProducto');
    const btnConfirmar = document.getElementById('btnConfirmarEstado');

    productoIdAccion = productoId;

    if (tipo === 'activar') {
      titulo.textContent = '¿Activar Producto?';
      mensaje.innerHTML = '¿Estás seguro de activar el producto <strong>' + nombreProducto + '</strong>?';
      btnConfirmar.textContent = 'Sí, Activar';
      accionFormElemento = document.querySelector(`form[action*="/activar/${productoId}"]`);
    } else {
      titulo.textContent = '¿Desactivar Producto?';
      mensaje.innerHTML = '¿Estás seguro de desactivar el producto <strong>' + nombreProducto + '</strong>?';
      btnConfirmar.textContent = 'Sí, Desactivar';
      accionFormElemento = document.querySelector(`form[action*="/desactivar/${productoId}"]`);
    }

    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    // Listeners
    document.addEventListener('keydown', cerrarEstadoConEscape);
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        cerrarModalEstado();
      }
    });
  }

  function cerrarModalEstado() {
    const modal = document.getElementById('modalEstadoProducto');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', cerrarEstadoConEscape);
    accionFormElemento = null;
    productoIdAccion = null;
  }

  function cerrarEstadoConEscape(e) {
    if (e.key === 'Escape') {
      cerrarModalEstado();
    }
  }

  function confirmarCambioEstado() {
    if (accionFormElemento) {
      accionFormElemento.submit();
    }
  }

  // Modal de eliminar producto
  function mostrarModalEliminarProducto(productoId, nombreProducto) {
    const modal = document.getElementById('modalEliminarProducto');
    const nombreSpan = document.getElementById('nombreProductoEliminar');

    productoIdAccion = productoId;
    nombreSpan.textContent = nombreProducto;
    accionFormElemento = document.querySelector(`form[action*="/eliminarPermanente/${productoId}"]`);

    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    // Listeners
    document.addEventListener('keydown', cerrarEliminarProductoConEscape);
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        cerrarModalEliminarProducto();
      }
    });
  }

  function cerrarModalEliminarProducto() {
    const modal = document.getElementById('modalEliminarProducto');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', cerrarEliminarProductoConEscape);
    accionFormElemento = null;
    productoIdAccion = null;
  }

  function cerrarEliminarProductoConEscape(e) {
    if (e.key === 'Escape') {
      cerrarModalEliminarProducto();
    }
  }

  function confirmarEliminarProducto() {
    if (accionFormElemento) {
      accionFormElemento.submit();
    }
  }
</script>
