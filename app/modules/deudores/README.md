# Módulo de Deudores - Comercializadora Sosa

## 📝 Descripción
Módulo completo para gestión de deudas de clientes con seguimiento de productos, pagos y saldos pendientes.

## 🗄️ Instalación de Base de Datos

Ejecuta el siguiente SQL en phpMyAdmin (base de datos: `comercializadora_sosa`):

```sql
-- Tabla principal de deudas
CREATE TABLE IF NOT EXISTS deuda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha DATETIME NOT NULL,
    total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_pagado DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    descripcion TEXT NULL,
    estado ENUM('ACTIVA','CANCELADA') NOT NULL DEFAULT 'ACTIVA',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX (cliente_id),
    FOREIGN KEY (cliente_id) REFERENCES clientess(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalle de productos en deudas
CREATE TABLE IF NOT EXISTS deuda_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deuda_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    INDEX (deuda_id),
    INDEX (producto_id),
    FOREIGN KEY (deuda_id) REFERENCES deuda(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pagos/abonos de deudas
CREATE TABLE IF NOT EXISTS deuda_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deuda_id INT NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    fecha DATETIME NOT NULL,
    usuario_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deuda_id) REFERENCES deuda(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 🚀 Uso del Módulo

### Acceso Principal
- **URL:** http://localhost/ferreteria-pos/public/admin/deudores
- **Ubicación en menú:** Sidebar Admin → Sección "Clientes" → Deudores

### Funcionalidades

#### 1. Crear Nueva Deuda
1. Clic en botón **"Nueva Deuda"**
2. **Buscar cliente:** Escribe nombre, NIT o teléfono
3. **Agregar productos:**
   - Busca el producto por nombre o código de barras
   - Selecciona cantidad
   - Clic en **"Agregar"**
4. Opcional: Añade descripción
5. Clic en **"Guardar Deuda"**

**Características:**
- Búsqueda autocompletable de clientes
- Búsqueda autocompletable de productos con stock en tiempo real
- Validación de stock automática
- Descuento automático de inventario
- Cálculo automático de totales

#### 2. Ver Detalle de Deuda
- Desde el listado, clic en **"Ver Detalle"**
- Muestra:
  - Resumen financiero (Total, Pagado, Saldo)
  - Productos incluidos en la deuda
  - Historial de pagos realizados
  
#### 3. Registrar Abono
1. Desde el detalle de deuda, clic en **"Registrar Pago"**
2. Ingresa monto (máximo: saldo pendiente)
3. Clic en **"Registrar Pago"**

**Características:**
- Actualización automática del saldo
- Registro de usuario y fecha
- Validación de montos

## 📊 Estructura de Archivos

```
app/modules/deudores/
├── DeudoresController.php    # Lógica de negocio
├── DeudoresModel.php          # Acceso a base de datos
└── views/
    ├── crear.php             # Formulario de nueva deuda
    ├── index.php             # Listado de deudas
    └── ver.php               # Detalle y pagos
```

## 🔗 Rutas Registradas

```php
GET  /admin/deudores                  → Listado
GET  /admin/deudores/crear            → Formulario nueva deuda
POST /admin/deudores/guardar          → Procesar nueva deuda
GET  /admin/deudores/ver?id={id}      → Ver detalle
POST /admin/deudores/registrarPago    → Registrar abono
POST /admin/deudores/ampliar          → Ampliar deuda (reservado)
```

## 📋 Notas Técnicas

- **Stock:** Se descuenta automáticamente al crear la deuda
- **Saldo:** Se calcula como `total - total_pagado`
- **Estados:** ACTIVA (predeterminado) | CANCELADA
- **Ampliación:** Endpoint disponible para incrementar monto de deuda existente

## 🎨 Características de UI

- Diseño moderno con gradientes
- Búsqueda autocompletable tipo-ahead
- Indicadores visuales de stock
- Resumen financiero con tarjetas coloridas
- Modal para registro de pagos
- Tabla responsive con información detallada

## ✅ Validaciones Implementadas

- Cliente obligatorio
- Al menos un producto requerido
- Stock suficiente antes de agregar
- Monto de pago no puede exceder saldo
- Productos activos únicamente

## 🔐 Seguridad

- Middleware de autenticación `RoleMiddleware::requireAdmin()`
- Validación de datos en backend
- Transacciones SQL con rollback automático
- Escapado de HTML para prevenir XSS

---

**Desarrollado para:** Comercializadora Sosa  
**Fecha:** Diciembre 2025  
**Versión:** 1.0.0
