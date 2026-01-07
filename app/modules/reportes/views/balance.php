<?php
$balance = $balance ?? [];
$balancePorDia = $balancePorDia ?? [];
$fechaInicio = $fechaInicio ?? date('Y-m-01');
$fechaFin = $fechaFin ?? date('Y-m-d');
?>

<style>
    .balance-container {
        padding: 2rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
    }

    .balance-header {
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
        padding: 1.75rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 20px rgba(10, 61, 145, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .balance-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: white;
        margin: 0 0 0.5rem 0;
    }

    .balance-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-size: 0.95rem;
    }

    .btn-volver {
        background: white;
        color: #0a3d91;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-volver:hover {
        transform: translateY(-2px);
    }

    .filtros-container {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .filtros-form {
        display: flex;
        gap: 1rem;
        align-items: end;
    }

    .form-group {
        flex: 1;
    }

    .form-label {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
    }

    .form-input:focus {
        outline: none;
        border-color: #0a3d91;
    }

    .btn-filtrar {
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-filtrar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(10, 61, 145, 0.3);
    }

    .balance-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .balance-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid;
    }

    .balance-card.ingresos {
        border-color: #10b981;
    }

    .balance-card.egresos {
        border-color: #ef4444;
    }

    .balance-card.neto {
        border-color: #0a3d91;
    }

    .balance-card-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .balance-card-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .balance-card-value {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .balance-card.ingresos .balance-card-value {
        color: #10b981;
    }

    .balance-card.egresos .balance-card-value {
        color: #ef4444;
    }

    .balance-card.neto .balance-card-value {
        color: #0a3d91;
    }

    .balance-card-details {
        font-size: 0.875rem;
        color: #64748b;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .section-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0a3d91;
        margin: 2rem 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid #e0f2fe;
    }

    .section-title::before {
        content: '';
        width: 4px;
        height: 28px;
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
        border-radius: 4px;
    }

    .balance-table-container {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .balance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .balance-table thead {
        background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    }

    .balance-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
    }

    .balance-table thead tr th:first-child {
        border-radius: 0.5rem 0 0 0;
    }

    .balance-table thead tr th:last-child {
        border-radius: 0 0.5rem 0 0;
    }

    .balance-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .balance-table tbody tr:hover {
        background: #f8fafc;
    }

    .balance-table tbody tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge.ingreso {
        background: #d1fae5;
        color: #065f46;
    }

    .badge.egreso {
        background: #fee2e2;
        color: #991b1b;
    }

    .presets-btns {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .preset-btn {
        padding: 0.5rem 1rem;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .preset-btn:hover {
        background: #e2e8f0;
        border-color: #0a3d91;
    }

    .btn-exportar {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-exportar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
</style>

<div class="balance-container">
    <div class="balance-header">
        <div>
            <h1>💹 Balance Financiero</h1>
            <p>Análisis de ingresos y egresos del negocio</p>
        </div>
        <a href="<?= url('/admin/reportes') ?>" class="btn-volver">← Volver a Reportes</a>
    </div>

    <!-- Filtros de fecha -->
    <div class="filtros-container">
        <form method="GET" action="<?= url('/admin/reportes/balance') ?>" class="filtros-form">
            <div class="form-group">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-input" value="<?= htmlspecialchars($fechaInicio) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-input" value="<?= htmlspecialchars($fechaFin) ?>" required>
            </div>
            <button type="submit" class="btn-filtrar">🔍 Generar Reporte</button>
            <button type="button" onclick="exportarExcel()" class="btn-exportar">📄 Exportar Excel</button>
        </form>

        <!-- Presets rápidos -->
        <div style="margin-top: 1rem;">
            <div class="presets-btns">
                <button class="preset-btn" onclick="setRango('hoy')">Hoy</button>
                <button class="preset-btn" onclick="setRango('semana')">Esta Semana</button>
                <button class="preset-btn" onclick="setRango('mes')">Este Mes</button>
                <button class="preset-btn" onclick="setRango('trimestre')">Último Trimestre</button>
                <button class="preset-btn" onclick="setRango('año')">Este Año</button>
            </div>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="balance-cards">
        <div class="balance-card ingresos">
            <div class="balance-card-label">💰 Ingresos (Ventas)</div>
            <div class="balance-card-icon">↗️</div>
            <div class="balance-card-value">Q <?= number_format($balance['ingresos'] ?? 0, 2) ?></div>
            <div class="balance-card-details">
                📝 <?= number_format($balance['cantidad_ventas'] ?? 0) ?> ventas realizadas
            </div>
        </div>

        <div class="balance-card egresos">
            <div class="balance-card-label">📦 Egresos (Compras)</div>
            <div class="balance-card-icon">↘️</div>
            <div class="balance-card-value">Q <?= number_format($balance['egresos'] ?? 0, 2) ?></div>
            <div class="balance-card-details">
                📝 <?= number_format($balance['cantidad_compras'] ?? 0) ?> compras realizadas
            </div>
        </div>

        <div class="balance-card neto">
            <div class="balance-card-label">💹 Balance Neto</div>
            <div class="balance-card-icon">
                <?php
                $balanceNeto = $balance['balance'] ?? 0;
                echo $balanceNeto >= 0 ? '✅' : '⚠️';
                ?>
            </div>
            <div class="balance-card-value">Q <?= number_format($balanceNeto, 2) ?></div>
            <div class="balance-card-details">
                <?= $balanceNeto >= 0 ? 'Saldo positivo ✅' : 'Saldo negativo ⚠️' ?>
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <div class="section-title">💰 Detalle de Ventas (<?= count($detalleVentas ?? []) ?> transacciones)</div>
    <div class="balance-table-container">
        <?php if (empty($detalleVentas)): ?>
            <p style="text-align: center; padding: 2rem; color: #64748b;">No hay ventas en el período seleccionado</p>
        <?php else: ?>
            <table class="balance-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Productos</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalleVentas as $venta): ?>
                        <tr>
                            <td><span class="badge ingreso">#<?= $venta['id'] ?></span></td>
                            <td>📅 <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                            <td><?= htmlspecialchars($venta['cliente'] ?? 'Cliente General') ?></td>
                            <td><?= htmlspecialchars($venta['vendedor'] ?? 'N/A') ?></td>
                            <td><?= $venta['cantidad_productos'] ?> items</td>
                            <td style="font-weight: 700; color: #10b981;">Q <?= number_format($venta['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="5" style="text-align: right; padding-right: 2rem;">TOTAL VENTAS:</td>
                        <td style="color: #10b981;">Q <?= number_format(array_sum(array_column($detalleVentas, 'total')), 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Detalle de Compras -->
    <div class="section-title" style="margin-top: 2rem;">📦 Detalle de Compras (<?= count($detalleCompras ?? []) ?> transacciones)</div>
    <div class="balance-table-container">
        <?php if (empty($detalleCompras)): ?>
            <p style="text-align: center; padding: 2rem; color: #64748b;">No hay compras en el período seleccionado</p>
        <?php else: ?>
            <table class="balance-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Productos</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalleCompras as $compra): ?>
                        <tr>
                            <td><span class="badge egreso">#<?= $compra['id'] ?></span></td>
                            <td>📅 <?= date('d/m/Y H:i', strtotime($compra['fecha'])) ?></td>
                            <td><?= htmlspecialchars($compra['proveedor'] ?? 'N/A') ?></td>
                            <td><?= $compra['productos'] ?? 0 ?> items</td>
                            <td style="font-weight: 700; color: #ef4444;">Q <?= number_format($compra['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="4" style="text-align: right; padding-right: 2rem;">TOTAL COMPRAS:</td>
                        <td style="color: #ef4444;">Q <?= number_format(array_sum(array_column($detalleCompras, 'total')), 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
    function setRango(periodo) {
        const hoy = new Date();
        let fechaInicio, fechaFin;

        fechaFin = hoy.toISOString().split('T')[0];

        switch (periodo) {
            case 'hoy':
                fechaInicio = fechaFin;
                break;
            case 'semana':
                const primerDia = new Date(hoy.setDate(hoy.getDate() - hoy.getDay()));
                fechaInicio = primerDia.toISOString().split('T')[0];
                break;
            case 'mes':
                fechaInicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().split('T')[0];
                break;
            case 'trimestre':
                const hace90 = new Date(hoy.setDate(hoy.getDate() - 90));
                fechaInicio = hace90.toISOString().split('T')[0];
                break;
            case 'año':
                fechaInicio = new Date(hoy.getFullYear(), 0, 1).toISOString().split('T')[0];
                break;
        }

        document.querySelector('input[name="fecha_inicio"]').value = fechaInicio;
        document.querySelector('input[name="fecha_fin"]').value = fechaFin;
        document.querySelector('form').submit();
    }

    function exportarExcel() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        const fechaFin = document.querySelector('input[name="fecha_fin"]').value;

        if (!fechaInicio || !fechaFin) {
            alert('Por favor selecciona un rango de fechas');
            return;
        }

        window.location.href = `<?= url('/admin/reportes/balance') ?>?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&exportar=excel`;
    }
</script>
