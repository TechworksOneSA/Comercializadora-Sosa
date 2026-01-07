<?php
$productos = $productos ?? [];
?>

<link rel="stylesheet" href="<?= url('/assets/css/inventario_avanzado.css') ?>">

<div class="invadv-wrap">
  <div class="invadv-header">
    <h1 class="invadv-title">📦 Inventario Avanzado (Kardex)</h1>
    <p class="invadv-sub">Movimientos por producto: entradas/salidas/ajustes, con saldo acumulado y auditoría.</p>
  </div>

  <div class="invadv-body">
    <div class="invadv-grid">
      <div>
        <label class="invadv-label">Producto</label>
        <input type="hidden" id="productoId" value="">
        <div class="fbselect" id="fbProducto">
          <input
            type="text"
            id="productoTxt"
            class="invadv-select"
            placeholder="Buscar producto por SKU o nombre..."
            autocomplete="off"
          >
          <div class="fbselect-menu" id="fbProductoMenu"></div>
        </div>
      </div>

      <div>
        <label class="invadv-label">Desde</label>
        <input id="desde" type="date" class="invadv-input">
      </div>

      <div>
        <label class="invadv-label">Hasta</label>
        <input id="hasta" type="date" class="invadv-input">
      </div>

      <div class="invadv-actions">
        <button class="invbtn invbtn-primary" id="btnBuscar" type="button">🔎 Consultar</button>
        <button class="invbtn invbtn-ghost" id="btnLimpiar" type="button">🧹 Limpiar</button>
      </div>
    </div>

    <div class="invadv-kpis">
      <div class="invkpi invkpi-stock"><small>Stock actual</small><strong id="kpiStock">—</strong></div>
      <div class="invkpi invkpi-entrada"><small>Entradas</small><strong id="kpiEntradas">—</strong></div>
      <div class="invkpi invkpi-salida"><small>Salidas</small><strong id="kpiSalidas">—</strong></div>
      <div class="invkpi invkpi-saldo"><small>Saldo (rango)</small><strong id="kpiSaldo">—</strong></div>
    </div>

    <div class="invadv-tablewrap" style="margin-top: 1.25rem;">
      <table class="invadv-table">
        <thead>
          <tr>
            <th style="width: 170px;">Fecha</th>
            <th style="width: 120px;">Tipo</th>
            <th style="width: 110px; text-align:right;">Cantidad</th>
            <th style="width: 140px; text-align:right;">Costo Unit.</th>
            <th style="width: 140px; text-align:right;">Valor</th>
            <th style="width: 140px; text-align:right;">Saldo</th>
            <th style="width: 130px;">Origen</th>
            <th>Detalle</th>
            <th style="width: 140px;">Usuario</th>
          </tr>
        </thead>
        <tbody id="tbodyKardex">
          <tr><td colspan="9" class="invadv-empty">Seleccione un producto y consulte.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const $ = (id) => document.getElementById(id);

const productoId = $("productoId");
const productoTxt = $("productoTxt");
const fbProductoMenu = $("fbProductoMenu");
const desde = $("desde");
const hasta = $("hasta");
const btnBuscar = $("btnBuscar");
const btnLimpiar = $("btnLimpiar");
const tbody = $("tbodyKardex");

const kpiStock = $("kpiStock");
const kpiEntradas = $("kpiEntradas");
const kpiSalidas = $("kpiSalidas");
const kpiSaldo = $("kpiSaldo");

// Productos en memoria
const productosData = <?= json_encode(array_map(function($p){
  return [
    'id' => $p['id'],
    'sku' => $p['sku'] ?? 'N/A',
    'nombre' => $p['nombre'] ?? ''
  ];
}, $productos), JSON_UNESCAPED_UNICODE) ?>;

console.log("Total productos cargados:", productosData.length);

// === FUNCIONES AUXILIARES ===
function moneyQ(n){
  const num = Number(n || 0);
  return "Q " + num.toFixed(2);
}

function fmt(n){
  return Number(n || 0).toFixed(2);
}

function normalize(s) {
  return (s || "")
    .toString()
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .trim();
}

// === BUSCADOR TIPO FACEBOOK ===
function renderProductoMenu() {
  const q = normalize(productoTxt.value);

  const filtered = productosData
    .filter(p => {
      if (!q) return true;
      return normalize(p.sku).includes(q) || normalize(p.nombre).includes(q);
    })
    .slice(0, 30);

  console.log("Productos filtrados:", filtered.length, "Búsqueda:", q);

  fbProductoMenu.innerHTML = "";

  if (filtered.length === 0) {
    const noResult = document.createElement("div");
    noResult.className = "fbselect-empty";
    noResult.textContent = "No se encontraron productos";
    fbProductoMenu.appendChild(noResult);
    fbProductoMenu.classList.add("open");
    return;
  }

  filtered.forEach(p => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "fbselect-item";
    btn.innerHTML = `<strong>${p.sku}</strong> — ${p.nombre}`;

    btn.addEventListener("mousedown", (e) => {
      e.preventDefault();
      productoId.value = p.id;
      productoTxt.value = `${p.sku} — ${p.nombre}`;
      fbProductoMenu.classList.remove("open");
    });

    fbProductoMenu.appendChild(btn);
  });

  fbProductoMenu.classList.add("open");
}

