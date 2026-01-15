<?php
// Inicializar variables con valores por defecto
$filters = $filters ?? ["categoria_id" => 0, "marca_id" => 0, "stock" => "all", "estado" => "ALL"];
$categorias = $categorias ?? [];
$marcas = $marcas ?? [];
$q = $q ?? "";
$kpis = $kpis ?? [];
?>

<!-- CSS del módulo (MISMO CSS) -->
<link rel="stylesheet" href="<?= url('/assets/css/productos.css?v=' . time()) ?>">

<!-- JavaScript del módulo -->
<script>
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

  <!-- HEADER -->
  <div class="productos-header-modern">
    <div class="productos-header-flex">
      <div class="productos-header-left">
        <div class="productos-header-logo">
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

      <a href="<?= url('/admin/productos/crear') ?>" class="btn-nuevo-producto">
        Nuevo Producto
      </a>
    </div>
  </div>

  <!-- KPIs -->
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
      <div class="kpi-value">
        Q <?= number_format(($kpis['valor_inventario'] ?? 0) - ($kpis['costo_inversion'] ?? 0), 2) ?>
      </div>
      <div class="kpi-label">Ganancia Potencial</div>
    </div>

    <div class="kpi-card-producto kpi-stock">
      <div class="kpi-icon">⚠️</div>
      <div class="kpi-value"><?= number_format($kpis['stock_bajo'] ?? 0) ?></div>
      <div class="kpi-label">Stock Bajo</div>
    </div>
  </div>

  <!-- BUSCADOR + FILTROS -->
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

      <button type="button"
        class="btn-clear-filters"
        id="btnClearFilters"
        onclick="clearAllFilters()">
        <span class="clear-icon">✕</span>
        <span class="clear-text">Limpiar</span>
      </button>
    </div>

    <div class="filters-advanced-bar">

      <!-- CATEGORÍA (IDÉNTICO) -->
      <div class="filter-group">
        <span class="filter-label">🏷️ Categoría:</span>
        <div class="fbselect" id="fbCat">
          <input type="text" id="fCategoriaTxt" class="filter-input" autocomplete="off" placeholder="Todas">
          <input type="hidden" id="fCategoria" value="<?= (int)($filters['categoria_id'] ?? 0) ?>">
          <div class="fbselect-menu" id="fbCatMenu" role="listbox" aria-label="Categorías"></div>
        </div>
      </div>

      <!-- MARCA (IDÉNTICO) -->
      <div class="filter-group">
        <span class="filter-label">🔖 Marca:</span>
        <div class="fbselect" id="fbMarca">
          <input type="text" id="fMarcaTxt" class="filter-input" autocomplete="off" placeholder="Todas">
          <input type="hidden" id="fMarca" value="<?= (int)($filters['marca_id'] ?? 0) ?>">
          <div class="fbselect-menu" id="fbMarcaMenu" role="listbox" aria-label="Marcas"></div>
        </div>
      </div>

      <!-- STOCK -->
      <div class="filter-group">
        <span class="filter-label">📦 Stock:</span>
        <select id="fStock" class="filter-select">
          <option value="all" <?= (($filters['stock'] ?? 'all') === 'all') ? 'selected' : '' ?>>Todos</option>
          <option value="bajo" <?= (($filters['stock'] ?? 'all') === 'bajo') ? 'selected' : '' ?>>Bajo</option>
          <option value="cero" <?= (($filters['stock'] ?? 'all') === 'cero') ? 'selected' : '' ?>>En Cero</option>
        </select>
      </div>

    </div>
  </div>

  <!-- TABLA -->
  <div id="productosTableWrap" class="table-layer">
    <?php require __DIR__ . "/tabla.php"; ?>
  </div>

  <!-- INFO FILTROS -->
  <div class="filters-info-bar" id="filtersInfoBar">
    <div class="filters-info-content">
      <span class="filters-info-icon">ℹ️</span>
      <span class="filters-info-text" id="filtersInfoText">
        Mostrando todos los productos
      </span>
    </div>
  </div>

</div>

<!-- MODAL GLOBAL -->
<div id="bmModal" class="bm-modal" aria-hidden="true">
  <div class="bm-modal__backdrop" data-close="1"></div>
  <div class="bm-modal__panel" role="dialog" aria-modal="true">
    <div class="bm-modal__header">
      <h3 class="bm-modal__title">Cargando…</h3>
      <button type="button" class="bm-modal__close" data-close="1">✕</button>
    </div>
    <div class="bm-modal__body" id="bmModalBody">
      <div class="bm-modal__loading">Cargando…</div>
    </div>
  </div>
</div>
