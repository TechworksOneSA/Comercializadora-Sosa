/**
 * BMModal - Sistema de Modal Universal
 * Archivo: public/assets/js/modal.js
 */

(() => {
  let modal, bodyEl, titleEl;

  function init() {
    modal = document.getElementById("bmModal");
    bodyEl = document.getElementById("bmModalBody");
    titleEl = document.getElementById("bmModalTitle");

    if (!modal || !bodyEl || !titleEl) return;

    setupEventListeners();
  }

  function lockBody(lock) {
    document.documentElement.classList.toggle("bm-modal-lock", !!lock);
    document.body.classList.toggle("bm-modal-lock", !!lock);
  }

  function open() {
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
    lockBody(true);
  }

  function close() {
    modal.classList.remove("open");
    modal.setAttribute("aria-hidden", "true");
    bodyEl.innerHTML = '<div class="bm-modal__loading">Cargando…</div>';
    titleEl.textContent = "Cargando…";
    lockBody(false);
  }

  async function load(url, title) {
    titleEl.textContent = title || "Cargando…";
    bodyEl.innerHTML = '<div class="bm-modal__loading">Cargando…</div>';
    open();

    try {
      const res = await fetch(url, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      if (!res.ok) throw new Error("HTTP " + res.status);

      const html = await res.text();
      bodyEl.innerHTML = html;

      // Ejecutar scripts inline del contenido cargado
      bodyEl.querySelectorAll("script").forEach((old) => {
        const s = document.createElement("script");
        if (old.src) s.src = old.src;
        s.text = old.textContent;
        old.replaceWith(s);
      });
    } catch (err) {
      console.error("Error cargando modal:", err);
      bodyEl.innerHTML =
        '<div class="bm-modal__loading">❌ Error cargando el formulario. Revise consola.</div>';
    }
  }

  function setupEventListeners() {
    modal.addEventListener("click", (e) => {
      const t = e.target;
      if (t && t.getAttribute && t.getAttribute("data-close") === "1") {
        close();
      }
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && modal.classList.contains("open")) {
        close();
      }
    });
  }

  function refreshTabla() {
    if (typeof window.fetchTablaProductos === "function") {
      window.fetchTablaProductos();
    } else if (typeof window.ProductosModule?.fetchTabla === "function") {
      window.ProductosModule.fetchTabla();
    }
  }

  // API pública del modal
  window.BMModal = {
    init,
    open,
    close,
    load,
    refreshTabla,
    // Métodos específicos para productos (pueden configurarse desde PHP)
    openCrear() {
      const url = window.modalUrls?.crear || "/admin/productos/crear";
      load(url + "?modal=1", "Nuevo Producto");
    },
    openEditar(id) {
      const url = window.modalUrls?.editar || "/admin/productos/editar";
      load(url + "/" + id + "?modal=1", "Editar Producto");
    },
  };

  // Auto-inicialización
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
