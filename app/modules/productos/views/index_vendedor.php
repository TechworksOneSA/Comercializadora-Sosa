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
        HEADER (PRO) - VENDEDOR
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
                    <h1 class="productos-title-modern">Catálogo de Productos</h1>
                    <p class="productos-subtitle-modern">Consulta de productos y disponibilidad de stock</p>
                </div>
            </div>

            <!-- BOTÓN ELIMINADO: Nuevo Producto -->

        </div>
    </div>


    <!-- =======================
        KPIs PARA VENDEDORES (SIN VALORES SENSIBLES)
    ======================== -->
    <div class="productos-kpis-grid">
        <div class="kpi-card-producto kpi-total">
            <div class="kpi-icon">📊</div>
            <div class="kpi-value"><?= number_format($kpis['total_productos'] ?? 0) ?></div>
            <div class="kpi-label">Total Productos</div>
        </div>
        <div class="kpi-card-producto kpi-stock">
            <div class="kpi-icon">⚠️</div>
            <div class="kpi-value"><?= number_format($kpis['stock_bajo'] ?? 0) ?></div>
            <div class="kpi-label">Stock Bajo</div>
        </div>
        <div class="kpi-card-producto kpi-activos">
            <div class="kpi-icon">✅</div>
            <div class="kpi-value"><?= number_format($kpis['productos_activos'] ?? 0) ?></div>
            <div class="kpi-label">Productos Activos</div>
        </div>
        <div class="kpi-card-producto kpi-disponibles">
            <div class="kpi-icon">📦</div>
            <div class="kpi-value"><?= number_format($kpis['productos_con_stock'] ?? 0) ?></div>
            <div class="kpi-label">Con Stock Disponible</div>
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
    </div>

    <!-- =======================
        FILTROS AVANZADOS (fuera de la tabla)
    ======================== -->
    <div class="filters-advanced-bar" style="z-index:10001; position:relative;">
        <div class="filter-group">
            <span class="filter-label">🏷️ Categoría:</span>
            <select id="fCategoria" class="filter-select">
                <option value="0">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= ((int)($filters['categoria_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">🔖 Marca:</span>
            <select id="fMarca" class="filter-select">
                <option value="0">Todas</option>
                <?php foreach ($marcas as $marca): ?>
                    <option value="<?= (int)$marca['id'] ?>" <?= ((int)($filters['marca_id'] ?? 0) === (int)$marca['id']) ? 'selected' : '' ?>><?= htmlspecialchars($marca['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">📦 Stock:</span>
            <select id="fStock" class="filter-select">
                <option value="all" <?= (($filters['stock'] ?? 'all') === 'all') ? 'selected' : '' ?>>Todos</option>
                <option value="bajo" <?= (($filters['stock'] ?? 'all') === 'bajo') ? 'selected' : '' ?>>Bajo</option>
                <option value="cero" <?= (($filters['stock'] ?? 'all') === 'cero') ? 'selected' : '' ?>>En Cero</option>
            </select>
        </div>

    </div>


    <!-- =======================
        TABLA (AJAX)
    ======================== -->
    <div id="productosTableWrap" class="table-layer">
        <?php require __DIR__ . "/tabla_vendedor.php"; ?>
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

<style>
    /* Mejora visual para los menús de selección de filtros (categoría, marca) */
    .fbselect {
        position: relative;
        z-index: 10002;
    }
    .fbselect-menu {
        position: fixed !important;
        left: 0;
        top: 0;
        background: #fff;
        border: 1px solid #e3eafc;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 8px 24px rgba(10,36,99,0.10);
        z-index: 999999 !important;
        max-height: 260px;
        overflow-y: auto;
        min-width: 160px;
        display: none;
        width: auto;
    }
    .fbselect.open .fbselect-menu {
        display: block;
    }
    </style>
    <script>
    // Portaliza el menú de selección para que siempre esté por encima de todo
    function positionFbSelectMenus() {
        document.querySelectorAll('.fbselect').forEach(function(fbselect) {
            const menu = fbselect.querySelector('.fbselect-menu');
            if (!menu) return;
            if (fbselect.classList.contains('open')) {
                // Obtener posición absoluta del input
                const input = fbselect.querySelector('input.filter-input');
                const rect = input ? input.getBoundingClientRect() : fbselect.getBoundingClientRect();
                menu.style.left = rect.left + 'px';
                menu.style.top = (rect.bottom + window.scrollY) + 'px';
                menu.style.width = rect.width + 'px';
                menu.style.position = 'fixed';
                menu.style.zIndex = 999999;
            } else {
                menu.style.display = 'none';
            }
        });
    }
    document.addEventListener('click', function(e) {
        // Cierra todos los menús si se hace click fuera
        document.querySelectorAll('.fbselect').forEach(function(fbselect) {
            if (!fbselect.contains(e.target)) {
                fbselect.classList.remove('open');
                positionFbSelectMenus();
            }
        });
    });
    document.querySelectorAll('.fbselect .filter-input').forEach(function(input) {
        input.addEventListener('focus', function(e) {
            const fbselect = e.target.closest('.fbselect');
            fbselect.classList.add('open');
            positionFbSelectMenus();
        });
        input.addEventListener('input', function(e) {
            positionFbSelectMenus();
        });
    });
    window.addEventListener('resize', positionFbSelectMenus);
    window.addEventListener('scroll', positionFbSelectMenus, true);
    </script>
</style>

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
