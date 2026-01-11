<?php
// recibe: $productos
?>

<div class="productos-table-modern">
    <div class="table-container">
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

                        // Manejar imagen del producto
                        $imagenPath = $p['imagen_path'] ?? null;
                        $imagenUrl = null;
                        if ($imagenPath) {
                            // Si es JSON con array de imágenes
                            $imagenes = json_decode($imagenPath, true);
                            if (is_array($imagenes) && !empty($imagenes)) {
                                $imagenUrl = url('/uploads/productos/' . $imagenes[0]);
                            } else {
                                $imagenUrl = url('/uploads/productos/' . $imagenPath);
                            }
                        }
                        ?>

                        <tr class="<?= (($p['estado'] ?? 'ACTIVO') === 'INACTIVO') ? 'row-inactivo' : '' ?>">
                            <td class="producto-imagen-cell">
                                <?php if ($imagenUrl): ?>
                                    <img src="<?= $imagenUrl ?>" alt="<?= htmlspecialchars($p['nombre']) ?>" class="producto-thumbnail" onerror="this.src='<?= url('/assets/img/no-image.png') ?>'">
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
