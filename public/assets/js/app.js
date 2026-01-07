/**
 * JavaScript global para Sistema POS/ERP Ferretería
 * Helpers y funciones comunes
 */

// Configuración global de la aplicación
const App = {
  baseUrl: window.location.origin,
  apiUrl: window.location.origin + "/api",
  version: "1.0.0",
};

// Helpers para manejo de DOM
const DOM = {
  // Selector helper
  select: (selector) => document.querySelector(selector),
  selectAll: (selector) => document.querySelectorAll(selector),

  // Event helper
  on: (element, event, handler) => {
    if (typeof element === "string") {
      element = document.querySelector(element);
    }
    if (element) {
      element.addEventListener(event, handler);
    }
  },

  // Show/Hide elements
  show: (element) => {
    if (typeof element === "string") {
      element = document.querySelector(element);
    }
    if (element) element.style.display = "block";
  },

  hide: (element) => {
    if (typeof element === "string") {
      element = document.querySelector(element);
    }
    if (element) element.style.display = "none";
  },
};

// Helpers para API calls
const API = {
  // GET request
  get: async (endpoint) => {
    try {
      const response = await fetch(`${App.apiUrl}/${endpoint}`);
      return await response.json();
    } catch (error) {
      console.error("API GET Error:", error);
      throw error;
    }
  },

  // POST request
  post: async (endpoint, data) => {
    try {
      const response = await fetch(`${App.apiUrl}/${endpoint}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });
      return await response.json();
    } catch (error) {
      console.error("API POST Error:", error);
      throw error;
    }
  },
};

// Helpers para POS (escáner, formato moneda, etc)
const POS = {
  // Formatear precio en quetzales
  formatPrice: (amount) => {
    return new Intl.NumberFormat("es-GT", {
      style: "currency",
      currency: "GTQ",
    }).format(amount);
  },

  // Formatear número con separadores
  formatNumber: (number) => {
    return new Intl.NumberFormat("es-GT").format(number);
  },

  // Scanner placeholder - se implementará cuando se integre hardware
  scanner: {
    isListening: false,
    buffer: "",

    startListening: () => {
      console.log("Scanner: Iniciando escucha...");
      // TODO: Implementar lógica de escáner
    },

    stopListening: () => {
      console.log("Scanner: Deteniendo escucha...");
      // TODO: Implementar lógica de escáner
    },
  },
};

// Utilities generales
const Utils = {
  // Generar UUID simple
  generateUUID: () => {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
      const r = (Math.random() * 16) | 0;
      const v = c == "x" ? r : (r & 0x3) | 0x8;
      return v.toString(16);
    });
  },

  // Formatear fecha
  formatDate: (date) => {
    return new Intl.DateTimeFormat("es-GT", {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
    }).format(new Date(date));
  },

  // Debounce function
  debounce: (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },
};

// Inicialización cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  console.log("Sistema POS/ERP Ferretería - JavaScript cargado");

  // Aquí se pueden inicializar componentes globales
  // TODO: Inicializar componentes cuando se implementen
  
  // Solución para el problema de focus persistente (sombra blanca)
  // Quita el foco automáticamente después de hacer click en enlaces y botones
  initializeFocusFix();
});

/**
 * Solución para el problema de la "sombra blanca" persistente
 * Remueve automáticamente el foco de botones y enlaces después de hacer click
 */
function initializeFocusFix() {
  // Elementos que deben perder el foco después del click
  const selectors = [
    'a',
    'button',
    '.admin-nav-item',
    '.admin-logout',
    'input[type="submit"]',
    'input[type="button"]'
  ];
  
  // Agregar listener a cada tipo de elemento
  selectors.forEach(selector => {
    document.addEventListener('click', (e) => {
      const element = e.target.closest(selector);
      if (element) {
        // Quitar el foco después de un pequeño delay para permitir la navegación
        setTimeout(() => {
          element.blur();
        }, 100);
      }
    });
  });
  
  console.log('✓ Fix de focus persistente activado');
}

// Exportar para uso global
window.App = App;
window.DOM = DOM;
window.API = API;
window.POS = POS;
window.Utils = Utils;
