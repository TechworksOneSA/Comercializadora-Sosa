# Modelo de Base de Datos - Sistema POS/ERP Ferretería

## Tablas Principales

### usuarios

- `id` (int, PK, AUTO_INCREMENT)
- `username` (varchar 50, UNIQUE)
- `email` (varchar 100, UNIQUE)
- `password` (varchar 255)
- `nombre` (varchar 100)
- `apellido` (varchar 100)
- `role` (enum: 'ADMIN', 'VENDEDOR')
- `estado` (enum: 'ACTIVO', 'INACTIVO', 'SUSPENDIDO')
- `ultimo_acceso` (datetime)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### productos

- `id` (int, PK, AUTO_INCREMENT)
- `codigo` (varchar 50, UNIQUE)
- `codigo_barras` (varchar 100, NULL)
- `nombre` (varchar 200)
- `descripcion` (text, NULL)
- `categoria_id` (int, FK)
- `marca` (varchar 100, NULL)
- `unidad_medida` (varchar 20) // 'UNIDAD', 'METRO', 'KILO', etc.
- `precio_compra` (decimal 10,2)
- `precio_venta` (decimal 10,2)
- `precio_mayoreo` (decimal 10,2, NULL)
- `stock_actual` (decimal 10,2)
- `stock_minimo` (decimal 10,2)
- `stock_maximo` (decimal 10,2, NULL)
- `ubicacion` (varchar 100, NULL) // Pasillo, estante, etc.
- `estado` (enum: 'ACTIVO', 'INACTIVO', 'DESCONTINUADO')
- `imagen` (varchar 255, NULL)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### categorias

- `id` (int, PK, AUTO_INCREMENT)
- `nombre` (varchar 100)
- `descripcion` (text, NULL)
- `parent_id` (int, FK, NULL) // Para subcategorías
- `estado` (enum: 'ACTIVO', 'INACTIVO')
- `created_at` (timestamp)

### inventario_movimientos

- `id` (int, PK, AUTO_INCREMENT)
- `producto_id` (int, FK)
- `tipo_movimiento` (enum: 'ENTRADA', 'SALIDA', 'AJUSTE', 'TRANSFERENCIA')
- `cantidad` (decimal 10,2)
- `costo_unitario` (decimal 10,2)
- `referencia_tipo` (varchar 50) // 'COMPRA', 'VENTA', 'AJUSTE', etc.
- `referencia_id` (int, NULL) // ID de la compra, venta, etc.
- `observaciones` (text, NULL)
- `usuario_id` (int, FK)
- `created_at` (timestamp)

### proveedores

- `id` (int, PK, AUTO_INCREMENT)
- `nit` (varchar 20, UNIQUE, NULL)
- `nombre` (varchar 200)
- `contacto` (varchar 100, NULL)
- `telefono` (varchar 20, NULL)
- `email` (varchar 100, NULL)
- `direccion` (text, NULL)
- `estado` (enum: 'ACTIVO', 'INACTIVO')
- `created_at` (timestamp)
- `updated_at` (timestamp)

### compras

- `id` (int, PK, AUTO_INCREMENT)
- `numero_factura` (varchar 100)
- `proveedor_id` (int, FK)
- `fecha_compra` (date)
- `fecha_entrega` (date, NULL)
- `subtotal` (decimal 10,2)
- `impuestos` (decimal 10,2)
- `total` (decimal 10,2)
- `estado` (enum: 'PENDIENTE', 'RECIBIDA', 'CANCELADA')
- `observaciones` (text, NULL)
- `usuario_id` (int, FK)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### compra_detalles

- `id` (int, PK, AUTO_INCREMENT)
- `compra_id` (int, FK)
- `producto_id` (int, FK)
- `cantidad` (decimal 10,2)
- `precio_unitario` (decimal 10,2)
- `subtotal` (decimal 10,2)

### clientes

- `id` (int, PK, AUTO_INCREMENT)
- `nit` (varchar 20, NULL)
- `cui` (varchar 20, NULL)
- `nombre` (varchar 200)
- `telefono` (varchar 20, NULL)
- `email` (varchar 100, NULL)
- `direccion` (text, NULL)
- `tipo` (enum: 'CONSUMIDOR_FINAL', 'EMPRESA', 'GOBIERNO')
- `estado` (enum: 'ACTIVO', 'INACTIVO')
- `created_at` (timestamp)
- `updated_at` (timestamp)

### ventas

