<?php
// recibe: $clientes, $productos, $errors
?>
<div class="card" style="max-width: 1200px; margin: 0 auto;">
  <!-- HEADER -->
  <div class="card-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 2rem;">
    <h1 class="card-title" style="color: white; font-size: 1.75rem; font-weight: 700; margin: 0;">
      🧾 Nueva Deuda
    </h1>
    <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
      Registra una deuda seleccionando cliente y productos
    </p>
  </div>

  <!-- ERRORES -->
  <?php if (!empty($errors)): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
      <strong>⚠️ Errores:</strong>
      <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- FORMULARIO -->
  <form method="POST" action="<?= url('/admin/deudores/guardar') ?>" id="formDeuda" style="padding: 2rem;">

    <!-- SECCIÓN: DATOS GENERALES -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📋 Datos Generales</h3>

      <div>
        <!-- CLIENTE -->
        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
            Cliente <span style="color: #dc3545;">*</span>
          </label>
          <div style="position: relative;">
            <input
              type="text"
              id="buscarCliente"
              placeholder="🔍 Buscar por nombre, NIT o teléfono..."
              autocomplete="off"
              style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; background: white;"
              onfocus="this.style.borderColor='#dc3545'; mostrarResultadosClientes()"
              onblur="setTimeout(() => ocultarResultadosClientes(), 200)"
              oninput="buscarClientes()">
            <input type="hidden" name="cliente_id" id="cliente_id" required>
            <div id="resultadosClientes" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 300px; overflow-y: auto; background: white; border: 2px solid #dc3545; border-top: none; border-radius: 0 0 0.5rem 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;"></div>
          </div>
        </div>

        <!-- DESCRIPCIÓN -->
        <div>
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">Descripción (opcional)</label>
          <textarea
            name="descripcion"
            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; min-height: 80px;"
            placeholder="Notas o detalles adicionales..."></textarea>
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
              placeholder="🔍 Buscar por nombre o código de barras..."
              autocomplete="off"
              style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; background: white;"
              onfocus="this.style.borderColor='#dc3545'; mostrarResultadosProductos()"
              onblur="setTimeout(() => ocultarResultadosProductos(), 200)"
              oninput="buscarProductos()">
            <div id="resultadosProductos" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 300px; overflow-y: auto; background: white; border: 2px solid #dc3545; border-top: none; border-radius: 0 0 0.5rem 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;"></div>
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
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📦 Productos en la Deuda</h3>

      <div style="border: 2px solid #e9ecef; border-radius: 0.5rem; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
          <thead style="background: #f8f9fa;">
            <tr>
              <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #495057;">Producto</th>
              <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #495057; width: 120px;">Cantidad</th>
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
          <div style="font-size: 1.5rem; font-weight: 700; color: #dc3545;">
            Total Deuda: Q <span id="totalDisplay">0.00</span>
          </div>
        </div>
      </div>
    </div>

    <!-- INPUTS OCULTOS PARA PRODUCTOS -->
    <div id="inputsProductos"></div>

    <!-- BOTONES -->
    <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 2rem; border-top: 2px solid #e9ecef;">
      <a
        href="<?= url('/admin/deudores') ?>"
        style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
        ← Cancelar
      </a>
      <button
        type="submit"
        id="btnGuardar"
        style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(220, 53, 69, 0.3);">
        💾 Guardar Deuda
      </button>
    </div>

  </form>
</div>

