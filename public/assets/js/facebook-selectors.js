/**
 * Facebook-style Selectors
 * Componente de selección con búsqueda tipo Facebook
 */

function initFacebookSelectors() {
  document.querySelectorAll(".fbselect").forEach((selector) => {
    const input = selector.querySelector(".fbselect-input");
    const menu = selector.querySelector(".fbselect-menu");
    const hiddenInput = selector.querySelector('input[type="hidden"]');
    const items = menu.querySelectorAll(".fbselect-item");

    let isOpen = false;

    // Establecer valor inicial
    const initialValue = hiddenInput.value;
    if (initialValue) {
      const initialItem = menu.querySelector(`[data-value="${initialValue}"]`);
      if (initialItem) {
        input.value = initialItem.textContent.trim();
        hiddenInput.value = initialValue;
      }
    }

    // Abrir/cerrar menú solo con click intencional
    input.addEventListener("click", () => toggleMenu(true));

    // Filtrar opciones mientras se escribe
    input.addEventListener("input", () => {
      const query = input.value.toLowerCase();
      let hasVisibleItems = false;

      items.forEach((item) => {
        const searchText = item.dataset.search || "";
        const itemText = item.textContent.toLowerCase();
        const matches = searchText.includes(query) || itemText.includes(query);

        item.style.display = matches ? "block" : "none";
        if (matches) hasVisibleItems = true;
      });

      menu.style.display = hasVisibleItems ? "block" : "none";

      // Si no hay coincidencia exacta, limpiar valor oculto
      if (
        query &&
        !Array.from(items).some(
          (item) => item.style.display !== "none" && item.textContent.toLowerCase() === query
        )
      ) {
        hiddenInput.value = "";
      }
    });

    // Seleccionar opción
    items.forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();

        const value = item.dataset.value;
        const text = item.textContent.trim();

        input.value = text;
        hiddenInput.value = value;
        toggleMenu(false);

        // Trigger change event
        hiddenInput.dispatchEvent(new Event("change", { bubbles: true }));
      });
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener("click", (e) => {
      if (!selector.contains(e.target)) {
        toggleMenu(false);
      }
    });

    // Cerrar con Escape
    input.addEventListener("keydown", (e) => {
      const visibleItems = Array.from(items).filter((item) => item.style.display !== "none");
      const currentIndex = visibleItems.findIndex((item) => item.classList.contains("highlighted"));

      switch (e.key) {
        case "ArrowDown":
          e.preventDefault();
          if (!isOpen) toggleMenu(true);
          else highlightItem(visibleItems, currentIndex + 1);
          break;
        case "ArrowUp":
          e.preventDefault();
          if (isOpen) highlightItem(visibleItems, currentIndex - 1);
          break;
        case "Enter":
          e.preventDefault();
          if (isOpen && currentIndex >= 0) {
            visibleItems[currentIndex].click();
          }
          break;
        case "Escape":
          toggleMenu(false);
          input.blur();
          break;
        case "Tab":
          toggleMenu(false);
          break;
      }
    });

    function toggleMenu(open) {
      isOpen = open;
      menu.style.display = open ? "block" : "none";
      selector.classList.toggle("fbselect-open", open);

      if (open) {
        // Mostrar todas las opciones al abrir
        items.forEach((item) => (item.style.display = "block"));

        // Posicionar menú
        const rect = input.getBoundingClientRect();
        const menuHeight = menu.scrollHeight;
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;

        if (menuHeight > spaceBelow && spaceAbove > spaceBelow) {
          menu.classList.add("fbselect-menu-up");
          menu.classList.remove("fbselect-menu-down");
        } else {
          menu.classList.add("fbselect-menu-down");
          menu.classList.remove("fbselect-menu-up");
        }

        // Focus en el input
        setTimeout(() => input.focus(), 0);
      } else {
        // Limpiar highlighting
        items.forEach((item) => item.classList.remove("highlighted"));
        menu.classList.remove("fbselect-menu-up", "fbselect-menu-down");
      }
    }

    function highlightItem(visibleItems, index) {
      items.forEach((item) => item.classList.remove("highlighted"));

      // Circular navigation
      if (index < 0) index = visibleItems.length - 1;
      if (index >= visibleItems.length) index = 0;

      if (visibleItems[index]) {
        visibleItems[index].classList.add("highlighted");
        visibleItems[index].scrollIntoView({ block: "nearest", behavior: "smooth" });
      }
    }
  });
}

// Auto-inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initFacebookSelectors);
} else {
  initFacebookSelectors();
}
