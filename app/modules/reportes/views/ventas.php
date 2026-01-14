<?php
$ventas = $ventas ?? [];
$resumenPeriodo = $resumenPeriodo ?? [];
$ventasPorDia = $ventasPorDia ?? [];
$fechaInicio = $fechaInicio ?? date('Y-m-01');
$fechaFin = $fechaFin ?? date('Y-m-d');
?>

<style>
    .ventas-container {
        padding: 2rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
    }

    .ventas-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        padding: 1.75rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ventas-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .btn-volver {
        background: white;
        color: #0ea5e9;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-volver:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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
        border-color: #0ea5e9;
    }

    .btn-filtrar {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-filtrar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
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

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #0ea5e9;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0ea5e9;
        margin-bottom: 0.5rem;
    }

    .stat-subtitle {
        font-size: 0.875rem;
        color: #64748b;
    }

    .section-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0ea5e9;
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
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border-radius: 4px;
    }

    .table-container {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .ventas-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ventas-table thead {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }

    .ventas-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
    }

    .ventas-table thead tr th:first-child {
        border-radius: 0.5rem 0 0 0;
    }

    .ventas-table thead tr th:last-child {
        border-radius: 0 0.5rem 0 0;
    }

    .ventas-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .ventas-table tbody tr:hover {
        background: #f8fafc;
    }

    .ventas-table tbody tr:last-child td {
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

    .badge.primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .presets-btns {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
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
        border-color: #0ea5e9;
    }
</style>

<div class="ventas-container">
    <div class="ventas-header">
        <h1>💰 Reporte de Ventas</h1>
        <a href="<?= url('/admin/reportes') ?>" class="btn-volver">← Volver a Reportes</a>
    </div>

    <!-- Filtros de fecha -->
    <div class="filtros-container">
        <form method="GET" action="<?= url('/admin/reportes/ventas') ?>" class="filtros-form">
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
        <div class="presets-btns">
            <button class="preset-btn" onclick="setRango('hoy')">Hoy</button>
            <button class="preset-btn" onclick="setRango('semana')">Esta Semana</button>
            <button class="preset-btn" onclick="setRango('mes')">Este Mes</button>
            <button class="preset-btn" onclick="setRango('trimestre')">Último Trimestre</button>
            <button class="preset-btn" onclick="setRango('año')">Este Año</button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-label">Subtotal</div>
            <div class="stat-value">Q <?= number_format($resumenPeriodo['total_subtotal'] ?? 0, 2) ?></div>
            <div class="stat-subtitle">
                <?= number_format($resumenPeriodo['cantidad_ventas'] ?? ($resumenPeriodo['total_ventas'] ?? 0)) ?> ventas realizadas
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <div class="section-title">📋 Detalle de Ventas</div>
    <div class="table-container">
        <?php if (empty($ventas)): ?>
            <p style="text-align: center; padding: 2rem; color: #64748b;">No hay ventas en el período seleccionado</p>
        <?php else: ?>
            <table class="ventas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>NIT</th>
                        <th>Vendedor</th>
                        <th>Subtotal</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><span class="badge primary">#<?= $venta['id'] ?></span></td>
                            <td>📅 <?= date('d/m/Y H:i', strtotime($venta['fecha'] ?? $venta['fecha_venta'] ?? 'now')) ?></td>
                            <td><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') ?></td>
                            <td><?= htmlspecialchars($venta['cliente_nit'] ?? 'C/F') ?></td>
                            <td><?= htmlspecialchars($venta['vendedor'] ?? 'N/A') ?></td>
                            <td>Q <?= number_format($venta['subtotal'] ?? 0, 2) ?></td>
                            <td style="font-weight: 700; color: #0ea5e9;">Q <?= number_format($venta['total'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="6" style="text-align: right; padding-right: 2rem;">TOTAL:</td>
                        <td style="color: #0ea5e9;">Q <?= number_format(array_sum(array_column($ventas, 'total')), 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Ventas por Día -->
    <?php if (!empty($ventasPorDia)): ?>
        <div class="section-title" style="margin-top: 2rem;">📊 Ventas por Día</div>
        <div class="table-container">
            <table class="ventas-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cantidad de Ventas</th>
                        <th>Total del Día</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventasPorDia as $dia): ?>
                        <tr>
                            <td style="font-weight: 600;">📅 <?= date('d/m/Y', strtotime($dia['fecha'])) ?></td>
                            <td><?= number_format($dia['cantidad_ventas'] ?? 0) ?> ventas</td>
                            <td style="font-weight: 700; color: #0ea5e9;">Q <?= number_format($dia['total_ventas'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
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

        window.location.href = `<?= url('/admin/reportes/ventas') ?>?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&exportar=excel`;
    }
</script>
