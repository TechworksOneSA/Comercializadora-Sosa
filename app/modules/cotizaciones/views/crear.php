<?php
$series_existentes = $series_existentes ?? [];
?>
<div class="card" style="max-width: 1200px; margin: 0 auto;">
  <!-- HEADER -->
  <div class="card-header" style="background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); padding: 2rem;">
    <h1 class="card-title" style="color: white; font-size: 1.75rem; font-weight: 700; margin: 0;">
      📝 Nueva Cotización
    </h1>
    <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
      Crea una cotización seleccionando cliente y productos
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
  <form method="POST" action="<?= url('/admin/cotizaciones/guardar') ?>" id="formCotizacion" style="padding: 2rem;">

    <!-- SECCIÓN: DATOS GENERALES -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📋 Datos Generales</h3>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">

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
              oninput="buscarClientes()"
            >
            <input type="hidden" name="cliente_id" id="cliente_id" required>
            <div id="resultadosClientes" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 300px; overflow-y: auto; background: white; border: 2px solid #0a3d91; border-top: none; border-radius: 0 0 0.5rem 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;"></div>
          </div>
        </div>

        <!-- DÍAS DE VALIDEZ -->
        <div>
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem;">
            Días de Validez <span style="color: #dc3545;">*</span>
          </label>
          <input
            type="number"
            name="dias_validez"
            id="dias_validez"
            value="<?= e($old['dias_validez'] ?? '7') ?>"
            min="1"
            max="30"
            required
            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem;"
            onfocus="this.style.borderColor='#0a3d91'"
            onblur="this.style.borderColor='#e9ecef'"
          >
          <small style="display: block; margin-top: 0.5rem; color: #6c757d; font-size: 0.85rem;">
            La cotización vencerá en X días
          </small>
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
              onfocus="this.style.borderColor='#0a3d91'; mostrarResultadosProductos()"
              onblur="setTimeout(() => ocultarResultadosProductos(), 200)"
              oninput="buscarProductos()"
            >
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
            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem;"
          >
        </div>

        <button
          type="button"
          id="btnAgregarProducto"
          style="padding: 0.75rem 1.5rem; background: #28a745; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; font-size: 0.95rem;"
        >
          ➕ Agregar
        </button>
      </div>
    </div>

    <!-- TABLA DE PRODUCTOS SELECCIONADOS -->
    <div style="margin-bottom: 2rem;">
      <h3 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 700;">📦 Productos en la Cotización</h3>

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
        href="<?= url('/admin/cotizaciones') ?>"
        style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;"
      >
        ← Cancelar
      </a>
      <button
        type="submit"
        id="btnGuardar"
        style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(10, 61, 145, 0.3);"
      >
        💾 Guardar Cotización
      </button>
    </div>

  </form>
</div>

<script>
// Datos PHP a JavaScript
const clientesData = <?= json_encode($clientes) ?>;
const productosData = <?= json_encode(array_map(function($p) use ($series_existentes) {
  $data = [
    'id' => (int)$p['id'],
    'nombre' => $p['nombre'],
    'sku' => $p['sku'] ?? '',
    'codigo_barra' => $p['codigo_barra'] ?? '',
    'stock' => (int)($p['stock'] ?? 0),
    'precio' => (float)($p['precio_venta'] ?? 0),
    'numero_serie' => '',
  ];
  if (isset($series_existentes[$p['id']])) {
    $data['numero_serie'] = $series_existentes[$p['id']];
  }
  return $data;
}, $productos)) ?>;

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
const subtotalDisplay = document.getElementById('subtotalDisplay');
const totalDisplay = document.getElementById('totalDisplay');
const formCotizacion = document.getElementById('formCotizacion');

// ===== BÚSQUEDA DE CLIENTES =====
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

// ===== BÚSQUEDA DE PRODUCTOS =====
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
    const codigo = (producto.codigo_barra || '').toLowerCase();
    const serie = (producto.numero_serie || '').toLowerCase();
    return nombre.includes(termino) || sku.includes(termino) || codigo.includes(termino) || serie.includes(termino);
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