- `id` (int, PK, AUTO_INCREMENT)
- `numero_factura` (varchar 100, UNIQUE)
- `cliente_id` (int, FK, NULL) // NULL para consumidor final
- `fecha_venta` (datetime)
- `subtotal` (decimal 10,2)
- `descuento` (decimal 10,2, DEFAULT 0)
- `impuestos` (decimal 10,2)
- `total` (decimal 10,2)
- `metodo_pago` (enum: 'EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'MIXTO')
- `estado` (enum: 'COMPLETADA', 'CANCELADA')
- `usuario_id` (int, FK)
- `fel_uuid` (varchar 100, NULL) // UUID del documento FEL
- `fel_serie` (varchar 20, NULL)
- `fel_numero` (varchar 50, NULL)
- `created_at` (timestamp)

### venta_detalles

- `id` (int, PK, AUTO_INCREMENT)
- `venta_id` (int, FK)
- `producto_id` (int, FK)
- `cantidad` (decimal 10,2)
- `precio_unitario` (decimal 10,2)
- `descuento` (decimal 10,2, DEFAULT 0)
- `subtotal` (decimal 10,2)

### cotizaciones

- `id` (int, PK, AUTO_INCREMENT)
- `numero_cotizacion` (varchar 100, UNIQUE)
- `cliente_id` (int, FK)
- `fecha_cotizacion` (datetime)
- `fecha_vencimiento` (date)
- `subtotal` (decimal 10,2)
- `descuento` (decimal 10,2, DEFAULT 0)
- `impuestos` (decimal 10,2)
- `total` (decimal 10,2)
- `estado` (enum: 'PENDIENTE', 'APROBADA', 'RECHAZADA', 'CONVERTIDA', 'VENCIDA')
- `observaciones` (text, NULL)
- `usuario_id` (int, FK)
- `venta_id` (int, FK, NULL) // Si se convirtió en venta
- `created_at` (timestamp)
- `updated_at` (timestamp)

### cotizacion_detalles

- `id` (int, PK, AUTO_INCREMENT)
- `cotizacion_id` (int, FK)
- `producto_id` (int, FK)
- `cantidad` (decimal 10,2)
- `precio_unitario` (decimal 10,2)
- `descuento` (decimal 10,2, DEFAULT 0)
- `subtotal` (decimal 10,2)

### fel_documentos

- `id` (int, PK, AUTO_INCREMENT)
- `venta_id` (int, FK, NULL)
- `tipo_documento` (varchar 20) // 'FACT', 'FCAM', etc.
- `serie` (varchar 20)
- `numero` (varchar 50)
- `uuid` (varchar 100, UNIQUE)
- `fecha_emision` (datetime)
- `nit_emisor` (varchar 20)
- `nit_receptor` (varchar 20, NULL)
- `total` (decimal 10,2)
- `xml_content` (longtext)
- `pdf_url` (varchar 255, NULL)
- `estado` (enum: 'PENDIENTE', 'CERTIFICADO', 'ERROR', 'ANULADO')
- `response_sat` (text, NULL)
- `created_at` (timestamp)

### cierre_caja

- `id` (int, PK, AUTO_INCREMENT)
- `fecha` (date)
- `usuario_id` (int, FK)
- `hora_apertura` (datetime)
- `hora_cierre` (datetime, NULL)
- `monto_inicial` (decimal 10,2)
- `ventas_efectivo` (decimal 10,2)
- `ventas_tarjeta` (decimal 10,2)
- `ventas_transferencia` (decimal 10,2)
- `total_ventas` (decimal 10,2)
- `gastos` (decimal 10,2, DEFAULT 0)
- `monto_final_sistema` (decimal 10,2)
- `monto_final_fisico` (decimal 10,2, NULL)
- `diferencia` (decimal 10,2, NULL)
- `observaciones` (text, NULL)
- `estado` (enum: 'ABIERTA', 'CERRADA')
- `created_at` (timestamp)

### configuracion

- `id` (int, PK, AUTO_INCREMENT)
- `clave` (varchar 100, UNIQUE)
- `valor` (text)
- `descripcion` (varchar 255, NULL)
- `tipo` (enum: 'STRING', 'NUMBER', 'BOOLEAN', 'JSON')
- `updated_at` (timestamp)

## Índices Importantes

```sql
-- Productos
INDEX idx_productos_codigo (codigo)
INDEX idx_productos_codigo_barras (codigo_barras)
INDEX idx_productos_categoria (categoria_id)
INDEX idx_productos_estado (estado)

-- Inventario
INDEX idx_inventario_producto (producto_id)
INDEX idx_inventario_fecha (created_at)
INDEX idx_inventario_tipo (tipo_movimiento)

-- Ventas
INDEX idx_ventas_fecha (fecha_venta)
INDEX idx_ventas_usuario (usuario_id)
INDEX idx_ventas_cliente (cliente_id)
INDEX idx_ventas_estado (estado)

-- FEL
INDEX idx_fel_uuid (uuid)
INDEX idx_fel_venta (venta_id)
INDEX idx_fel_fecha (fecha_emision)

-- Compras
INDEX idx_compras_proveedor (proveedor_id)
INDEX idx_compras_fecha (fecha_compra)
INDEX idx_compras_estado (estado)
```

## Relaciones Principales

- `productos` → `categorias` (muchos a uno)
- `productos` → `inventario_movimientos` (uno a muchos)
- `compras` → `proveedores` (muchos a uno)
- `compras` → `compra_detalles` (uno a muchos)
- `ventas` → `clientes` (muchos a uno)
- `ventas` → `venta_detalles` (uno a muchos)
- `ventas` → `fel_documentos` (uno a uno)
- `cotizaciones` → `cotizacion_detalles` (uno a muchos)
- `usuarios` → `ventas`, `compras`, etc. (uno a muchos)
