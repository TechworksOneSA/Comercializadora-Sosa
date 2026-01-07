/**
 * Productos/Inventario - JavaScript Module
 * Archivo: public/assets/js/productos.js
 */

(() => {
  // Variables globales del módulo
  let timer = null;
  let controller = null;
  const DEBOUNCE_MS = 220;

  // DOM Elements
  const elements = {
    inputQ: null,
    wrap: null,
    fStock: null,
    fEstado: null,
    fCategoriaHidden: null,
    fMarcaHidden: null,
    fCategoriaTxt: null,
    fMarcaTxt: null,
    fbCatMenu: null,
    fbMarcaMenu: null,
  };

  // Inicialización del módulo
  function init() {
    // Obtener referencias a elementos del DOM
    elements.inputQ = document.getElementById("qLive");
    elements.wrap = document.getElementById("productosTableWrap");
    elements.fStock = document.getElementById("fStock");
    elements.fEstado = document.getElementById("fEstado");
    elements.fCategoriaHidden = document.getElementById("fCategoria");
    elements.fMarcaHidden = document.getElementById("fMarca");
    elements.fCategoriaTxt = document.getElementById("fCategoriaTxt");
    elements.fMarcaTxt = document.getElementById("fMarcaTxt");
    elements.fbCatMenu = document.getElementById("fbCatMenu");
    elements.fbMarcaMenu = document.getElementById("fbMarcaMenu");

    if (!elements.inputQ || !elements.wrap) return;

    // Inicializar filtros
    initFilters();

    // Configurar event listeners
    setupEventListeners();

    // Actualizar info inicial de filtros
    updateFiltersInfo();
  }

  // Utilidades
  function normalize(s) {
    return (s || "")
      .toString()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .trim();
  }

  function openMenu(menuEl) {
    menuEl.classList.add("open");
  }

  function closeMenu(menuEl) {
    menuEl.classList.remove("open");
  }

  function setFBPlaceholder(txtEl, prefix, label) {
    txtEl.placeholder = label;
  }

  // Renderizar menús de filtros
  function renderMenu(menuEl, items, prefix, txtEl, hiddenEl) {
    const q = normalize(txtEl.value);

    const filtered = (items || [])
      .filter((it) => !q || normalize(it.nombre).includes(q))
      .slice(0, 25);

    menuEl.innerHTML = "";

    // Opción "Todas"
    const allBtn = document.createElement("button");
    allBtn.type = "button";
    allBtn.className = "fbselect-item";
    allBtn.textContent = "Todas";
    allBtn.setAttribute("role", "option");
    allBtn.addEventListener("mousedown", (e) => {
      e.preventDefault();
      hiddenEl.value = "0";
      setFBPlaceholder(txtEl, prefix, "Todas");
      txtEl.value = "";
      closeMenu(menuEl);
      schedule();
      updateFiltersInfo();
    });
    menuEl.appendChild(allBtn);

    // Opciones filtradas
    filtered.forEach((it) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "fbselect-item";
      btn.textContent = it.nombre;
      btn.setAttribute("role", "option");

      btn.addEventListener("mousedown", (e) => {
        e.preventDefault();
        hiddenEl.value = String(it.id);
        setFBPlaceholder(txtEl, prefix, it.nombre);
        txtEl.value = "";
        closeMenu(menuEl);
        schedule();
        updateFiltersInfo();
      });

      menuEl.appendChild(btn);
    });

    openMenu(menuEl);
  }

  // Inicializar filtros de selección
  function initFBSelect(prefix, txtEl, hiddenEl, menuEl, dataArr) {
    const currentId = parseInt(hiddenEl.value || "0", 10);
    if (currentId > 0) {
      const found = (dataArr || []).find((x) => parseInt(x.id, 10) === currentId);
      setFBPlaceholder(txtEl, prefix, found ? found.nombre : "Todas");
    } else {
      setFBPlaceholder(txtEl, prefix, "Todas");
    }
    txtEl.value = "";

    txtEl.addEventListener("focus", () => renderMenu(menuEl, dataArr, prefix, txtEl, hiddenEl));
    txtEl.addEventListener("click", () => renderMenu(menuEl, dataArr, prefix, txtEl, hiddenEl));
    txtEl.addEventListener("input", () => renderMenu(menuEl, dataArr, prefix, txtEl, hiddenEl));

    txtEl.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        closeMenu(menuEl);
        txtEl.blur();
      }
    });

    document.addEventListener("mousedown", (e) => {
      if (!txtEl.closest(".fbselect")?.contains(e.target)) {
        closeMenu(menuEl);
      }
    });

    txtEl.addEventListener("blur", () => {
      setTimeout(() => closeMenu(menuEl), 120);
    });
  }

  function initFilters() {
    initFBSelect(
      "Categoría",
      elements.fCategoriaTxt,
      elements.fCategoriaHidden,
      elements.fbCatMenu,
      window.__categorias || []
    );
    initFBSelect(
      "Marca",
      elements.fMarcaTxt,
      elements.fMarcaHidden,
      elements.fbMarcaMenu,
      window.__marcas || []
    );
  }

  // Construir query string para AJAX
  function buildQS() {
    const params = new URLSearchParams();
    params.set("q", (elements.inputQ.value || "").trim());
    params.set("categoria_id", elements.fCategoriaHidden?.value || "0");
    params.set("marca_id", elements.fMarcaHidden?.value || "0");
    params.set("stock", elements.fStock?.value || "all");
    params.set("estado", elements.fEstado?.value || "ALL");
    return params.toString();
  }

  // Cargar tabla via AJAX
  async function fetchTabla() {
    if (controller) controller.abort();
    controller = new AbortController();

    // URL base necesita ser definida desde PHP
    const baseUrl = window.productosTableUrl || "/admin/productos/tabla";
    const url = baseUrl + "?" + buildQS();

    try {
      const res = await fetch(url, {
        method: "GET",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        signal: controller.signal,
      });

      if (!res.ok) throw new Error("HTTP " + res.status);

      const html = await res.text();
      elements.wrap.innerHTML = html;
    } catch (err) {
      if (err.name === "AbortError") return;
      console.error("Error cargando tabla de productos:", err);
      elements.wrap.innerHTML =
        '<div class="error-message">Error cargando productos. Intente nuevamente.</div>';
    }
  }

  // Programar carga de tabla con debounce
  function schedule() {
    clearTimeout(timer);
    timer = setTimeout(() => {
      fetchTabla();
    }, DEBOUNCE_MS);
  }

  // Configurar event listeners
  function setupEventListeners() {
    elements.inputQ.addEventListener("input", () => {
      schedule();
      updateFiltersInfo();
    });

    elements.fStock?.addEventListener("change", () => {
      schedule();
      updateFiltersInfo();
    });

    elements.fEstado?.addEventListener("change", () => {
      schedule();
      updateFiltersInfo();
    });
  }

  // Actualizar información de filtros activos
  function updateFiltersInfo() {
    const infoText = document.getElementById("filtersInfoText");
    if (!infoText) return;

    const activeFilters = [];

    const searchQuery = elements.inputQ.value.trim();
    if (searchQuery) activeFilters.push(`Búsqueda: "${searchQuery}"`);

    const catId = parseInt(elements.fCategoriaHidden?.value || "0", 10);
    if (catId > 0) {
      const cat = (window.__categorias || []).find((x) => parseInt(x.id, 10) === catId);
      if (cat) activeFilters.push(`Categoría: ${cat.nombre}`);
    }

    const marcaId = parseInt(elements.fMarcaHidden?.value || "0", 10);
    if (marcaId > 0) {
      const marca = (window.__marcas || []).find((x) => parseInt(x.id, 10) === marcaId);
      if (marca) activeFilters.push(`Marca: ${marca.nombre}`);
    }

    const stockVal = elements.fStock?.value;
    if (stockVal && stockVal !== "all") {
      const stockLabels = { bajo: "Stock Bajo", cero: "Stock en Cero" };
      activeFilters.push(stockLabels[stockVal] || stockVal);
    }

    const estadoVal = elements.fEstado?.value;
    if (estadoVal && estadoVal !== "ALL") {
      activeFilters.push(`Estado: ${estadoVal === "ACTIVO" ? "Activos" : "Desactivados"}`);
    }

    infoText.textContent =
      activeFilters.length === 0
        ? "Mostrando todos los productos"
        : `Filtros activos: ${activeFilters.join(" • ")}`;
  }

  // Limpiar todos los filtros
  function clearAllFilters() {
    elements.inputQ.value = "";

    elements.fCategoriaHidden.value = "0";
    elements.fCategoriaTxt.value = "";
    elements.fCategoriaTxt.placeholder = "Todas";

    elements.fMarcaHidden.value = "0";
    elements.fMarcaTxt.value = "";
    elements.fMarcaTxt.placeholder = "Todas";

    if (elements.fStock) elements.fStock.value = "all";
    if (elements.fEstado) elements.fEstado.value = "ALL";

    updateFiltersInfo();
    schedule();

    // Animación del botón
    const btn = document.getElementById("btnClearFilters");
    if (btn) {
      btn.style.transform = "rotate(180deg)";
      setTimeout(() => {
        btn.style.transform = "";
      }, 300);
    }
  }

  // API pública del módulo
  window.ProductosModule = {
    init,
    clearAllFilters,
    fetchTabla,
    updateFiltersInfo,
  };

  // Backward compatibility
  window.fetchTablaProductos = fetchTabla;
  window.clearAllFilters = clearAllFilters;

  // Auto-inicialización cuando el DOM esté listo
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