// ===== AGREGAR PRODUCTO =====
btnAgregarProducto.addEventListener('click', function() {
  if (!productoSeleccionado) {
    alert('Selecciona un producto primero');
    return;
  }

  const productoId = productoSeleccionado.id.toString();
  const cantidad = parseInt(inputCantidad.value);

  if (cantidad <= 0) {
    alert('La cantidad debe ser mayor a 0');
    return;
  }

  const nombre = productoSeleccionado.nombre;
  const precio = parseFloat(productoSeleccionado.precio_venta);
  const stock = parseInt(productoSeleccionado.stock);

  // Validar stock
  if (cantidad > stock) {
    alert(`Stock insuficiente para ${nombre}. Disponible: ${stock}`);
    return;
  }

  // Verificar si ya existe
  const existe = productosSeleccionados.find(p => p.id === productoId);
  if (existe) {
    alert('Este producto ya está en la lista');
    return;
  }

  // Agregar a la lista
  productosSeleccionados.push({
    id: productoId,
    nombre: nombre,
    precio: precio,
    cantidad: cantidad,
    stock: stock
  });

  // Resetear
  productoSeleccionado = null;
  buscarProductoInput.value = '';
  inputCantidad.value = '1';

  // Actualizar tabla
  renderizarTabla();
});

// Renderizar tabla de productos
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
    const subtotalLinea = prod.cantidad * prod.precio;

    html += `
      <tr style="border-bottom: 1px solid #e9ecef;">
        <td style="padding: 0.75rem; color: #495057; font-weight: 600;">
          ${prod.nombre}
          <br><small style="color: #6c757d; font-weight: 400;">Stock disponible: ${prod.stock}</small>
        </td>
        <td style="padding: 0.75rem; text-align: center;">
          <input
            type="number"
            class="cantidad-input"
            data-index="${index}"
            value="${prod.cantidad}"
            min="1"
            max="${prod.stock}"
            style="width: 80px; padding: 0.5rem; border: 2px solid #e9ecef; border-radius: 0.375rem; text-align: center;"
          >
        </td>
        <td style="padding: 0.75rem; text-align: right; color: #6c757d;">
          Q ${prod.precio.toFixed(2)}
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

    // Inputs ocultos para el formulario
    inputsHtml += `
      <input type="hidden" name="producto_id[]" value="${prod.id}">
      <input type="hidden" name="cantidad[]" class="hidden-cantidad-${index}" value="${prod.cantidad}">
    `;
  });

  tablaProductos.innerHTML = html;
  inputsProductos.innerHTML = inputsHtml;

  // Event listeners para cambio de cantidad
  document.querySelectorAll('.cantidad-input').forEach(input => {
    input.addEventListener('change', function() {
      const index = parseInt(this.dataset.index);
      let cantidad = parseInt(this.value);
      const prod = productosSeleccionados[index];

      if (cantidad <= 0) {
        cantidad = 1;
        this.value = 1;
      }

      if (cantidad > prod.stock) {
        alert(`Stock máximo: ${prod.stock}`);
        cantidad = prod.stock;
        this.value = prod.stock;
      }

      productosSeleccionados[index].cantidad = cantidad;

      // Actualizar input oculto
      const hiddenInput = document.querySelector(`.hidden-cantidad-${index}`);
      if (hiddenInput) {
        hiddenInput.value = cantidad;
      }

      renderizarTabla();
    });
  });

  // Event listeners para quitar producto
  document.querySelectorAll('.btn-quitar').forEach(btn => {
    btn.addEventListener('click', function() {
      const index = parseInt(this.dataset.index);
      productosSeleccionados.splice(index, 1);
      renderizarTabla();
    });
  });

  calcularTotales();
}

// Calcular totales
function calcularTotales() {
  let subtotal = 0;

  productosSeleccionados.forEach(prod => {
    subtotal += prod.cantidad * prod.precio;
  });

  const total = subtotal; // Sin descuentos ni impuestos por ahora

  subtotalDisplay.textContent = subtotal.toFixed(2);
  totalDisplay.textContent = total.toFixed(2);
}

// Validar formulario antes de enviar
formCotizacion.addEventListener('submit', function(e) {
  if (productosSeleccionados.length === 0) {
    e.preventDefault();
    alert('Debes agregar al menos un producto a la cotización');
    return false;
  }
});

// Inicializar
renderizarTabla();
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

  input:focus, select:focus {
    outline: none;
  }
</style>
