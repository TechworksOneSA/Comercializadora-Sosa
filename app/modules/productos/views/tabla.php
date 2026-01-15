<?php
// recibe: $productos
?>
<div class="productos-table-modern" style="z-index:0;position:relative;">
    <div class="table-scroll-navbar-wrapper" style="z-index:0;position:relative;">
        <div class="table-scroll-navbar" id="tableScrollNavbar" style="z-index:0;position:relative;"></div>
        <div class="table-container" style="overflow-x: auto;z-index:0;position:relative;">
            <table class="table-productos-modern" style="z-index:0;position:relative;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Imagen</th>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>Tipo de Producto</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="10" class="empty-state">
                                <div class="empty-state-icon"></div>
                                <p>No se encontraron productos con esos filtros</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $p): ?>
                            <?php
                            $stockClase = 'badge-stock-ok';
                            if (($p['stock'] ?? 0) <= 0) {
                                $stockClase = 'badge-stock-critico';
                            } elseif (($p['stock'] ?? 0) <= ($p['stock_minimo'] ?? 5)) {
                                $stockClase = 'badge-stock-bajo';
                            }

                            // ==============================
                            // Manejar imagen del producto (ROBUSTO)
                            // Soporta:
                            // - JSON array: ["img1.jpg","img2.jpg"]
                            // - filename: "img1.jpg"
                            // - ruta absoluta: "/uploads/productos/img1.jpg"
                            // - URL absoluta: "https://..."
                            // ==============================
                            $imagenPath = $p['imagen_path'] ?? null;
                            $imagenUrl  = null;

                            if (!empty($imagenPath)) {
                                $first = null;

                                // 1) JSON (array de imágenes)
                                $decoded = json_decode($imagenPath, true);
                                if (is_array($decoded) && !empty($decoded)) {
                                    $first = (string)$decoded[0];
                                } else {
                                    $first = (string)$imagenPath;
                                }

                                $first = trim($first);

                                // 2) URL absoluta
                                if (preg_match('#^https?://#i', $first)) {
                                    $imagenUrl = $first;

                                    // 3) Ruta absoluta desde raíz (/uploads/...)
                                } elseif (str_starts_with($first, '/')) {
                                    $imagenUrl = url($first);

                                    // 4) Solo filename
                                } else {
                                    $imagenUrl = url('/uploads/productos/' . $first);
                                }
                            }
                            ?>

                            <tr class="<?= (($p['estado'] ?? 'ACTIVO') === 'INACTIVO') ? 'row-inactivo' : '' ?>">
                                <td class="producto-imagen-cell">
                                    <?php if ($imagenUrl): ?>
                                        <img
                                            src="<?= $imagenUrl ?>"
                                            alt="<?= htmlspecialchars($p['nombre']) ?>"
                                            class="producto-thumbnail"
                                            onerror="this.src='<?= url('/assets/img/no-image.png') ?>'">
                                    <?php else: ?>
                                        <div class="producto-sin-imagen">
                                            <span>📦</span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="producto-sku">
                                    <?= htmlspecialchars($p['sku']) ?>
                                    <?php if (($p['estado'] ?? 'ACTIVO') === 'INACTIVO'): ?>
                                        <span class="producto-inactivo-label">INACTIVO</span>
                                    <?php endif; ?>
                                </td>

                                <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>

                                <td>
                                    <?php if (!empty($p['categoria_nombre'])): ?>
                                        <span class="badge-categoria"><?= htmlspecialchars($p['categoria_nombre']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Sin categoría</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($p['marca_nombre'])): ?>
                                        <span class="badge-marca"><?= htmlspecialchars($p['marca_nombre']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Sin marca</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php
                                    $tipoProducto = strtoupper($p['tipo_producto'] ?? 'UNIDAD');
                                    if ($tipoProducto === 'UNIDAD'):
                                    ?>
                                        <span class="badge-tipo-serie">📦 Aplica Serie</span>
                                    <?php elseif ($tipoProducto === 'MISC'): ?>
                                        <span class="badge-tipo-misc">🔩 Misceláneo</span>
                                    <?php else: ?>
                                        <span class="text-muted"><?= htmlspecialchars($tipoProducto) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="producto-precio">Q <?= number_format($p['precio_venta'], 2) ?></td>

                                <td>
                                    <span class="badge-stock <?= $stockClase ?>">
                                        <?= number_format($p['stock']) ?> uds
                                    </span>
                                </td>

                                <td><span class="fecha-registro"><?= htmlspecialchars($p['fecha_registro'] ?? 'N/A') ?></span></td>

                                <td>
                                    <div class="acciones-flex">
                                        <a
                                            href="<?= url('/admin/productos/editar/' . (int)$p['id']) ?>"
                                            class="btn-accion btn-editar">
                                            Editar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Barra de desplazamiento visual para tablas en móvil */
    .productos-table-modern,
    .table-scroll-navbar-wrapper,
    .table-scroll-navbar,
    .table-container,
    .table-productos-modern,
    .table-productos-modern th,
    .table-productos-modern td,
    .table-productos-modern tr {
        z-index: 0 !important;
        position: relative;
    }

    .table-scroll-navbar-wrapper {
        position: relative;
    }

    .table-scroll-navbar {
        display: none;
        height: 8px;
        background: #e3eafc;
        border-radius: 4px;
        margin-bottom: 6px;
        position: relative;
        overflow: hidden;
    }

    .table-scroll-navbar.active {
        display: block;
    }

    .table-scroll-navbar-thumb {
        height: 100%;
        background: linear-gradient(90deg, #0a3d91 0%, #1565c0 100%);
        border-radius: 4px;
        position: absolute;
        left: 0;
        top: 0;
        width: 40%;
        transition: left 0.15s;
    }

    @media (max-width: 900px) {
        .table-scroll-navbar {
            display: block;
        }

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-productos-modern {
            min-width: 800px;
        }
    }
</style>
<script>
    // Barra de desplazamiento visual para tablas en móvil
    document.addEventListener('DOMContentLoaded', function() {
        var tableContainer = document.querySelector('.table-container');
        var navbar = document.getElementById('tableScrollNavbar');
        if (!tableContainer || !navbar) return;

        // Crear el thumb
        var thumb = document.createElement('div');
        thumb.className = 'table-scroll-navbar-thumb';
        navbar.appendChild(thumb);

        function updateThumb() {
            var scrollW = tableContainer.scrollWidth;
            var clientW = tableContainer.clientWidth;
            if (scrollW <= clientW) {
                navbar.style.display = 'none';
                return;
            }
            navbar.style.display = 'block';
            var ratio = clientW / scrollW;
            var thumbW = Math.max(40, ratio * 100) + '%';
            thumb.style.width = thumbW;
            var left = (tableContainer.scrollLeft / (scrollW - clientW)) * (100 - parseFloat(thumb.style.width));
            thumb.style.left = left + '%';
        }

        tableContainer.addEventListener('scroll', updateThumb);
        window.addEventListener('resize', updateThumb);
        updateThumb();

        // Permitir arrastrar el thumb
        let dragging = false;
        let startX = 0;
        let startScroll = 0;
        thumb.addEventListener('touchstart', function(e) {
            dragging = true;
            startX = e.touches[0].clientX;
            startScroll = tableContainer.scrollLeft;
            e.preventDefault();
        });
        document.addEventListener('touchmove', function(e) {
            if (!dragging) return;
            var dx = e.touches[0].clientX - startX;
            var scrollW = tableContainer.scrollWidth;
            var clientW = tableContainer.clientWidth;
            var maxScroll = scrollW - clientW;
            var percent = dx / navbar.offsetWidth;
            tableContainer.scrollLeft = startScroll + percent * maxScroll;
        });
        document.addEventListener('touchend', function() {
            dragging = false;
        });
    });
</script>
</style>
