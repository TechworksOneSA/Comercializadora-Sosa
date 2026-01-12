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
                </tr>
            </thead>

            <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="8" class="empty-state">
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
                        // Manejo robusto de imagen
                        // ==============================
                        $imagenPath = $p['imagen_path'] ?? null;
                        $imagenUrl  = null;

                        if (!empty($imagenPath)) {
                            $decoded = json_decode($imagenPath, true);
                            $first = is_array($decoded) && !empty($decoded)
                                ? (string)$decoded[0]
                                : (string)$imagenPath;

                            $first = trim($first);

                            if (preg_match('#^https?://#i', $first)) {
                                $imagenUrl = $first;
                            } elseif (str_starts_with($first, '/')) {
                                $imagenUrl = url($first);
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
                                        alt="<?= htmlspecialchars($p['nombre'] ?? 'Producto') ?>"
                                        class="producto-thumbnail"
                                        onerror="this.src='<?= url('/assets/img/no-image.png') ?>'">
                                <?php else: ?>
                                    <div class="producto-sin-imagen">
                                        <span>📦</span>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="producto-sku">
                                <?= htmlspecialchars($p['sku'] ?? '') ?>
                                <?php if (($p['estado'] ?? 'ACTIVO') === 'INACTIVO'): ?>
                                    <span class="producto-inactivo-label">INACTIVO</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($p['nombre'] ?? '') ?></strong>
                            </td>

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

                            <td class="producto-precio">
                                Q <?= number_format((float)($p['precio_venta'] ?? 0), 2) ?>
                            </td>

                            <td>
                                <span class="badge-stock <?= $stockClase ?>">
                                    <?= number_format((float)($p['stock'] ?? 0)) ?> uds
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .row-inactivo {
        opacity: 0.6;
        background-color: #f8f9fa;
    }
</style>
