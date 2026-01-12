<?php
// recibe: $productos
?>
<div class="productos-table-modern">
        <div class="table-scroll-navbar-wrapper">
            <div class="table-scroll-navbar" id="tableScrollNavbar"></div>
            <div class="table-container" style="overflow-x: auto;">
                <table class="table-productos-modern">
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
                            <!-- Mover el bloque de estilos aquí al final para evitar romper la tabla -->
                            <style>
                                /* Estilos adicionales para la vista de vendedor */
                                .descripcion-corta {
                                    font-size: 0.85rem;
                                    color: #6c757d;
                                    margin-top: 4px;
                                    font-style: italic;
                                }

                                .codigo-barras {
                                    display: block;
                                    font-size: 0.75rem;
                                    color: #6c757d;
                                    margin-top: 2px;
                                }

                                .alerta-stock {
                                    font-size: 0.75rem;
                                    color: #dc3545;
                                    margin-top: 2px;
                                    font-weight: 500;
                                }

                                .badge-estado-activo {
                                    background: #d1fae5;
                                    color: #065f46;
                                    padding: 4px 8px;
                                    border-radius: 6px;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                }

                                .badge-estado-inactivo {
                                    background: #fee2e2;
                                    color: #991b1b;
                                    padding: 4px 8px;
                                    border-radius: 6px;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                }

                                .btn-ver {
                                    background: #e0e7ff;
                                    color: #3730a3;
                                    border: 1px solid #c7d2fe;
                                }

                                .btn-ver:hover {
                                    background: #c7d2fe;
                                    border-color: #a5b4fc;
                                }

                                .row-inactivo {
                                    opacity: 0.6;
                                    background-color: #f8f9fa;
                                }
                            </style>
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
        document.addEventListener('touchend', function() { dragging = false; });
    });
    </script>
    /* Estilos adicionales para la vista de vendedor */
    .descripcion-corta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 4px;
        font-style: italic;
    }

    .codigo-barras {
        display: block;
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 2px;
    }

    .alerta-stock {
        font-size: 0.75rem;
        color: #dc3545;
        margin-top: 2px;
        font-weight: 500;
    }

    .badge-estado-activo {
        background: #d1fae5;
        color: #065f46;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-estado-inactivo {
        background: #fee2e2;
        color: #991b1b;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-ver {
        background: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }

    .btn-ver:hover {
        background: #c7d2fe;
        border-color: #a5b4fc;
    }

    .row-inactivo {
        opacity: 0.6;
        background-color: #f8f9fa;
    }
</style>
