<?php
$producto = $producto ?? [];
$series = $series ?? [];
?>

<style>
    /* ========================================
   SERIES DE PRODUCTOS - VISTA MODERNA
======================================== */

    .series-container {
        padding: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .series-header {
        background: linear-gradient(135deg, #0a2463 0%, #0f3a7a 35%, #1565c0 70%, #0284c7 100%);
        border-radius: 18px;
        padding: 28px 32px;
        margin-bottom: 32px;
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

    .series-header::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 85% 15%, rgba(255, 255, 255, .12) 0%, transparent 50%),
            radial-gradient(circle at 15% 85%, rgba(255, 255, 255, .08) 0%, transparent 50%);
    }

    .series-header h1 {
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

    .series-header p {
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
        border: 2px solid rgba(255, 255, 255, 0.9);
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
    }

    .btn-volver:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-color: rgba(255, 255, 255, 1);
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 28px rgba(10, 36, 99, .25);
    }

    .producto-info {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 32px;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .producto-info h3 {
        color: #0a2463;
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 16px 0;
    }

    .producto-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-value {
        font-size: 1rem;
        color: #1e293b;
        font-weight: 600;
    }

    .series-table-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 2px solid #e2e8f0;
    }

    .series-table {
        width: 100%;
        border-collapse: collapse;
    }

    .series-table thead {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    }

    .series-table th {
        padding: 16px 20px;
        text-align: left;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 3px solid #0ea5e9;
    }

    .series-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .series-table tbody tr:hover {
        background: #f8fafc;
    }

    .series-table td {
        padding: 16px 20px;
        color: #334155;
        font-weight: 500;
    }

    .estado-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .estado-badge.en-stock {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border: 1px solid #86efac;
    }

    .estado-badge.vendido {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .estado-badge.reservado {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #fcd34d;
    }

    .empty-state {
        text-align: center;
        padding: 64px 32px;
        color: #64748b;
    }

    .empty-state .icon {
        font-size: 4rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 1rem;
        color: #64748b;
    }

    @media (max-width: 768px) {
        .series-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .btn-volver {
            width: 100%;
            justify-content: center;
        }

        .producto-details {
            grid-template-columns: 1fr;
        }

        .series-table-container {
            overflow-x: auto;
        }
    }
</style>

<div class="series-container">
    <!-- Header -->
    <div class="series-header">
        <div>
            <h1>📦 Series del Producto</h1>
            <p>Control de números de serie y estado de inventario</p>
        </div>
        <a href="<?= url('/admin/productos/editar/' . $producto['id']) ?>" class="btn-volver">
            ← Volver al Producto
        </a>
    </div>

    <!-- Información del Producto -->
    <div class="producto-info">
        <h3><?= htmlspecialchars($producto['nombre'] ?? 'Producto') ?></h3>
        <div class="producto-details">
            <div class="detail-item">
                <span class="detail-label">SKU</span>
                <span class="detail-value"><?= htmlspecialchars($producto['sku'] ?? '') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Código de Barras</span>
                <span class="detail-value"><?= htmlspecialchars($producto['codigo_barra'] ?? 'No asignado') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Stock Actual</span>
                <span class="detail-value"><?= number_format($producto['stock'] ?? 0) ?> unidades</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Precio de Venta</span>
                <span class="detail-value">Q <?= number_format($producto['precio_venta'] ?? 0, 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Tabla de Series -->
    <div class="series-table-container">
        <?php if (!empty($series)): ?>
            <table class="series-table">
                <thead>
                    <tr>
                        <th>Número de Serie</th>
                        <th>Estado</th>
                        <th>Fecha de Registro</th>
                        <th>Fecha de Venta</th>
                        <th>Venta ID</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($series as $serie): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($serie['numero_serie'] ?? '') ?></strong>
                            </td>
                            <td>
                                <?php
                                $estado = strtolower($serie['estado'] ?? 'en_stock');
                                $estadoTexto = '';
                                $estadoClass = '';

                                switch ($estado) {
                                    case 'en_stock':
                                        $estadoTexto = 'En Stock';
                                        $estadoClass = 'en-stock';
                                        break;
                                    case 'vendido':
                                        $estadoTexto = 'Vendido';
                                        $estadoClass = 'vendido';
                                        break;
                                    case 'reservado':
                                        $estadoTexto = 'Reservado';
                                        $estadoClass = 'reservado';
                                        break;
                                    default:
                                        $estadoTexto = ucfirst($estado);
                                        $estadoClass = 'en-stock';
                                }
                                ?>
                                <span class="estado-badge <?= $estadoClass ?>">
                                    <?= $estadoTexto ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $fechaRegistro = $serie['created_at'] ?? '';
                                if ($fechaRegistro) {
                                    echo date('d/m/Y H:i', strtotime($fechaRegistro));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $fechaVenta = $serie['fecha_venta'] ?? '';
                                if ($fechaVenta) {
                                    echo date('d/m/Y H:i', strtotime($fechaVenta));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($serie['venta_id'])): ?>
                                    <a href="<?= url('/admin/ventas/ver/' . $serie['venta_id']) ?>"
                                        style="color: #0ea5e9; text-decoration: none; font-weight: 600;">
                                        #<?= $serie['venta_id'] ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($serie['notas'] ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon">📦</div>
                <h3>No hay series registradas</h3>
                <p>Este producto aún no tiene números de serie asignados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