productoTxt.addEventListener("focus", renderProductoMenu);
productoTxt.addEventListener("click", renderProductoMenu);
productoTxt.addEventListener("input", renderProductoMenu);

productoTxt.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    fbProductoMenu.classList.remove("open");
    productoTxt.blur();
  }
});

document.addEventListener("mousedown", (e) => {
  if (!e.target.closest("#fbProducto")) {
    fbProductoMenu.classList.remove("open");
  }
});

productoTxt.addEventListener("blur", () => {
  setTimeout(() => fbProductoMenu.classList.remove("open"), 150);
});

function badgeTipo(tipo){
  if (tipo === "ENTRADA") return `<span class="badge badge-in">✅ ENTRADA</span>`;
  if (tipo === "SALIDA") return `<span class="badge badge-out">❌ SALIDA</span>`;
  return `<span class="badge badge-adj">🟡 AJUSTE</span>`;
}

function renderEmpty(msg){
  tbody.innerHTML = `<tr><td colspan="9" class="invadv-empty">${msg}</td></tr>`;
}

async function cargarKardex(){
  const pid = productoId.value;
  if (!pid){
    renderEmpty("Seleccione un producto.");
    return;
  }

  const params = new URLSearchParams();
  params.set("producto_id", pid);
  if (desde.value) params.set("desde", desde.value);
  if (hasta.value) params.set("hasta", hasta.value);

  btnBuscar.disabled = true;
  btnBuscar.textContent = "⏳ Consultando...";

  try{
    const res = await fetch(`<?= url('/admin/inventario-avanzado/kardex') ?>?` + params.toString(), {
      headers: { "Accept": "application/json" }
    });

    if (!res.ok){
      renderEmpty("No se pudo consultar el kardex.");
      return;
    }

    const data = await res.json();

    console.log("Respuesta del servidor:", data);

    // KPIs
    kpiStock.textContent = Number(data.stock_actual ?? 0).toFixed(2);
    kpiEntradas.textContent = "+" + fmt(data.totales?.entradas ?? 0);
    kpiSalidas.textContent = "-" + fmt(data.totales?.salidas ?? 0);
    kpiSaldo.textContent = fmt(data.totales?.saldo_rango ?? 0);

    console.log("KPIs actualizados:", {
      stock: kpiStock.textContent,
      entradas: kpiEntradas.textContent,
      salidas: kpiSalidas.textContent,
      saldo: kpiSaldo.textContent
    });

    const rows = data.movimientos || [];
    if (!rows.length){
      renderEmpty("Sin movimientos para el filtro seleccionado.");
      return;
    }

    tbody.innerHTML = rows.map(m => {
      const cantidad = Number(m.cantidad || 0);
      const costo = Number(m.costo_unitario || 0);
      const valor = Number(m.valor || 0);
      const saldo = Number(m.saldo || 0);

      const origen = `${m.origen ?? ""} #${m.origen_id ?? ""}`.trim();
      const detalle = (m.motivo || "").trim();

      return `
        <tr>
          <td>${m.fecha ?? ""}</td>
          <td>${badgeTipo(m.tipo)}</td>
          <td style="text-align:right; font-weight:800;">${fmt(cantidad)}</td>
          <td style="text-align:right;">${moneyQ(costo)}</td>
          <td style="text-align:right; font-weight:800; color:#0a3d91;">${moneyQ(valor)}</td>
          <td style="text-align:right; font-weight:900; color:#28a745;">${fmt(saldo)}</td>
          <td>${origen}</td>
          <td>${detalle || "—"}</td>
          <td>${m.usuario ?? "—"}</td>
        </tr>
      `;
    }).join("");

  }catch(e){
    console.error("Error consultando kardex:", e);
    renderEmpty("Error consultando kardex: " + e.message);
  }finally{
    btnBuscar.disabled = false;
    btnBuscar.textContent = "🔎 Consultar";
  }
}

btnBuscar.addEventListener("click", cargarKardex);

btnLimpiar.addEventListener("click", () => {
  productoTxt.value = "";
  productoTxt.placeholder = "Buscar producto por SKU o nombre...";
  productoId.value = "";
  desde.value = "";
  hasta.value = "";
  kpiStock.textContent = "—";
  kpiEntradas.textContent = "—";
  kpiSalidas.textContent = "—";
  kpiSaldo.textContent = "—";
  renderEmpty("Seleccione un producto y consulte.");
});
</script>
