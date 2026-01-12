<div class="card" style="max-width: 900px; margin: 0 auto;">
  <!-- HEADER CON ACCIONES -->
  <div class="no-print" style="padding: 1.5rem; background: white; border-bottom: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1 style="color: #495057; font-size: 1.75rem; font-weight: 700; margin: 0;">
        Ver Venta #<?= e($venta['id']) ?>
      </h1>
      <p style="color: #6c757d; margin: 0.25rem 0 0 0; font-size: 0.95rem;">
        Detalles completos de la venta
      </p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
      <a href="<?= url('/admin/ventas') ?>" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
        ← Volver
      </a>
      <?php if ($venta['estado'] === 'CONFIRMADA'): ?>
        <form method="POST" action="<?= url('/admin/ventas/anular') ?>" style="display: inline;" onsubmit="return confirm('¿Anular esta venta? Se revertirá el stock y se actualizará el total del cliente.')">
          <input type="hidden" name="venta_id" value="<?= $venta['id'] ?>">
          <button type="submit" style="padding: 0.75rem 1.5rem; background: #dc3545; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
            ❌ Anular Venta
          </button>
        </form>
      <?php endif; ?>
      <button onclick="window.print()" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
        🖨️ Imprimir
      </button>
    </div>
  </div>

  <!-- CONTENIDO DE LA VENTA -->
  <div style="padding: 2rem; background: white;" id="printable-area">

    <!-- ENCABEZADO CON LOGO Y DATOS DEL NEGOCIO -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 3px solid #0a3d91;">

      <!-- LOGO Y NOMBRE -->
      <div>
        <div style="background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
          <img src="<?= url('/assets/img/logo_sosa.png') ?>" alt="Comercializadora Sosa" style="max-width: 200px; height: auto; margin: 0 auto; display: block; background: white; padding: 0.5rem; border-radius: 0.5rem;">
          <p style="margin: 0.75rem 0 0 0; font-size: 1.25rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Comercializadora Sosa</p>
        </div>
        <div style="margin-top: 1rem; color: #6c757d; font-size: 0.9rem;">
          <div style="margin-bottom: 0.5rem;">📍 Barrio La Flores, Entrada Principal hacia Plazuela, Gualán</div>
          <div style="margin-bottom: 0.5rem;">📞 Teléfono: 4038-7031 | Whatsapp: 4818-8061</div>
        </div>
      </div>

      <!-- DATOS DE LA VENTA -->
      <div>
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; border-left: 4px solid #0a3d91;">
          <div style="display: grid; gap: 0.75rem;">
            <div>
              <strong style="color: #495057;">Venta #:</strong>
              <span style="color: #0a3d91; font-size: 1.25rem; font-weight: 700; margin-left: 0.5rem;"><?= e($venta['id']) ?></span>
            </div>
            <div>
              <strong style="color: #495057;">Fecha:</strong>
              <span style="margin-left: 0.5rem; color: #6c757d;">
                <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?>
                <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?>
              </span>
            </div>
            <?php if (!empty($venta['cotizacion_id'])): ?>
              <div>
                <strong style="color: #495057;">Origen:</strong>
                <span style="margin-left: 0.5rem; color: #0a3d91; font-weight: 600;">📝 Cotización #<?= e($venta['cotizacion_id']) ?></span>
              </div>
            <?php endif; ?>
            <div>
              <strong style="color: #495057;">Estado:</strong>
              <?php
              $estadoBadge = [
                'CONFIRMADA' => ['bg' => '#d4edda', 'color' => '#155724', 'icon' => '✅'],
                'ANULADA' => ['bg' => '#f8d7da', 'color' => '#721c24', 'icon' => '❌'],
              ];
              $badge = $estadoBadge[$venta['estado']] ?? ['bg' => '#e9ecef', 'color' => '#495057', 'icon' => '❓'];
              ?>
              <span style="display: inline-block; margin-left: 0.5rem; padding: 0.25rem 0.75rem; background: <?= $badge['bg'] ?>; color: <?= $badge['color'] ?>; border-radius: 1rem; font-size: 0.85rem; font-weight: 600;">
                <?= $badge['icon'] ?> <?= e($venta['estado']) ?>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- INFORMACIÓN DEL CLIENTE Y USUARIO -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">

      <!-- DATOS DEL CLIENTE -->
      <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem;">
        <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700; border-bottom: 2px solid #dee2e6; padding-bottom: 0.5rem;">
          👤 Datos del Cliente
        </h3>
        <div style="display: grid; gap: 0.5rem;">
          <div>
            <strong style="color: #495057;">Nombre:</strong>
            <span style="margin-left: 0.5rem; color: #6c757d;"><?= e($venta['cliente_nombre']) ?></span>
          </div>
          <div>
            <strong style="color: #495057;">Teléfono:</strong>
            <span style="margin-left: 0.5rem; color: #6c757d;">📞 <?= e($venta['cliente_telefono']) ?></span>
          </div>
          <?php if (!empty($venta['cliente_direccion'])): ?>
            <div>
              <strong style="color: #495057;">Dirección:</strong>
              <span style="margin-left: 0.5rem; color: #6c757d;"><?= e($venta['cliente_direccion']) ?></span>
            </div>
          <?php endif; ?>
          <?php if (!empty($venta['cliente_nit'])): ?>
            <div>
              <strong style="color: #495057;">NIT:</strong>
              <span style="margin-left: 0.5rem; color: #6c757d;"><?= e($venta['cliente_nit']) ?></span>
            </div>
          <?php endif; ?>
          <div>
            <strong style="color: #495057;">Método de Pago:</strong>
            <span style="margin-left: 0.5rem; color: #6c757d;">💳 <?= e($venta['metodo_pago'] ?? 'No especificado') ?></span>
          </div>
          <?php if (!empty($venta['numero_cheque'])): ?>
            <div>
              <strong style="color: #495057;">N° de Cheque:</strong>
              <span style="margin-left: 0.5rem; color: #6c757d;"><?= e($venta['numero_cheque']) ?></span>
            </div>
          <?php endif; ?>
          <?php if (!empty($venta['numero_boleta'])): ?>
            <div>
              <strong style="color: #495057;">N° de Boleta:</strong>
              <span style="margin-left: 0.5rem; color: #6c757d;"><?= e($venta['numero_boleta']) ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- DATOS DEL USUARIO QUE REALIZÓ LA VENTA -->
      <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem;">
        <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700; border-bottom: 2px solid #dee2e6; padding-bottom: 0.5rem;">
          🧑‍💼 Realizado por
        </h3>
        <div style="display: grid; gap: 0.5rem;">
          <div>
            <strong style="color: #495057;">Usuario:</strong>
            <span style="margin-left: 0.5rem; color: #6c757d;"><?= e($venta['usuario_nombre']) ?></span>
          </div>
          <div>
            <strong style="color: #495057;">Rol:</strong>
            <?php
            $rolBadge = [
              'admin' => ['bg' => '#0a3d91', 'text' => 'Administrador', 'icon' => '👨‍💼'],
              'vendedor' => ['bg' => '#28a745', 'text' => 'Vendedor', 'icon' => '🛍️'],
            ];
            $rol = $rolBadge[$user['rol'] ?? 'vendedor'] ?? ['bg' => '#6c757d', 'text' => 'Usuario', 'icon' => '👤'];
            ?>
            <span style="display: inline-block; margin-left: 0.5rem; padding: 0.25rem 0.75rem; background: <?= $rol['bg'] ?>; color: white; border-radius: 0.375rem; font-size: 0.85rem; font-weight: 600;">
              <?= $rol['icon'] ?> <?= $rol['text'] ?>
            </span>
          </div>
          <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #6c757d;">
            Creada: <?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?><?= date('d/m/Y H:i', strtotime($venta['created_at'])) ?>
          </div>
        </div>
      </div>
    </div>

    <!-- TABLA DE PRODUCTOS -->
    <div style="margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">
        📦 Detalle de Productos
      </h3>

      <div style="border: 2px solid #e9ecef; border-radius: 0.5rem; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
          <thead style="background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white;">
            <tr>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Producto</th>
              <th style="padding: 0.75rem; text-align: center; font-weight: 600; width: 100px;">Cantidad</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 600; width: 120px;">Precio Unit.</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 600; width: 120px;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($detalle)): ?>
              <?php foreach ($detalle as $item): ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                  <td style="padding: 0.75rem; color: #495057;">
                    <div style="font-weight: 600;"><?= e($item['producto_nombre']) ?></div>
                    <?php if (!empty($item['producto_sku'])): ?>
                      <div style="font-size: 0.85rem; color: #6c757d;">SKU: <?= e($item['producto_sku']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td style="padding: 0.75rem; text-align: center; color: #6c757d; font-weight: 600;">
                    <?= e($item['cantidad']) ?>
                  </td>
                  <td style="padding: 0.75rem; text-align: right; color: #6c757d;">
                    Q <?= number_format($item['precio_unitario'], 2) ?>
                  </td>
                  <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #28a745;">
                    Q <?= number_format($item['subtotal'], 2) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="padding: 2rem; text-align: center; color: #6c757d;">
                  No hay productos en esta venta
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TOTALES -->
    <div style="display: flex; justify-content: flex-end;">
      <div style="width: 350px; background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; border: 2px solid #0a3d91;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #dee2e6;">
          <span style="color: #6c757d; font-weight: 600;">Subtotal:</span>
          <span style="color: #495057; font-weight: 700;">Q <?= number_format($venta['subtotal'], 2) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 1rem 0; border-top: 2px solid #0a3d91; border-bottom: 2px solid #dee2e6;">
          <span style="color: #495057; font-weight: 700; font-size: 1.25rem;">TOTAL:</span>
          <span style="color: #28a745; font-weight: 700; font-size: 1.5rem;">Q <?= number_format($venta['total'], 2) ?></span>
        </div>
        <?php
        $totalPagado = (float)($venta['total_pagado'] ?? 0);
        $saldo = $venta['total'] - $totalPagado;
        ?>
        <div style="display: flex; justify-content: space-between; margin-top: 0.75rem; padding-top: 0.75rem;">
          <span style="color: #0a3d91; font-weight: 600;">Pagado:</span>
          <span style="color: #0a3d91; font-weight: 700;">Q <?= number_format($totalPagado, 2) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
          <span style="color: <?= $saldo > 0 ? '#dc3545' : '#28a745' ?>; font-weight: 700;">Saldo:</span>
          <span style="color: <?= $saldo > 0 ? '#dc3545' : '#28a745' ?>; font-weight: 700; font-size: 1.1rem;">Q <?= number_format($saldo, 2) ?></span>
        </div>
      </div>
    </div>

    <!-- PIE DE PÁGINA -->
    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e9ecef; text-align: center; color: #6c757d; font-size: 0.9rem;">
      <p style="margin: 0 0 0.5rem 0;">Venta realizada el <strong><?php if(function_exists('date_default_timezone_set')) date_default_timezone_set('America/Guatemala'); ?><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></strong></p>
        <p style="margin: 0.5rem 0; font-weight: 600;">
          Comercializadora Sosa – !Precios Sin Competencia!
        </p>
      <p style="margin: 0; font-size: 0.85rem;">Gracias por su compra - Comercializadora Sosa</p>
    </div>

  </div>
</div>

<!-- ESTILOS PARA IMPRESIÓN -->
<style>
  .no-print {
    display: block;
  }

  @media print {
    @page {
      margin: 0.3cm;
      size: letter;
    }

    /* Preservar todos los colores */
    * {
      -webkit-print-color-adjust: exact !important;
      color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    html,
    body {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      height: auto !important;
      overflow: visible !important;
      font-size: 11px !important;
    }

    /* Ocultar sidebar, navbar y elementos del layout */
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
    .no-print {
      display: none !important;
      visibility: hidden !important;
    }

    /* Ocultar el header con los botones */
    .card>div:first-child {
      display: none !important;
    }

    /* Ocultar botones y links de acción */
    button,
    a[href],
    form[onsubmit] {
      display: none !important;
    }

    /* El contenedor principal debe ocupar todo el ancho */
    .main-content,
    .content-wrapper,
    main,
    .container {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
    }

    /* Asegurar que el contenido principal se vea */
    .card {
      box-shadow: none !important;
      border: none !important;
      margin: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      page-break-inside: avoid;
    }

    #printable-area {
      display: block !important;
      width: 100% !important;
      max-width: 100% !important;
      padding: 0.5rem !important;
      margin: 0 !important;
      page-break-inside: avoid;
    }

    /* Reducir espaciado drásticamente */
    #printable-area>div {
      margin-bottom: 0.5rem !important;
      padding-bottom: 0.5rem !important;
    }

    /* Encabezado más compacto */
    #printable-area>div:first-child {
      margin-bottom: 0.5rem !important;
      padding-bottom: 0.5rem !important;
      border-bottom-width: 2px !important;
    }

    h1,
    h2,
    h3 {
      margin: 0.25rem 0 !important;
      font-size: 1rem !important;
    }

    p {
      margin: 0.25rem 0 !important;
      font-size: 0.85rem !important;
    }

    /* Asegurar que las tablas se vean bien */
    table {
      page-break-inside: avoid;
      width: 100%;
      font-size: 0.8rem !important;
    }

    tr {
      page-break-inside: avoid;
      page-break-after: auto;
    }

    td,
    th {
      padding: 0.35rem !important;
      font-size: 0.8rem !important;
    }

    thead {
      display: table-header-group;
    }

    tbody {
      display: table-row-group;
    }

    /* Logo mucho más pequeño */
    img[alt="Comercializadora Sosa"] {
      max-width: 120px !important;
      padding: 0.25rem !important;
    }

    /* Reducir todos los paddings */
    [style*="padding: 2rem"],
    [style*="padding: 1.5rem"],
    [style*="padding: 1rem"] {
      padding: 0.5rem !important;
    }

    /* Reducir gap de grids */
    [style*="grid-template-columns"] {
      gap: 0.5rem !important;
    }

    /* Secciones más compactas */
    [style*="background: #f8f9fa"] {
      padding: 0.75rem !important;
    }

    /* Ajustar el área de totales */
    #printable-area>div:last-child {
      margin-top: 0.5rem !important;
      padding-top: 0.5rem !important;
    }

    /* Pie de página más compacto */
    [style*="border-top: 2px solid #e9ecef"] {
      margin-top: 0.75rem !important;
      padding-top: 0.5rem !important;
    }

    /* Reducir tamaño de fuente en general */
    div,
    span {
      font-size: 0.85rem !important;
    }

    strong {
      font-size: 0.85rem !important;
    }
  }
</style>
