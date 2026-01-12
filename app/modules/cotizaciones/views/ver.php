<div class="card" style="max-width: 900px; margin: 0 auto;">
  <!-- HEADER CON ACCIONES -->
  <div class="no-print" style="padding: 1.5rem; background: white; border-bottom: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1 style="color: #495057; font-size: 1.75rem; font-weight: 700; margin: 0;">
        Ver Cotización #<?= e($cotizacion['id']) ?>
      </h1>
      <p style="color: #6c757d; margin: 0.25rem 0 0 0; font-size: 0.95rem;">
        Detalles completos de la cotización
      </p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
      <a href="<?= url('/admin/cotizaciones') ?>" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
        ← Volver
      </a>
      <?php if ($cotizacion['estado'] === 'ACTIVA'): ?>
        <form method="POST" action="<?= url('/admin/cotizaciones/convertir/' . $cotizacion['id']) ?>" style="display: inline;" class="form-convertir" id="form-convertir-<?= $cotizacion['id'] ?>">
          <button type="button" onclick="mostrarModalConvertir(<?= $cotizacion['id'] ?>)" style="padding: 0.75rem 1.5rem; background: #28a745; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
            ✔️ Convertir a Venta
          </button>
        </form>
      <?php endif; ?>
      <button onclick="window.print()" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
        🖨️ Imprimir
      </button>
    </div>
  </div>

  <!-- CONTENIDO DE LA COTIZACIÓN -->
  <div style="padding: 2rem; background: white;" id="printable-area">

    <!-- ENCABEZADO OPTIMIZADO PARA IMPRESIÓN -->
    <div class="print-header" style="display: grid; grid-template-columns: auto 1fr auto; gap: 2rem; align-items: start; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 3px solid #1e88e5;">

      <!-- LOGO COMPACTO -->
      <div class="company-logo" style="text-align: center;">
        <img src="<?= url('/assets/img/logo_sosa.png') ?>" alt="Comercializadora Sosa" style="max-width: 140px; height: auto; display: block; background: white; padding: 0.5rem; border-radius: 0.5rem; border: 2px solid #1e88e5;">
      </div>

      <!-- INFORMACIÓN DE LA EMPRESA -->
      <div class="company-info" style="text-align: center;">
        <h1 style="margin: 0 0 0.5rem 0; font-size: 1.8rem; font-weight: 800; color: #1e88e5; text-transform: uppercase; letter-spacing: 1px;">
          COMERCIALIZADORA SOSA
        </h1>
        <div style="color: #495057; font-size: 0.95rem; line-height: 1.4;">
          <div style="font-weight: 600; margin-bottom: 0.25rem;">📍 Barrio La Flores, Gualán</div>
          <div style="font-weight: 600; margin-bottom: 0.25rem;">📞 Tel: 4038-7031</div>
        </div>
      </div>

      <!-- DATOS DE LA COTIZACIÓN (COMPACTO) -->
      <div class="quotation-info" style="background: #f8f9fa; padding: 1rem; border-radius: 0.5rem; border: 2px solid #1e88e5; min-width: 200px;">
        <div style="text-align: center; margin-bottom: 0.75rem;">
          <h2 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #1e88e5; text-transform: uppercase;">COTIZACIÓN</h2>
        </div>
        <div style="display: grid; gap: 0.5rem; font-size: 0.9rem;">
          <div style="display: flex; justify-content: space-between;">
            <strong style="color: #495057;">No.:</strong>
            <span style="color: #1e88e5; font-weight: 700;"><?= str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <strong style="color: #495057;">Fecha:</strong>
            <span style="color: #6c757d;">
              <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?>
              <?= date('d/m/Y', strtotime($cotizacion['fecha'])) ?>
            </span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <strong style="color: #495057;">Vencimiento:</strong>
            <span style="color: #6c757d;">
              <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?>
              <?= date('d/m/Y', strtotime($cotizacion['fecha_expiracion'])) ?>
            </span>
          </div>
          <div style="margin-top: 0.5rem; text-align: center;">
            <?php
            $estadoBadge = [
              'ACTIVA' => ['bg' => '#e8f5e9', 'color' => '#2e7d32', 'text' => 'VÁLIDA'],
              'VENCIDA' => ['bg' => '#ffebee', 'color' => '#c62828', 'text' => 'VENCIDA'],
              'CONVERTIDA' => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'text' => 'CONVERTIDA'],
            ];
            $badge = $estadoBadge[$cotizacion['estado']] ?? ['bg' => '#f5f5f5', 'color' => '#616161', 'text' => 'N/A'];
            ?>
            <span style="display: inline-block; padding: 0.4rem 0.8rem; background: <?= $badge['bg'] ?>; color: <?= $badge['color'] ?>; border-radius: 0.35rem; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px;">
              <?= $badge['text'] ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- INFORMACIÓN COMPACTA CLIENTE/VENDEDOR -->
    <div class="client-info-row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">

      <!-- DATOS DEL CLIENTE (MÁS COMPACTO) -->
      <div class="client-details" style="background: #f8f9fa; padding: 1.25rem; border-radius: 0.5rem; border: 1px solid #dee2e6;">
        <div style="display: flex; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #1e88e5;">
          <div style="background: #1e88e5; color: white; padding: 0.5rem; border-radius: 50%; margin-right: 0.75rem; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
            👤
          </div>
          <h3 style="margin: 0; color: #495057; font-size: 1rem; font-weight: 700; text-transform: uppercase;">Facturar a:</h3>
        </div>
        <div class="client-grid" style="display: grid; grid-template-columns: auto 1fr; gap: 0.75rem 1rem; align-items: center;">
          <strong style="color: #495057;">Cliente:</strong>
          <span style="color: #1e88e5; font-weight: 700;"><?= e($cotizacion['cliente_nombre']) ?></span>

          <strong style="color: #495057;">Teléfono:</strong>
          <span style="color: #6c757d;">📞 <?= e($cotizacion['cliente_telefono']) ?></span>

          <?php if (!empty($cotizacion['cliente_direccion'])): ?>
            <strong style="color: #495057;">Dirección:</strong>
            <span style="color: #6c757d; font-size: 0.9rem;"><?= e($cotizacion['cliente_direccion']) ?></span>
          <?php endif; ?>

          <?php if (!empty($cotizacion['cliente_nit'])): ?>
            <strong style="color: #495057;">NIT:</strong>
            <span style="color: #6c757d; font-weight: 600;"><?= e($cotizacion['cliente_nit']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <!-- DATOS DEL VENDEDOR (COMPACTO) -->
      <div class="seller-info" style="background: #f8f9fa; padding: 1.25rem; border-radius: 0.5rem; border: 1px solid #dee2e6;">
        <div style="display: flex; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #28a745;">
          <div style="background: #28a745; color: white; padding: 0.5rem; border-radius: 50%; margin-right: 0.75rem; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
            🛍️
          </div>
          <h3 style="margin: 0; color: #495057; font-size: 1rem; font-weight: 700; text-transform: uppercase;">Vendedor:</h3>
        </div>
        <div style="display: grid; gap: 0.5rem;">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <strong style="color: #495057;">Nombre:</strong>
            <span style="color: #6c757d; font-weight: 600;"><?= e($user['nombre'] ?? 'Usuario') ?></span>
          </div>
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <?php
            $rolBadge = [
              'admin' => ['bg' => '#1e88e5', 'text' => 'Administrador', 'icon' => '👨‍💼'],
              'vendedor' => ['bg' => '#28a745', 'text' => 'Vendedor', 'icon' => '🛍️'],
            ];
            $rol = $rolBadge[$user['rol'] ?? 'vendedor'] ?? ['bg' => '#6c757d', 'text' => 'Usuario', 'icon' => '👤'];
            ?>
            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: <?= $rol['bg'] ?>; color: white; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600;">
              <?= $rol['icon'] ?> <?= $rol['text'] ?>
            </span>
          </div>
          <div style="font-size: 0.85rem; color: #6c757d; margin-top: 0.25rem;">
            <strong>Fecha:</strong> <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?><?= date('d/m/Y H:i', strtotime($cotizacion['created_at'])) ?>
          </div>
        </div>
      </div>
    </div>

    <!-- TABLA DE PRODUCTOS OPTIMIZADA -->
    <div class="products-section" style="margin-bottom: 1.5rem;">
      <div style="display: flex; align-items: center; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 3px solid #1e88e5;">
        <div style="background: #1e88e5; color: white; padding: 0.5rem; border-radius: 50%; margin-right: 0.75rem; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
          📦
        </div>
        <h3 style="margin: 0; color: #495057; font-size: 1.1rem; font-weight: 700; text-transform: uppercase;">
          Detalle de Productos Cotizados
        </h3>
      </div>

      <div class="products-table" style="border: 2px solid #1e88e5; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 2px 8px rgba(30, 136, 229, 0.1);">
        <table style="width: 100%; border-collapse: collapse;">
          <thead style="background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%); color: white;">
            <tr>
              <th style="padding: 0.75rem; text-align: left; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Descripción</th>
              <th style="padding: 0.75rem; text-align: center; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 80px;">Cant.</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 100px;">P. Unit.</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 120px;">Importe</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($detalle)): ?>
              <?php $itemCount = 0;
              foreach ($detalle as $item): $itemCount++; ?>
                <tr style="border-bottom: 1px solid #e9ecef; <?= $itemCount % 2 === 0 ? 'background: #f8f9fa;' : 'background: white;' ?>">
                  <td style="padding: 0.75rem; color: #495057; vertical-align: top;">
                    <div style="font-weight: 700; font-size: 0.95rem; color: #1e88e5; margin-bottom: 0.25rem;">
                      <?= e($item['producto_nombre']) ?>
                    </div>
                    <?php if (!empty($item['producto_sku'])): ?>
                      <div style="font-size: 0.8rem; color: #6c757d; font-family: 'Courier New', monospace; background: #f1f3f4; padding: 0.2rem 0.4rem; border-radius: 3px; display: inline-block;">
                        SKU: <?= e($item['producto_sku']) ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                    <div style="background: #e3f2fd; color: #1565c0; padding: 0.4rem 0.6rem; border-radius: 20px; font-weight: 700; font-size: 0.9rem; display: inline-block; min-width: 50px;">
                      <?= e($item['cantidad']) ?>
                    </div>
                  </td>
                  <td style="padding: 0.75rem; text-align: right; color: #6c757d; font-weight: 600; vertical-align: middle;">
                    <div style="font-size: 0.9rem;">Q <?= number_format($item['precio_unitario'], 2) ?></div>
                  </td>
                  <td style="padding: 0.75rem; text-align: right; vertical-align: middle;">
                    <div style="font-weight: 800; color: #28a745; font-size: 1rem; background: #e8f5e9; padding: 0.4rem 0.6rem; border-radius: 5px;">
                      Q <?= number_format($item['total_linea'], 2) ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="padding: 2rem; text-align: center; color: #6c757d; font-style: italic;">
                  No hay productos registrados en esta cotización
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- RESUMEN FINANCIERO Y TOTALES -->
    <div class="totals-section" style="display: flex; justify-content: flex-end; margin-bottom: 2rem;">
      <div class="totals-box" style="width: 400px;">

        <!-- Subtotal -->
        <div style="background: #f8f9fa; padding: 1rem 1.25rem; border: 1px solid #dee2e6; border-radius: 0.5rem 0.5rem 0 0; display: flex; justify-content: space-between; align-items: center;">
          <span style="color: #6c757d; font-weight: 600; font-size: 1rem;">Subtotal:</span>
          <span style="color: #495057; font-weight: 700; font-size: 1.1rem;">Q <?= number_format($cotizacion['subtotal'], 2) ?></span>
        </div>

        <!-- Total destacado -->
        <div style="background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%); color: white; padding: 1.25rem; border-radius: 0 0 0.5rem 0.5rem; text-align: center; box-shadow: 0 4px 12px rgba(30, 136, 229, 0.3);">
          <div style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; letter-spacing: 1px; text-transform: uppercase;">Total Cotización</div>
          <div style="font-size: 2rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.2); letter-spacing: 1px;">
            Q <?= number_format($cotizacion['total'], 2) ?>
          </div>
          <div style="font-size: 0.85rem; margin-top: 0.5rem; opacity: 0.9;">
            (<?= count($detalle) ?> <?= count($detalle) === 1 ? 'producto' : 'productos' ?>)
          </div>
        </div>
      </div>
    </div>

    <!-- PIE DE PÁGINA PROFESIONAL -->
    <div class="footer-section" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 3px double #1e88e5; text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 1.5rem; border-radius: 0.5rem;">

      <!-- Información de validez -->
      <div style="background: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #dee2e6; display: inline-block;">
        <div style="color: #495057; font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem;">
          ⏰ VALIDEZ DE LA COTIZACIÓN
        </div>
        <div style="color: #dc3545; font-weight: 800; font-size: 1.1rem;">
          Válida hasta: <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?><?= date('d/m/Y', strtotime($cotizacion['fecha_expiracion'])) ?>
        </div>
      </div>

      <!-- Mensaje de agradecimiento -->
      <div style="color: #6c757d; font-size: 0.95rem; line-height: 1.6; max-width: 600px; margin: 0 auto;">
        <div style="font-weight: 700; font-size: 1.1rem; color: #1e88e5; margin-bottom: 0.5rem;">
          ¡Gracias por confiar en nosotros!
        </div>
        <p style="margin: 0.5rem 0; font-weight: 600;">
          Comercializadora Sosa – Abasteciendo calidad, cuando usted la necesita
        </p>
        <p style="margin: 0; font-size: 0.85rem;">
          Esta cotización ha sido generada electrónicamente el <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?><?= date('d/m/Y \a \l\a\s H:i') ?>
        </p>
      </div>

      <!-- Información de contacto final -->
      <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #dee2e6; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.85rem; color: #6c757d;">
        <div>📞 <strong>Ventas:</strong> 4038-7031</div>
      </div>
    </div>

  </div>
