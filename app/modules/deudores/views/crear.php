<script>
// Datos PHP a JavaScript
const clientesData  = <?= json_encode($clientes) ?>;
const productosData = <?= json_encode($productos) ?>;

// Estado
let productosSeleccionados = [];
let productoSeleccionado = null;

// Elementos del DOM
const buscarClienteInput = document.getElementById('buscarCliente');
const clienteIdInput = document.getElementById('cliente_id');
const resultadosClientesDiv = document.getElementById('resultadosClientes');

const buscarProductoInput = document.getElementById('buscarProducto');
const resultadosProductosDiv = document.getElementById('resultadosProductos');
const inputCantidad = document.getElementById('inputCantidad');
const btnAgregarProducto = document.getElementById('btnAgregarProducto');
const tablaProductos = document.getElementById('tablaProductos');
const inputsProductos = document.getElementById('inputsProductos');
const totalDisplay = document.getElementById('totalDisplay');
const formDeuda = document.getElementById('formDeuda');

/* =========================================================
 * Toast simple (porque aquí no existía showToast)
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
  }, 1800);
}

/* =========================================================
 * Modo Supermercado (Scanner)
 * ========================================================= */
let processingScan = false;

function asegurarScannerInput() {
  // Busca el h3 por texto y agrega el input scanner justo debajo del título
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
  // Normalizaciones
  const requiereSerie = parseInt(producto.requiere_serie || 0, 10) === 1;
  const stock = parseInt(producto.stock || 0, 10);
  const precio = parseFloat(producto.precio_venta || producto.precio || 0);

  if (stock <= 0) {
    showToast('warning', 'Sin stock');
    return false;
  }

  if (requiereSerie) {
    // En serie: cada escaneo agrega una línea, no suma cantidades
    const yaExisteSerie = productosSeleccionados.some(p => (p.numero_serie || '') === q);
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
      numero_serie: q,
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

window.addEventListener('DOMContentLoaded', function() {
  const scannerInput = asegurarScannerInput();
  if (!scannerInput) return;

  // Focus inicial (experiencia tipo caja)
  scannerInput.focus();

  scannerInput.addEventListener('keydown', function(e) {
    if ((e.key === 'Enter' || e.key === 'Tab') && !e.repeat) {
      e.preventDefault();
      if (processingScan) return;

      const q = (scannerInput.value || '').trim();
      if (!q) return;

      processingScan = true;

      fetch('/admin/productos/api/buscar_por_scan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ q })
      })
      .then(res => res.json())
      .then(data => {
        if (data && data.success && data.producto) {
          const ok = agregarProductoPOS(data.producto, q, 1);
          if (ok) {
            renderizarTabla();
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
        setTimeout(() => { processingScan = false; }, 120);
      });
    }
  });
});

/* =========================================================
 * BÚSQUEDA DE CLIENTES
 * ========================================================= */
function buscarClientes() {
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
  clienteIdInput.value = id;
  buscarClienteInput.value = nombre;
  resultadosClientesDiv.style.display = 'none';
}

function mostrarResultadosClientes() {
  if (buscarClienteInput.value) {
    buscarClientes();
  } else {
    resultadosClientesDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
    resultadosClientesDiv.style.display = 'block';
  }
}

function ocultarResultadosClientes() {
  resultadosClientesDiv.style.display = 'none';
}

/* =========================================================
 * BÚSQUEDA DE PRODUCTOS
 * ========================================================= */
function buscarProductos() {
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
  buscarProductoInput.value = producto.nombre;
  resultadosProductosDiv.style.display = 'none';
  inputCantidad.focus();
}

function mostrarResultadosProductos() {
  if (buscarProductoInput.value) {
    buscarProductos();
  } else {
    resultadosProductosDiv.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">Escribe para buscar...</div>';
    resultadosProductosDiv.style.display = 'block';
  }
}

function ocultarResultadosProductos() {
  resultadosProductosDiv.style.display = 'none';
}

/* =========================================================
 * AGREGAR PRODUCTO (Manual) - ahora también suma como “supermercado”
 * ========================================================= */
btnAgregarProducto.addEventListener('click', function() {
  if (!productoSeleccionado) {
    showToast('warning', 'Seleccione un producto primero');
    return;
  }

  const cantidad = parseInt(inputCantidad.value, 10);
  if (!cantidad || cantidad <= 0) {
    showToast('warning', 'Cantidad inválida');
    return;
  }

  const ok = agregarProductoPOS(productoSeleccionado, '', cantidad);
  if (ok) {
    productoSeleccionado = null;
    buscarProductoInput.value = '';
    inputCantidad.value = '1';
    renderizarTabla();
    showToast('success', 'Producto agregado');
  }
});

/* =========================================================
 * Renderizar tabla de productos
 * ========================================================= */
function renderizarTabla() {
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

    // Inputs ocultos para el formulario (alineados por índice)
    inputsHtml += `
      <input type="hidden" name="producto_id[]" value="${String(prod.id)}">
      <input type="hidden" name="cantidad[]" class="hidden-cantidad-${index}" value="${requiereSerie ? 1 : (parseInt(prod.cantidad,10)||1)}">
      <input type="hidden" name="numero_serie[]" value="${requiereSerie ? String(prod.numero_serie || '') : ''}">
    `;
  });

  tablaProductos.innerHTML = html;
  inputsProductos.innerHTML = inputsHtml;

  // Event listeners para cambio de cantidad (solo si NO es serie)
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

      // Actualizar input oculto
      const hiddenInput = document.querySelector(`.hidden-cantidad-${index}`);
      if (hiddenInput) hiddenInput.value = cantidad;

      renderizarTabla();
    });
  });

  // Event listeners para quitar producto
  document.querySelectorAll('.btn-quitar').forEach(btn => {
    btn.addEventListener('click', function() {
      const index = parseInt(this.dataset.index, 10);
      productosSeleccionados.splice(index, 1);
      renderizarTabla();
    });
  });

  calcularTotales();
}

// Calcular totales
function calcularTotales() {
  let total = 0;
  productosSeleccionados.forEach(prod => {
    total += (parseInt(prod.cantidad, 10) || 0) * (parseFloat(prod.precio) || 0);
  });
  totalDisplay.textContent = total.toFixed(2);
}

// Validar formulario antes de enviar
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

// Inicializar
renderizarTabla();
</script>