<script>
  // Datos PHP a JavaScript
  const clientesData = <?= json_encode($clientes) ?>;
  const productosData = <?= json_encode($productos) ?>;

  // Estado
  let productosSeleccionados = [];
  let productoSeleccionado = null;

  /* =========================================================
   * Toast simple (showToast)
   * ========================================================= */
  function showToast(type, message) {
    const colors = {
      success: '#28a745',
      warning: '#ffc107',
      error: '#dc3545',
      info: '#17a2b8'
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
    }, 1800);
  }

  /* =========================================================
   * Modo Supermercado (Scanner)
   * ========================================================= */
  let processingScan = false;

  function asegurarScannerInput() {
    const h3s = document.querySelectorAll('h3');
    let productosH3 = null;

    h3s.forEach(h3 => {
      if ((h3.textContent || '').includes('🛒 Seleccionar Productos')) {
        productosH3 = h3;
      }
    });

    if (!productosH3) return null;

    // Si ya existe, no duplicar
    let scannerInput = document.getElementById('productoScanner');
    if (scannerInput) return scannerInput;

    scannerInput = document.createElement('input');
    scannerInput.type = 'text';
    scannerInput.id = 'productoScanner';
    scannerInput.placeholder = 'Escanear serie / código de barra / SKU y presione Enter';
    scannerInput.autocomplete = 'off';
    scannerInput.style = 'width: 100%; padding: 0.75rem 1rem; border: 2px solid #dc3545; border-radius: 0.5rem; font-size: 0.95rem; margin: 0 0 1rem 0; background: #fff;';

    // Insertar después del h3
    productosH3.insertAdjacentElement('afterend', scannerInput);

    return scannerInput;
  }

  function agregarProductoPOS(producto, q, cantidad = 1) {
    const requiereSerie = parseInt(producto.requiere_serie || 0, 10) === 1;
    const stock = parseInt(producto.stock || 0, 10);
    const precio = parseFloat(producto.precio_venta || producto.precio || 0);

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

      const yaExisteSerie = productosSeleccionados.some(p => (p.numero_serie || '') === serie);
      if (yaExisteSerie) {
        showToast('warning', 'Serie ya agregada');
        return false;
      }

      productosSeleccionados.push({
        id: String(producto.id),
        nombre: producto.nombre,
        precio: precio,
        cantidad: 1,
        stock: stock,
        numero_serie: serie,
        requiere_serie: 1
      });

      return true;
    }

    // Sin serie: sumar cantidad si ya existe
    const existente = productosSeleccionados.find(p => String(p.id) == String(producto.id) && (!p.requiere_serie || p.requiere_serie == 0));
    if (existente) {
      const nuevaCantidad = (parseInt(existente.cantidad, 10) || 0) + cantidad;
      if (nuevaCantidad > stock) {
        showToast('warning', `Sin stock (máx: ${stock})`);
        return false;
      }
      existente.cantidad = nuevaCantidad;
      return true;
    }

    // Nuevo
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
      numero_serie: '',
      requiere_serie: 0
    });

    return true;
  }

  /* =========================================================
   * Renderizar / Totales
   * ========================================================= */
  function calcularTotales(totalDisplayEl) {
    let total = 0;
    productosSeleccionados.forEach(prod => {
      total += (parseInt(prod.cantidad, 10) || 0) * (parseFloat(prod.precio) || 0);
    });
    if (totalDisplayEl) totalDisplayEl.textContent = total.toFixed(2);
  }

  function renderizarTabla(tablaProductosEl, inputsProductosEl, totalDisplayEl) {
    if (!tablaProductosEl || !inputsProductosEl) return;

    if (productosSeleccionados.length === 0) {
      tablaProductosEl.innerHTML = `
      <tr>
        <td colspan="5" style="padding: 2rem; text-align: center; color: #6c757d;">
          No hay productos agregados. Usa el selector de arriba para agregar.
        </td>
      </tr>
    `;
      inputsProductosEl.innerHTML = '';
      calcularTotales(totalDisplayEl);
      return;
    }

    let html = '';
    let inputsHtml = '';

    productosSeleccionados.forEach((prod, index) => {
      const requiereSerie = parseInt(prod.requiere_serie || 0, 10) === 1;
      const subtotalLinea = (parseInt(prod.cantidad, 10) || 0) * (parseFloat(prod.precio) || 0);

      const serieLabel = requiereSerie && prod.numero_serie ?
        `<br><small style="color:#6c757d;font-weight:600;">Serie: ${String(prod.numero_serie).replace(/</g,'&lt;')}</small>` :
        '';

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
            value="${requiereSerie ? 1 : prod.cantidad}"
            min="1"
            max="${requiereSerie ? 1 : prod.stock}"
            ${requiereSerie ? 'readonly' : ''}
            style="width: 80px; padding: 0.5rem; border: 2px solid #e9ecef; border-radius: 0.375rem; text-align: center; ${requiereSerie ? 'background:#f8f9fa; cursor:not-allowed;' : ''}"
          >
        </td>
        <td style="padding: 0.75rem; text-align: right; color: #6c757d;">
          Q ${Number(prod.precio).toFixed(2)}
        </td>
        <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #dc3545;">
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

      inputsHtml += `
      <input type="hidden" name="producto_id[]" value="${String(prod.id)}">
      <input type="hidden" name="cantidad[]" class="hidden-cantidad-${index}" value="${requiereSerie ? 1 : (parseInt(prod.cantidad,10)||1)}">
      <input type="hidden" name="numero_serie[]" value="${requiereSerie ? String(prod.numero_serie || '') : ''}">
    `;
    });

    tablaProductosEl.innerHTML = html;
    inputsProductosEl.innerHTML = inputsHtml;

    // Cantidad (solo no-serie)
    document.querySelectorAll('.cantidad-input').forEach(input => {
      input.addEventListener('change', function() {
        const index = parseInt(this.dataset.index, 10);
        const prod = productosSeleccionados[index];
        const requiereSerie = parseInt(prod.requiere_serie || 0, 10) === 1;

        if (requiereSerie) {
          this.value = 1;
          return;
        }

        let cantidad = parseInt(this.value, 10);
        if (!cantidad || cantidad <= 0) {
          cantidad = 1;
          this.value = 1;
        }

        if (cantidad > parseInt(prod.stock, 10)) {
          showToast('warning', `Stock máximo: ${prod.stock}`);
          cantidad = parseInt(prod.stock, 10);
          this.value = cantidad;
        }

        productosSeleccionados[index].cantidad = cantidad;

        const hiddenInput = document.querySelector(`.hidden-cantidad-${index}`);
        if (hiddenInput) hiddenInput.value = cantidad;

        renderizarTabla(tablaProductosEl, inputsProductosEl, totalDisplayEl);
      });
    });

    // Quitar
    document.querySelectorAll('.btn-quitar').forEach(btn => {
      btn.addEventListener('click', function() {
        const index = parseInt(this.dataset.index, 10);
        productosSeleccionados.splice(index, 1);
        renderizarTabla(tablaProductosEl, inputsProductosEl, totalDisplayEl);
      });
    });

    calcularTotales(totalDisplayEl);
  }

  /* =========================================================
   * BÚSQUEDA DE CLIENTES
   * ========================================================= */
  function buscarClientes() {
    const buscarClienteInput = document.getElementById('buscarCliente');
    const resultadosClientesDiv = document.getElementById('resultadosClientes');
    if (!buscarClienteInput || !resultadosClientesDiv) return;

    const termino = buscarClienteInput.value.toLowerCase().trim();

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
        <div style="font-size: 0.85rem; color: #6c757d;">NIT: ${cliente.nit || 'N/A'} | 📞 ${cliente.telefono}</div>
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

    if (buscarClienteInput.value) {
      buscarClientes();
    } else {
      resultadosClientesDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
      resultadosClientesDiv.style.display = 'block';
    }
  }

  function ocultarResultadosClientes() {
    const resultadosClientesDiv = document.getElementById('resultadosClientes');
    if (resultadosClientesDiv) resultadosClientesDiv.style.display = 'none';
  }

  /* =========================================================
   * BÚSQUEDA DE PRODUCTOS
   * ========================================================= */
  function buscarProductos() {
    const buscarProductoInput = document.getElementById('buscarProducto');
    const resultadosProductosDiv = document.getElementById('resultadosProductos');
    if (!buscarProductoInput || !resultadosProductosDiv) return;

    const termino = buscarProductoInput.value.toLowerCase().trim();

    if (!termino) {
      resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
      resultadosProductosDiv.style.display = 'block';
      return;
    }

    const resultados = productosData.filter(producto => {
      const nombre = (producto.nombre || '').toLowerCase();
      const sku = (producto.sku || '').toLowerCase();
      return nombre.includes(termino) || sku.includes(termino);
    });

    if (resultados.length === 0) {
      resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">No se encontraron productos</div>';
      resultadosProductosDiv.style.display = 'block';
      return;
    }

    let html = '';
    resultados.forEach(producto => {
      const stockColor = producto.stock > 10 ? '#28a745' : (producto.stock > 0 ? '#ffc107' : '#dc3545');
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
            <div style="font-size: 0.85rem; color: #6c757d;">SKU: ${producto.sku || 'N/A'} | Q ${parseFloat(producto.precio_venta).toFixed(2)}</div>
          </div>
          <div style="font-weight: 700; color: ${stockColor}; font-size: 0.9rem;">
            Stock: ${producto.stock}
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

    if (buscarProductoInput.value) {
      buscarProductos();
    } else {
      resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
      resultadosProductosDiv.style.display = 'block';
    }
  }

  function ocultarResultadosProductos() {
    const resultadosProductosDiv = document.getElementById('resultadosProductos');
    if (resultadosProductosDiv) resultadosProductosDiv.style.display = 'none';
  }

  /* =========================================================
   * INIT (corre cuando DOM ya existe)
   * ========================================================= */
  window.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM (ahora sí existen)
    const buscarProductoInput = document.getElementById('buscarProducto');
    const inputCantidad = document.getElementById('inputCantidad');
    const btnAgregarProducto = document.getElementById('btnAgregarProducto');
    const tablaProductos = document.getElementById('tablaProductos');
    const inputsProductos = document.getElementById('inputsProductos');
    const totalDisplay = document.getElementById('totalDisplay');
    const formDeuda = document.getElementById('formDeuda');
    const clienteIdInput = document.getElementById('cliente_id');

    // Guard-rails (si algo no existe, no explotamos)
    if (!btnAgregarProducto || !tablaProductos || !inputsProductos || !totalDisplay || !formDeuda || !clienteIdInput) {
      console.error('Faltan elementos del DOM (IDs). Revise: btnAgregarProducto/tablaProductos/inputsProductos/totalDisplay/formDeuda/cliente_id');
      return;
    }

    /* ===== Scanner ===== */
    const scannerInput = asegurarScannerInput();
    if (scannerInput) {
      scannerInput.focus();

      scannerInput.addEventListener('keydown', function(e) {
        if ((e.key === 'Enter' || e.key === 'Tab') && !e.repeat) {
          e.preventDefault();
          if (processingScan) return;

          const serie = (scannerInput.value || '').trim();
          if (!serie) return;

          processingScan = true;

          // Buscar producto por número de serie en productosData (JS, búsqueda local primero)
          let productoEncontrado = null;
          for (const prod of productosData) {
            if (prod.requiere_serie == 1 && prod.series && Array.isArray(prod.series)) {
              if (prod.series.includes(serie)) {
                productoEncontrado = prod;
                break;
              }
            }
          }

          if (productoEncontrado) {
            const ok = agregarProductoPOS(productoEncontrado, serie, 1);
            if (ok) {
              renderizarTabla(tablaProductos, inputsProductos, totalDisplay);
              showToast('success', 'Producto agregado');
            } else {
              showToast('warning', 'No se pudo agregar el producto');
            }
            scannerInput.value = '';
            scannerInput.focus();
            setTimeout(() => { processingScan = false; }, 120);
            return;
          }

          // Si no se encuentra localmente, buscar en backend (acceso directo al archivo PHP)
          fetch('/app/modules/productos/api/buscar_por_scan.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                q: serie
              })
            })
            .then(res => res.json())
            .then(data => {
              if (data && data.success && data.producto) {
                const ok = agregarProductoPOS(data.producto, serie, 1);
                if (ok) {
                  renderizarTabla(tablaProductos, inputsProductos, totalDisplay);
                  showToast('success', 'Producto agregado');
                }
              } else {
                showToast('error', (data && data.message) ? data.message : 'No encontrado');
              }
            })
            .catch(() => showToast('error', 'Error de red'))
            .finally(() => {
              scannerInput.value = '';
              scannerInput.focus();
              setTimeout(() => {
                processingScan = false;
              }, 120);
            });
        }
      });
    }

    /* ===== Agregar manual (botón) ===== */
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
        renderizarTabla(tablaProductos, inputsProductos, totalDisplay);
        showToast('success', 'Producto agregado');
      }
    });

    /* ===== Validación submit ===== */
    formDeuda.addEventListener('submit', function(e) {
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

    // Inicializar tabla
    renderizarTabla(tablaProductos, inputsProductos, totalDisplay);
  });
</script>

<style>
  .card {
    animation: fadeIn 0.3s ease-in;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  input:focus,
  select:focus {
    outline: none;
  }
</style>
