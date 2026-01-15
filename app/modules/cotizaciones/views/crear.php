<div class="card" style="max-width: 1200px; margin: 0 auto;">
  <!-- HEADER -->
  <div class="card-header" style="background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); padding: 2rem;">
    <h1 class="card-title" style="color: white; font-size: 1.75rem; font-weight: 700; margin: 0;">
      💰 Nueva Venta
    </h1>
    <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
      Registra una venta seleccionando cliente y productos
    </p>
  </div>

  <!-- ERRORES -->
  <?php if (!empty($errors)): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
      <strong>⚠️ Errores:</strong>
      <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
        <?php foreach ($errors as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- FORMULARIO -->
  <form method="POST" action="<?= url('/admin/ventas/guardar') ?>" id="formVenta" style="padding: 2rem;">

    <!-- SECCIÓN: DATOS GENERALES -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📋 Datos Generales</h3>

      <div>
        <!-- FECHA DE LA VENTA -->
        <div style="margin-bottom: 1rem;">
          <label for="fecha_venta" style="font-weight: 500; color: #495057;">Fecha de la venta</label>
          <input type="date" name="fecha_venta" id="fecha_venta" class="form-control" required
            value="<?= isset($old['fecha_venta']) ? htmlspecialchars($old['fecha_venta']) : date('Y-m-d') ?>"
            style="max-width: 220px; margin-left: 1rem;">
        </div>

        <!-- CLIENTE -->
        <div>
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
            Cliente <span style="color: #dc3545;">*</span>
          </label>
          <div style="position: relative;">
            <input
              type="text"
              id="buscarCliente"
              placeholder="🔍 Buscar por nombre o NIT..."
              autocomplete="off"
              style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; background: white;"
              onfocus="this.style.borderColor='#0a3d91'; mostrarResultadosClientes()"
              onblur="setTimeout(() => ocultarResultadosClientes(), 200)"
              oninput="buscarClientes()">
            <input type="hidden" name="cliente_id" id="cliente_id" required>
            <div id="resultadosClientes" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 300px; overflow-y: auto; background: white; border: 2px solid #0a3d91; border-top: none; border-radius: 0 0 0.5rem 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- SECCIÓN: PRODUCTOS -->
    <div style="background: #fff; border: 2px solid #e9ecef; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">🛒 Seleccionar Productos</h3>

      <div style="display: grid; grid-template-columns: 3fr 1fr auto; gap: 1rem; align-items: end;">
        <div>
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">Producto</label>
          <div style="position: relative;">
            <input
              type="text"
              id="buscarProducto"
              placeholder="🔍 Buscar por nombre"
              autocomplete="off"
              style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; background: white;"
              onfocus="this.style.borderColor='#0a3d91'; mostrarResultadosProductos()"
              onblur="setTimeout(() => ocultarResultadosProductos(), 200)"
              oninput="buscarProductos()">
            <div id="resultadosProductos" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 300px; overflow-y: auto; background: white; border: 2px solid #0a3d91; border-top: none; border-radius: 0 0 0.5rem 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;"></div>
          </div>
        </div>

        <div>
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">Cantidad</label>
          <input
            type="number"
            id="inputCantidad"
            value="1"
            min="1"
            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem;">
        </div>

        <button
          type="button"
          id="btnAgregarProducto"
          style="padding: 0.75rem 1.5rem; background: #28a745; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; font-size: 0.95rem;">
          ➕ Agregar
        </button>
      </div>

      <!-- (El input del scanner se inyecta automáticamente aquí, debajo del H3) -->
    </div>

    <!-- TABLA DE PRODUCTOS SELECCIONADOS -->
    <div style="margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📦 Productos en la Venta</h3>

      <div style="border: 2px solid #e9ecef; border-radius: 0.5rem; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
          <thead style="background: #f8f9fa;">
            <tr>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057;">Producto</th>
              <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #495057; width: 140px;">Cantidad</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Precio Unit.</th>
              <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #495057; width: 120px;">Subtotal</th>
              <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #495057; width: 80px;">Acción</th>
            </tr>
          </thead>
          <tbody id="tablaProductos">
            <tr>
              <td colspan="5" style="padding: 2rem; text-align: center; color: #6c757d;">
                No hay productos agregados. Usa el selector de arriba para agregar.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TOTALES -->
      <div style="display: flex; justify-content: flex-end; margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 0.5rem;">
        <div style="text-align: right;">
          <div style="font-size: 0.95rem; color: #6c757d; margin-bottom: 0.5rem;">
            Subtotal: <span style="font-weight: 700; color: #495057;">Q <span id="subtotalDisplay">0.00</span></span>
          </div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #28a745;">
            Total: Q <span id="totalDisplay">0.00</span>
          </div>
        </div>
      </div>
    </div>

    <!-- INPUTS OCULTOS PARA PRODUCTOS -->
    <div id="inputsProductos"></div>

    <!-- BOTONES -->
    <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 2rem; border-top: 2px solid #e9ecef;">
      <a
        href="<?= url('/admin/ventas') ?>"
        style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
        ← Cancelar
      </a>
      <button
        type="submit"
        id="btnGuardar"
        style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(10, 61, 145, 0.3);">
        💾 Guardar Venta
      </button>
    </div>

  </form>
</div>

<?php
$series_existentes = $series_existentes ?? [];
?>

<script>
  // Datos PHP a JavaScript
  const clientesData = <?= json_encode($clientes) ?>;

  // Normalizamos productos desde PHP (si su array $productos no trae requiere_serie, igual funcionará por backend)
  const productosData = <?= json_encode(array_map(function($p) use ($series_existentes) {
    $data = [
      'id' => (int)($p['id'] ?? 0),
      'nombre' => (string)($p['nombre'] ?? ''),
      'sku' => (string)($p['sku'] ?? ''),
      'codigo_barra' => (string)($p['codigo_barra'] ?? ''),
      'stock' => (int)($p['stock'] ?? 0),
      'precio_venta' => (float)($p['precio_venta'] ?? 0),
      'requiere_serie' => (int)($p['requiere_serie'] ?? 0),
      'numero_serie' => '',
    ];
    if (isset($series_existentes[$data['id']])) {
      $data['numero_serie'] = $series_existentes[$data['id']];
    }
    return $data;
  }, $productos)) ?>;

  // Estado
  let productosSeleccionados = []; // [{id,nombre,precio,cantidad,stock,requiere_serie,numero_serie}]
  let productoSeleccionado = null;

  /* =========================================================
   * Toast simple
   * ========================================================= */
  function showToast(type, message) {
    const colors = {
      success: '#28a745',
      warning: '#ffc107',
      error:   '#dc3545',
      info:    '#17a2b8'
    };

    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.right = '18px';
    toast.style.top = '18px';
    toast.style.zIndex = '99999';
    toast.style.padding = '12px 14px';
    toast.style.borderRadius = '10px';
    toast.style.color = '#fff';
    toast.style.fontWeight = '700';
    toast.style.fontSize = '0.95rem';
    toast.style.boxShadow = '0 8px 18px rgba(0,0,0,0.18)';
    toast.style.background = colors[type] || colors.info;
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px)';
    toast.style.transition = 'all .18s ease';

    document.body.appendChild(toast);

    requestAnimationFrame(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-10px)';
      setTimeout(() => toast.remove(), 200);
    }, 1600);
  }

  /* =========================================================
   * Scanner input (inyectado)
   * ========================================================= */
  let processingScan = false;

  function asegurarScannerInput() {
    const h3s = document.querySelectorAll('h3');
    let productosH3 = null;

    h3s.forEach(h3 => {
      if ((h3.textContent || '').includes('🛒 Seleccionar Productos')) productosH3 = h3;
    });

    if (!productosH3) return null;

    let scannerInput = document.getElementById('productoScanner');
    if (scannerInput) return scannerInput;

    scannerInput = document.createElement('input');
    scannerInput.type = 'text';
    scannerInput.id = 'productoScanner';
    scannerInput.placeholder = 'Escanear serie / código de barra / SKU y presione Enter';
    scannerInput.autocomplete = 'off';
    scannerInput.style = 'width: 100%; padding: 0.75rem 1rem; border: 2px solid #0a3d91; border-radius: 0.5rem; font-size: 0.95rem; margin: 0 0 1rem 0; background: #fff;';

    productosH3.insertAdjacentElement('afterend', scannerInput);
    return scannerInput;
  }

  /* =========================================================
   * Lógica POS para agregar productos (SERIE y NO-SERIE)
   * ========================================================= */
  function agregarProductoPOS(producto, q, cantidad = 1) {
    const requiereSerie = parseInt(producto.requiere_serie || 0, 10) === 1;
    const stock = parseInt(producto.stock || 0, 10);
    const precio = parseFloat(producto.precio_venta || producto.precio || 0);

    if (!cantidad || cantidad <= 0) cantidad = 1;

    if (stock <= 0) {
      showToast('warning', 'Sin stock');
      return false;
    }

    if (requiereSerie) {
      const serie = (q || '').trim();
      if (!serie) {
        showToast('warning', 'Serie inválida');
        return false;
      }

      // ✅ MISMA SERIE => SUMA CANTIDAD (NO BLOQUEAR)
      const existenteSerie = productosSeleccionados.find(p =>
        String(p.id) === String(producto.id) &&
        parseInt(p.requiere_serie || 0, 10) === 1 &&
        String(p.numero_serie || '') === serie
      );

      if (existenteSerie) {
        const actual = parseInt(existenteSerie.cantidad, 10) || 1;
        const nuevaCantidad = actual + cantidad;

        if (nuevaCantidad > stock) {
          showToast('warning', `Stock máximo: ${stock}`);
          return false;
        }

        existenteSerie.cantidad = nuevaCantidad;
        return true;
      }

      if (cantidad > stock) {
        showToast('warning', `Stock insuficiente (máx: ${stock})`);
        return false;
      }

      productosSeleccionados.push({
        id: String(producto.id),
        nombre: producto.nombre,
        precio: precio,
        cantidad: cantidad,
        stock: stock,
        requiere_serie: 1,
        numero_serie: serie
      });
      return true;
    }

    // NO-SERIE => SUMA por ID
    const existente = productosSeleccionados.find(p =>
      String(p.id) === String(producto.id) && parseInt(p.requiere_serie || 0, 10) === 0
    );

    if (existente) {
      const actual = parseInt(existente.cantidad, 10) || 0;
      const nuevaCantidad = actual + cantidad;

      if (nuevaCantidad > stock) {
        showToast('warning', `Stock máximo: ${stock}`);
        return false;
      }

      existente.cantidad = nuevaCantidad;
      return true;
    }

    if (cantidad > stock) {
      showToast('warning', `Stock insuficiente (máx: ${stock})`);
      return false;
    }

    productosSeleccionados.push({
      id: String(producto.id),
      nombre: producto.nombre,
      precio: precio,
      cantidad: cantidad,
      stock: stock,
      requiere_serie: 0,
      numero_serie: ''
    });

    return true;
  }

  /* =========================================================
   * Render / Totales
   * ========================================================= */
  function calcularTotales() {
    let subtotal = 0;
    productosSeleccionados.forEach(p => {
      subtotal += (parseInt(p.cantidad, 10) || 0) * (parseFloat(p.precio) || 0);
    });

    const total = subtotal;

    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    if (subtotalDisplay) subtotalDisplay.textContent = subtotal.toFixed(2);
    if (totalDisplay) totalDisplay.textContent = total.toFixed(2);
  }

  function renderizarTabla() {
    const tablaProductos = document.getElementById('tablaProductos');
    const inputsProductos = document.getElementById('inputsProductos');
    if (!tablaProductos || !inputsProductos) return;

    if (productosSeleccionados.length === 0) {
      tablaProductos.innerHTML = `
        <tr>
          <td colspan="5" style="padding: 2rem; text-align: center; color: #6c757d;">
            No hay productos agregados. Usa el selector de arriba para agregar.
          </td>
        </tr>
      `;
      inputsProductos.innerHTML = '';
      calcularTotales();
      return;
    }

    let html = '';
    let inputsHtml = '';

    productosSeleccionados.forEach((prod, index) => {
      const requiereSerie = parseInt(prod.requiere_serie || 0, 10) === 1;
      const subtotalLinea = (parseInt(prod.cantidad, 10) || 0) * (parseFloat(prod.precio) || 0);

      const serieLabel = requiereSerie && prod.numero_serie
        ? `<br><small style="color:#6c757d;font-weight:600;">Serie: ${String(prod.numero_serie).replace(/</g,'&lt;')}</small>`
        : '';

      html += `
        <tr style="border-bottom: 1px solid #e9ecef;">
          <td style="padding: 0.75rem; color: #495057; font-weight: 600;">
            ${prod.nombre}
            ${serieLabel}
            <br><small style="color: #6c757d; font-weight: 400;">Stock disponible: ${prod.stock}</small>
          </td>

          <td style="padding: 0.75rem; text-align: center;">
            <input
              type="number"
              class="cantidad-input"
              data-index="${index}"
              value="${parseInt(prod.cantidad,10) || 1}"
              min="1"
              max="${parseInt(prod.stock,10) || 1}"
              style="width: 90px; padding: 0.5rem; border: 2px solid #e9ecef; border-radius: 0.375rem; text-align: center;"
            >
          </td>

          <td style="padding: 0.75rem; text-align: right; color: #6c757d;">
            Q ${Number(prod.precio).toFixed(2)}
          </td>

          <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #28a745;">
            Q ${subtotalLinea.toFixed(2)}
          </td>

          <td style="padding: 0.75rem; text-align: center;">
            <button
              type="button"
              class="btn-quitar"
              data-index="${index}"
              style="padding: 0.5rem 0.75rem; background: #dc3545; color: white; border: none; border-radius: 0.375rem; cursor: pointer; font-weight: 600;"
            >
              🗑️
            </button>
          </td>
        </tr>
      `;

      // Inputs ocultos: en ventas mandamos serie si aplica
      inputsHtml += `
        <input type="hidden" name="producto_id[]" value="${String(prod.id)}">
        <input type="hidden" name="cantidad[]" class="hidden-cantidad-${index}" value="${parseInt(prod.cantidad,10)||1}">
        <input type="hidden" name="numero_serie[]" value="${requiereSerie ? String(prod.numero_serie || '') : ''}">
      `;
    });

    tablaProductos.innerHTML = html;
    inputsProductos.innerHTML = inputsHtml;

    // Cambios manuales de cantidad (aplica a SERIE y NO-SERIE)
    document.querySelectorAll('.cantidad-input').forEach(input => {
      input.addEventListener('change', function() {
        const idx = parseInt(this.dataset.index, 10);
        const prod = productosSeleccionados[idx];
        if (!prod) return;

        let cantidad = parseInt(this.value, 10);
        if (!cantidad || cantidad <= 0) {
          cantidad = 1;
          this.value = 1;
        }

        const stock = parseInt(prod.stock, 10) || 0;
        if (stock > 0 && cantidad > stock) {
          showToast('warning', `Stock máximo: ${stock}`);
          cantidad = stock;
          this.value = cantidad;
        }

        productosSeleccionados[idx].cantidad = cantidad;

        const hidden = document.querySelector(`.hidden-cantidad-${idx}`);
        if (hidden) hidden.value = cantidad;

        renderizarTabla();
      });
    });

    // Quitar
    document.querySelectorAll('.btn-quitar').forEach(btn => {
      btn.addEventListener('click', function() {
        const idx = parseInt(this.dataset.index, 10);
        productosSeleccionados.splice(idx, 1);
        renderizarTabla();
      });
    });

    calcularTotales();
  }

  /* =========================================================
   * Clientes (igual que antes)
   * ========================================================= */
  function buscarClientes() {
    const buscarClienteInput = document.getElementById('buscarCliente');
    const resultadosClientesDiv = document.getElementById('resultadosClientes');
    if (!buscarClienteInput || !resultadosClientesDiv) return;

    const termino = (buscarClienteInput.value || '').toLowerCase().trim();

    if (!termino) {
      resultadosClientesDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
      resultadosClientesDiv.style.display = 'block';
      return;
    }

    const resultados = clientesData.filter(cliente => {
      const nombre = (cliente.nombre + ' ' + cliente.apellido).toLowerCase();
      const nit = (cliente.nit || '').toLowerCase();
      const telefono = (cliente.telefono || '').toLowerCase();
      return nombre.includes(termino) || nit.includes(termino) || telefono.includes(termino);
    });

    if (resultados.length === 0) {
      resultadosClientesDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">No se encontraron clientes</div>';
      resultadosClientesDiv.style.display = 'block';
      return;
    }

    let html = '';
    resultados.forEach(cliente => {
      html += `
        <div
          onclick="seleccionarCliente(${cliente.id}, '${cliente.nombre} ${cliente.apellido}')"
          style="padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.2s;"
          onmouseover="this.style.background='#f8f9fa'"
          onmouseout="this.style.background='white'"
        >
          <div style="font-weight: 600; color: #495057;">${cliente.nombre} ${cliente.apellido}</div>
          <div style="font-size: 0.85rem; color: #6c757d;">NIT: ${cliente.nit || 'N/A'} | 📞 ${cliente.telefono || ''}</div>
        </div>
      `;
    });

    resultadosClientesDiv.innerHTML = html;
    resultadosClientesDiv.style.display = 'block';
  }

  function seleccionarCliente(id, nombre) {
    const clienteIdInput = document.getElementById('cliente_id');
    const buscarClienteInput = document.getElementById('buscarCliente');
    const resultadosClientesDiv = document.getElementById('resultadosClientes');
    if (clienteIdInput) clienteIdInput.value = id;
    if (buscarClienteInput) buscarClienteInput.value = nombre;
    if (resultadosClientesDiv) resultadosClientesDiv.style.display = 'none';
  }

  function mostrarResultadosClientes() {
    const buscarClienteInput = document.getElementById('buscarCliente');
    const resultadosClientesDiv = document.getElementById('resultadosClientes');
    if (!buscarClienteInput || !resultadosClientesDiv) return;

    if (buscarClienteInput.value) buscarClientes();
    else {
      resultadosClientesDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
      resultadosClientesDiv.style.display = 'block';
    }
  }

  function ocultarResultadosClientes() {
    const resultadosClientesDiv = document.getElementById('resultadosClientes');
    if (resultadosClientesDiv) resultadosClientesDiv.style.display = 'none';
  }

  /* =========================================================
   * Productos (búsqueda manual)
   * ========================================================= */
  function buscarProductos() {
    const buscarProductoInput = document.getElementById('buscarProducto');
    const resultadosProductosDiv = document.getElementById('resultadosProductos');
    if (!buscarProductoInput || !resultadosProductosDiv) return;

    const termino = (buscarProductoInput.value || '').toLowerCase().trim();

    if (!termino) {
      resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
      resultadosProductosDiv.style.display = 'block';
      return;
    }

    const resultados = productosData.filter(producto => {
      const nombre = (producto.nombre || '').toLowerCase();
      const sku = (producto.sku || '').toLowerCase();
      const codigo = (producto.codigo_barra || '').toLowerCase();
      return nombre.includes(termino) || sku.includes(termino) || codigo.includes(termino);
    });

    if (resultados.length === 0) {
      resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">No se encontraron productos</div>';
      resultadosProductosDiv.style.display = 'block';
      return;
    }

    let html = '';
    resultados.forEach(producto => {
      const stockNum = parseInt(producto.stock || 0, 10);
      const stockColor = stockNum > 10 ? '#28a745' : (stockNum > 0 ? '#ffc107' : '#dc3545');
      html += `
        <div
          onclick='seleccionarProducto(${JSON.stringify(producto)})'
          style="padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.2s;"
          onmouseover="this.style.background='#f8f9fa'"
          onmouseout="this.style.background='white'"
        >
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
              <div style="font-weight: 600; color: #495057;">${producto.nombre}</div>
              <div style="font-size: 0.85rem; color: #6c757d;">
                SKU: ${producto.sku || 'N/A'} | Q ${(parseFloat(producto.precio_venta || 0)).toFixed(2)}
              </div>
            </div>
            <div style="font-weight: 700; color: ${stockColor}; font-size: 0.9rem;">
              Stock: ${stockNum}
            </div>
          </div>
        </div>
      `;
    });

    resultadosProductosDiv.innerHTML = html;
    resultadosProductosDiv.style.display = 'block';
  }

  function seleccionarProducto(producto) {
    productoSeleccionado = producto;
    const buscarProductoInput = document.getElementById('buscarProducto');
    const resultadosProductosDiv = document.getElementById('resultadosProductos');
    const inputCantidad = document.getElementById('inputCantidad');
    if (buscarProductoInput) buscarProductoInput.value = producto.nombre;
    if (resultadosProductosDiv) resultadosProductosDiv.style.display = 'none';
    if (inputCantidad) inputCantidad.focus();
  }

  function mostrarResultadosProductos() {
    const buscarProductoInput = document.getElementById('buscarProducto');
    const resultadosProductosDiv = document.getElementById('resultadosProductos');
    if (!buscarProductoInput || !resultadosProductosDiv) return;

    if (buscarProductoInput.value) buscarProductos();
    else {
      resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
      resultadosProductosDiv.style.display = 'block';
    }
  }

  function ocultarResultadosProductos() {
    const resultadosProductosDiv = document.getElementById('resultadosProductos');
    if (resultadosProductosDiv) resultadosProductosDiv.style.display = 'none';
  }

  /* =========================================================
   * INIT
   * ========================================================= */
  window.addEventListener('DOMContentLoaded', function() {
    const buscarProductoInput = document.getElementById('buscarProducto');
    const inputCantidad = document.getElementById('inputCantidad');
    const btnAgregarProducto = document.getElementById('btnAgregarProducto');
    const formVenta = document.getElementById('formVenta');
    const clienteIdInput = document.getElementById('cliente_id');

    if (!btnAgregarProducto || !formVenta || !clienteIdInput) return;

    // Scanner
    const scannerInput = asegurarScannerInput();
    if (scannerInput) {
      scannerInput.focus();

      scannerInput.addEventListener('keydown', function(e) {
        if ((e.key === 'Enter' || e.key === 'Tab') && !e.repeat) {
          e.preventDefault();
          if (processingScan) return;

          const code = (scannerInput.value || '').trim();
          if (!code) return;

          processingScan = true;

          fetch('<?= url("/admin/productos/api/buscar_por_scan") ?>', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ q: code })
          })
          .then(r => r.json())
          .then(data => {
            if (data && data.success && data.producto) {
              const ok = agregarProductoPOS(data.producto, code, 1);
              if (ok) {
                renderizarTabla();
                showToast('success', 'Producto agregado');
              } else {
                showToast('warning', 'No se pudo agregar');
              }
            } else {
              showToast('error', (data && data.message) ? data.message : 'No encontrado');
            }
          })
          .catch(() => showToast('error', 'Error de red'))
          .finally(() => {
            scannerInput.value = '';
            scannerInput.focus();
            setTimeout(() => { processingScan = false; }, 120);
          });
        }
      });
    }

    // Agregar manual
    btnAgregarProducto.addEventListener('click', function() {
      if (!productoSeleccionado) {
        showToast('warning', 'Seleccione un producto primero');
        return;
      }

      const cantidad = parseInt((inputCantidad && inputCantidad.value) ? inputCantidad.value : '1', 10);
      if (!cantidad || cantidad <= 0) {
        showToast('warning', 'Cantidad inválida');
        return;
      }

      const ok = agregarProductoPOS(productoSeleccionado, '', cantidad);
      if (ok) {
        productoSeleccionado = null;
        if (buscarProductoInput) buscarProductoInput.value = '';
        if (inputCantidad) inputCantidad.value = '1';
        renderizarTabla();
        showToast('success', 'Producto agregado');
      }
    });

    // Validación submit
    formVenta.addEventListener('submit', function(e) {
      if (productosSeleccionados.length === 0) {
        e.preventDefault();
        showToast('warning', 'Debe agregar al menos un producto');
        return false;
      }
      if (!clienteIdInput.value) {
        e.preventDefault();
        showToast('warning', 'Debe seleccionar un cliente');
        return false;
      }
    });

    renderizarTabla();
  });
</script>

<style>
  .card { animation: fadeIn 0.3s ease-in; }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  input:focus, select:focus { outline: none; }

  /* --- SOLO FECHA DE LA VENTA --- */
  #fecha_venta {
    background: #fafdff;
    border: 2px solid #e3eafc;
    border-radius: 0.5rem;
    font-size: 1rem;
    color: #0a3d91;
    padding: 0.7rem 1rem;
    box-shadow: 0 1px 2px rgba(10, 61, 145, 0.03);
    transition: border-color 0.2s, box-shadow 0.2s;
    margin-left: 1rem;
    max-width: 220px;
    margin-bottom: 0.5rem;
  }
  #fecha_venta:focus {
    border-color: #0a3d91;
    box-shadow: 0 0 0 2px rgba(10, 61, 145, 0.10);
    background: #f0f6ff;
  }
</style>
