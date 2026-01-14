// --- Modo Supermercado (Scanner) ---
let processingScan = false;
// Busca el h3 por texto y agrega el input scanner arriba
window.addEventListener('DOMContentLoaded', function() {
  const h3s = document.querySelectorAll('h3');
  let productosSection = null;
  h3s.forEach(h3 => {
    if (h3.textContent.includes('🛒 Seleccionar Productos')) {
      productosSection = h3.parentNode;
    }
  });
  if (productosSection) {
    const scannerInput = document.createElement('input');
    scannerInput.type = 'text';
    scannerInput.id = 'productoScanner';
    scannerInput.placeholder = 'Escanear serie / código de barra / SKU y presione Enter';
    scannerInput.style = 'width: 100%; padding: 0.75rem 1rem; border: 2px solid #dc3545; border-radius: 0.5rem; font-size: 0.95rem; margin-bottom: 1rem;';
    scannerInput.autocomplete = 'off';
    productosSection.insertBefore(scannerInput, productosSection.firstChild);

    scannerInput.addEventListener('keydown', function(e) {
      if ((e.key === 'Enter' || e.key === 'Tab') && !e.repeat) {
        e.preventDefault();
        if (processingScan) return;
        processingScan = true;
        const q = scannerInput.value.trim();
        if (!q) {
          processingScan = false;
          return;
        }
        fetch('/admin/productos/api/buscar_por_scan', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ q })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success && data.producto) {
            if (parseInt(data.producto.requiere_serie) === 1) {
              const yaExisteSerie = productosSeleccionados.some(
                p => p.numero_serie && p.numero_serie === q
              );
              if (yaExisteSerie) {
                showToast('warning', 'Serie ya agregada');
                return;
              }
              if (parseInt(data.producto.stock) <= 0) {
                showToast('warning', 'Sin stock');
                return;
              }
              productosSeleccionados.push({
                id: data.producto.id,
                nombre: data.producto.nombre,
                precio: parseFloat(data.producto.precio_venta),
                cantidad: 1,
                stock: data.producto.stock,
                numero_serie: q,
                requiere_serie: 1
              });
            } else {
              let existe = productosSeleccionados.find(
                p => p.id == data.producto.id && (!p.requiere_serie || p.requiere_serie == 0)
              );
              if (existe) {
                if (existe.cantidad + 1 > data.producto.stock) {
                  showToast('warning', 'Sin stock');
                  return;
                }
                existe.cantidad += 1;
              } else {
                if (parseInt(data.producto.stock) <= 0) {
                  showToast('warning', 'Sin stock');
                  return;
                }
                productosSeleccionados.push({
                  id: data.producto.id,
                  nombre: data.producto.nombre,
                  precio: parseFloat(data.producto.precio_venta),
                  cantidad: 1,
                  stock: data.producto.stock,
                  numero_serie: '',
                  requiere_serie: 0
                });
              }
            }
            renderizarTabla();
            showToast('success', 'Producto agregado');
          } else {
            showToast('error', data.message || 'No encontrado');
          }
        })
        .catch(() => showToast('error', 'Error de red'))
        .finally(() => {
          scannerInput.value = '';
          scannerInput.focus();
          setTimeout(() => { processingScan = false; }, 150);
        });
      }
    });
  }
});
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
              oninput="buscarClientes()"
            >
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
            placeholder="Notas o detalles adicionales..."
          ></textarea>
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
              oninput="buscarProductos()"
            >
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
        style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;"
      >
        ← Cancelar
      </a>
      <button 
        type="submit"
        id="btnGuardar"
        style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(220, 53, 69, 0.3);"
      >
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

    // Inputs ocultos para el formulario (siempre los 3)
    inputsHtml += `
      <input type="hidden" name="producto_id[]" value="${prod.id}">
      <input type="hidden" name="cantidad[]" class="hidden-cantidad-${index}" value="${prod.cantidad}">
      <input type="hidden" name="numero_serie[]" value="${prod.numero_serie ? prod.numero_serie : ''}">
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
  let total = 0;

  productosSeleccionados.forEach(prod => {
    total += prod.cantidad * prod.precio;
  });

  totalDisplay.textContent = total.toFixed(2);
}

// Validar formulario antes de enviar
formDeuda.addEventListener('submit', function(e) {
  if (productosSeleccionados.length === 0) {
    e.preventDefault();
    alert('Debes agregar al menos un producto a la deuda');
    return false;
  }
  
  if (!clienteIdInput.value) {
    e.preventDefault();
    alert('Debes seleccionar un cliente');
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
