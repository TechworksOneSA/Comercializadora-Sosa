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

                                    <!-- ✅ Link directo a página completa -->
                                    <a
                                        href="<?= url('/admin/productos/editar/' . (int)$p['id']) ?>"
                                        class="btn-accion btn-editar">
                                        Editar
                                    </a>

                                    <?php if (($p['estado'] ?? 'ACTIVO') === 'ACTIVO'): ?>
                                        <form method="POST"
                                            action="<?= url('/admin/productos/desactivar/' . (int)$p['id']) ?>"
                                            class="inline-form">
                                            <button type="button"
                                                onclick="mostrarModalEstado('desactivar', <?= (int)$p['id'] ?>, '<?= addslashes($p['nombre'] ?? '') ?>')"
                                                class="btn-accion btn-desactivar">Desactivar</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST"
                                            action="<?= url('/admin/productos/activar/' . (int)$p['id']) ?>"
                                            class="inline-form">
                                            <button type="button"
                                                onclick="mostrarModalEstado('activar', <?= (int)$p['id'] ?>, '<?= addslashes($p['nombre'] ?? '') ?>')"
                                                class="btn-accion btn-activar">Activar</button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST"
                                        action="<?= url('/admin/productos/eliminarPermanente/' . (int)$p['id']) ?>"
                                        class="inline-form">
                                        <button type="button"
                                            onclick="mostrarModalEliminarProducto(<?= (int)$p['id'] ?>, '<?= addslashes($p['nombre'] ?? '') ?>')"
                                            class="btn-accion btn-eliminar">Eliminar</button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