</div>

<!-- ESTILOS OPTIMIZADOS PARA IMPRESIÓN PROFESIONAL -->
<style>
  .no-print {
    display: block;
  }

  /* Estilos base para impresión */
  @media print {
    @page {
      margin: 0.5cm;
      size: letter;
    }

    /* Preservar colores y gradientes */
    * {
      -webkit-print-color-adjust: exact !important;
      color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    /* Configuración básica del documento */
    html,
    body {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      height: auto !important;
      overflow: visible !important;
      font-size: 12px !important;
      line-height: 1.4 !important;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }

    /* Ocultar elementos no imprimibles */
    .sidebar,
    .admin-sidebar,
    nav,
    .navbar,
    aside,
    .main-sidebar,
    [class*="sidebar"],
    [class*="nav"],
    header,
    footer,
    .no-print,
    button,
    .card>div:first-child {
      display: none !important;
      visibility: hidden !important;
    }

    /* Contenedor principal */
    .main-content,
    .content-wrapper,
    main,
    .container {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
    }

    /* Card principal */
    .card {
      box-shadow: none !important;
      border: none !important;
      margin: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      page-break-inside: avoid;
    }

    /* Área imprimible */
    #printable-area {
      display: block !important;
      width: 100% !important;
      max-width: 100% !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    /* Encabezado principal */
    .print-header {
      grid-template-columns: auto 1fr auto !important;
      gap: 1rem !important;
      margin-bottom: 1.5rem !important;
      padding-bottom: 1rem !important;
      page-break-inside: avoid;
    }

    .company-logo img {
      max-width: 100px !important;
      padding: 0.25rem !important;
    }

    .company-info h1 {
      font-size: 1.4rem !important;
      margin-bottom: 0.25rem !important;
    }

    .company-info div {
      font-size: 0.8rem !important;
      line-height: 1.2 !important;
    }

    .quotation-info {
      padding: 0.75rem !important;
      min-width: 150px !important;
    }

    .quotation-info h2 {
      font-size: 0.9rem !important;
    }

    .quotation-info div {
      font-size: 0.75rem !important;
      gap: 0.25rem !important;
    }

    /* Información cliente/vendedor */
    .client-info-row {
      grid-template-columns: 2fr 1fr !important;
      gap: 1rem !important;
      margin-bottom: 1rem !important;
      page-break-inside: avoid;
    }

    .client-details,
    .seller-info {
      padding: 0.75rem !important;
    }

    .client-details h3,
    .seller-info h3 {
      font-size: 0.85rem !important;
      margin-bottom: 0.5rem !important;
    }

    .client-details div,
    .seller-info div {
      font-size: 0.75rem !important;
    }

    .client-grid {
      grid-template-columns: auto 1fr !important;
      gap: 0.25rem 0.75rem !important;
    }

    /* Sección de productos */
    .products-section {
      margin-bottom: 1rem !important;
      page-break-inside: avoid;
    }

    .products-section h3 {
      font-size: 0.9rem !important;
      margin-bottom: 0.5rem !important;
    }

    .products-table {
      border-width: 1px !important;
    }

    .products-table table {
      font-size: 0.75rem !important;
    }

    .products-table th {
      padding: 0.5rem !important;
      font-size: 0.7rem !important;
    }

    .products-table td {
      padding: 0.5rem !important;
      font-size: 0.75rem !important;
    }

    .products-table td div {
      font-size: 0.7rem !important;
      padding: 0.2rem 0.4rem !important;
    }

    /* Totales */
    .totals-section {
      margin-bottom: 1rem !important;
      page-break-inside: avoid;
    }

    .totals-box {
      width: 300px !important;
    }

    .totals-box>div:first-child {
      padding: 0.75rem 1rem !important;
      font-size: 0.85rem !important;
    }

    .totals-box>div:last-child {
      padding: 1rem !important;
    }

    .totals-box>div:last-child>div:first-child {
      font-size: 0.8rem !important;
    }

    .totals-box>div:last-child>div:nth-child(2) {
      font-size: 1.4rem !important;
    }

    .totals-box>div:last-child>div:last-child {
      font-size: 0.7rem !important;
    }

    /* Pie de página */
    .footer-section {
      margin-top: 1.5rem !important;
      padding: 1rem !important;
      page-break-inside: avoid;
    }

    .footer-section>div:first-child {
      padding: 0.75rem !important;
      margin-bottom: 0.75rem !important;
      font-size: 0.8rem !important;
    }

    .footer-section>div:first-child>div:first-child {
      font-size: 0.8rem !important;
    }

    .footer-section>div:first-child>div:last-child {
      font-size: 0.9rem !important;
    }

    .footer-section>div:nth-child(2) {
      font-size: 0.75rem !important;
    }

    .footer-section>div:nth-child(2)>div:first-child {
      font-size: 0.85rem !important;
    }

    .footer-section>div:nth-child(2) p {
      font-size: 0.75rem !important;
      margin: 0.25rem 0 !important;
    }

    .footer-section>div:last-child {
      grid-template-columns: repeat(2, 1fr) !important;
      gap: 0.5rem !important;
      font-size: 0.65rem !important;
    }

    /* Espaciado general más compacto */
    h1,
    h2,
    h3 {
      margin: 0.25rem 0 !important;
      page-break-after: avoid;
    }

    p {
      margin: 0.25rem 0 !important;
      page-break-inside: avoid;
    }

    /* Evitar divisiones de página problemáticas */
    tr,
    .client-details,
    .seller-info,
    .products-section,
    .totals-section,
    .footer-section {
      page-break-inside: avoid;
    }

    /* Forzar nueva página si es necesario */
    .page-break {
      page-break-before: always;
    }
  }

  /* Estilos especiales para mejorar legibilidad en impresión */
  @media print {
    .print-header {
      border-bottom: 2px solid #1e88e5 !important;
    }

    .quotation-info {
      border: 1px solid #1e88e5 !important;
    }

    .products-table {
      border: 1px solid #1e88e5 !important;
    }

    .totals-box>div:last-child {
      background: #1e88e5 !important;
      color: white !important;
    }

    .footer-section {
      border-top: 2px solid #1e88e5 !important;
    }
  }
</style>

<!-- Modal de Confirmación para Convertir a Venta -->
<div id="modal-convertir" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
  <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3); max-width: 400px; width: 90%; text-align: center;">
    <div style="margin-bottom: 1.5rem;">
      <div style="width: 60px; height: 60px; background: #28a745; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
        <svg style="width: 30px; height: 30px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <h3 style="margin: 0; color: #333; font-size: 1.25rem; font-weight: 600;">Convertir a Venta</h3>
      <p style="margin: 0.5rem 0 0 0; color: #666;">¿Está seguro que desea convertir esta cotización a venta? Esta acción no se puede deshacer.</p>
    </div>
    <div style="display: flex; gap: 1rem; justify-content: center;">
      <button onclick="cerrarModalConvertir()" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
        Cancelar
      </button>
      <button onclick="confirmarConversion()" style="padding: 0.75rem 1.5rem; background: #28a745; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
        Confirmar
      </button>
    </div>
  </div>
</div>

<script>
  let cotizacionIdActual = null;

  function mostrarModalConvertir(cotizacionId) {
    cotizacionIdActual = cotizacionId;
    const modal = document.getElementById('modal-convertir');
    modal.style.display = 'flex';
  }

  function cerrarModalConvertir() {
    const modal = document.getElementById('modal-convertir');
    modal.style.display = 'none';
    cotizacionIdActual = null;
  }

  function confirmarConversion() {
    if (cotizacionIdActual) {
      const form = document.getElementById(`form-convertir-${cotizacionIdActual}`);
      if (form) {
        form.submit();
      }
    }
  }

  // Cerrar modal si se hace clic fuera de él
  document.getElementById('modal-convertir').addEventListener('click', function(e) {
    if (e.target === this) {
      cerrarModalConvertir();
    }
  });

  // Cerrar modal con tecla Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      cerrarModalConvertir();
    }
  });
</script>
