# API Endpoints - Sistema POS/ERP Ferretería

## Autenticación

- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/me` - Obtener usuario actual

## Productos

- `GET /api/productos` - Listar productos
- `GET /api/productos/{id}` - Obtener producto
- `POST /api/productos` - Crear producto (ADMIN)
- `PUT /api/productos/{id}` - Actualizar producto (ADMIN)
- `DELETE /api/productos/{id}` - Eliminar producto (ADMIN)
- `GET /api/productos/buscar?q={query}` - Buscar productos
- `GET /api/productos/{id}/stock` - Consultar stock

## Inventario

- `GET /api/inventario` - Estado de inventario
- `GET /api/inventario/kardex/{producto_id}` - Kardex por producto
- `POST /api/inventario/movimiento` - Registrar movimiento
- `GET /api/inventario/alertas` - Productos con stock bajo

## Ventas/POS

- `GET /api/ventas` - Listar ventas
- `GET /api/ventas/{id}` - Obtener venta
- `POST /api/ventas` - Procesar venta
- `PUT /api/ventas/{id}/cancelar` - Cancelar venta (ADMIN)
- `GET /api/pos/productos/{codigo}` - Buscar por código/escáner
- `POST /api/pos/calcular` - Calcular totales de venta

## Compras

- `GET /api/compras` - Listar compras
- `GET /api/compras/{id}` - Obtener compra
- `POST /api/compras` - Crear orden de compra (ADMIN)
- `PUT /api/compras/{id}` - Actualizar compra (ADMIN)
- `POST /api/compras/{id}/recibir` - Marcar como recibida

## Cotizaciones

- `GET /api/cotizaciones` - Listar cotizaciones
- `GET /api/cotizaciones/{id}` - Obtener cotización
- `POST /api/cotizaciones` - Crear cotización
- `PUT /api/cotizaciones/{id}` - Actualizar cotización
- `POST /api/cotizaciones/{id}/convertir` - Convertir a venta

## Clientes

- `GET /api/clientes` - Listar clientes
- `GET /api/clientes/{id}` - Obtener cliente
- `POST /api/clientes` - Crear cliente
- `PUT /api/clientes/{id}` - Actualizar cliente
- `GET /api/clientes/buscar?q={query}` - Buscar clientes

## Proveedores

- `GET /api/proveedores` - Listar proveedores (ADMIN)
- `GET /api/proveedores/{id}` - Obtener proveedor (ADMIN)
- `POST /api/proveedores` - Crear proveedor (ADMIN)
- `PUT /api/proveedores/{id}` - Actualizar proveedor (ADMIN)

## FEL (Facturación Electrónica)

- `GET /api/fel/documentos` - Listar documentos FEL
- `GET /api/fel/documentos/{id}` - Obtener documento FEL
- `POST /api/fel/certificar` - Enviar documento al certificador
- `GET /api/fel/config` - Obtener configuración FEL (ADMIN)
- `PUT /api/fel/config` - Actualizar configuración FEL (ADMIN)

## Cierre de Caja

- `GET /api/cierrecaja/actual` - Obtener estado de caja actual
- `POST /api/cierrecaja/abrir` - Abrir caja
- `POST /api/cierrecaja/cerrar` - Cerrar caja
- `GET /api/cierrecaja/historial` - Historial de cierres

## Reportes

- `GET /api/reportes/ventas?fecha_inicio={date}&fecha_fin={date}` - Reporte ventas
- `GET /api/reportes/inventario` - Reporte de inventario
- `GET /api/reportes/compras?fecha_inicio={date}&fecha_fin={date}` - Reporte compras
- `POST /api/reportes/generar/{tipo}` - Generar reporte PDF/Excel

## Usuarios (Solo ADMIN)

- `GET /api/usuarios` - Listar usuarios
- `GET /api/usuarios/{id}` - Obtener usuario
- `POST /api/usuarios` - Crear usuario
- `PUT /api/usuarios/{id}` - Actualizar usuario
- `DELETE /api/usuarios/{id}` - Eliminar usuario

## Formatos de Respuesta

### Success Response

```json
{
  "success": true,
  "data": {...},
  "message": "Operación exitosa"
}
```

### Error Response

```json
{
  "success": false,
  "error": "Descripción del error",
  "code": 400
}
```

### Pagination Response

```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 150,
    "pages": 8
  }
}
```

## Autenticación

- Sesiones PHP para aplicación web
- Headers requeridos:
  - `Content-Type: application/json`
  - `X-Requested-With: XMLHttpRequest`

## Códigos de Estado HTTP

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error
