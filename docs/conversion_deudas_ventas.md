# Conversión Automática de Deudas a Ventas

## Funcionalidad Implementada

### 📋 Descripción

Cuando una deuda es completamente pagada (saldo = 0), el sistema automáticamente:

1. Crea una venta nueva en el módulo de ventas
2. Transfiere toda la información de la deuda a la venta
3. Marca la deuda como "CONVERTIDA"
4. Vincula la venta generada con la deuda original

### 🔧 Archivos Modificados

#### 1. DeudoresModel.php

- **Método modificado**: `registrarPago()`
- **Nueva funcionalidad**:
  - Detecta cuando saldo llega a 0
  - Llama automáticamente a `convertirDeudaAVenta()`
- **Métodos añadidos**:
  - `convertirDeudaAVenta()`: Convierte deuda pagada a venta
  - `marcarDeudaComoConvertida()`: Actualiza estado de deuda

#### 2. VentasModel.php

- **Método añadido**: `crearVentaDesdeDeuda()`
- **Funcionalidad**:
  - Crea venta con estado "COMPLETADA" (ya pagada)
  - Transfiere productos sin descontar stock (ya descontado)
  - Registra movimiento contable del cliente

#### 3. DeudoresController.php

- **Método modificado**: `registrarPago()`
- **Nueva funcionalidad**:
  - Detecta si deuda fue convertida a venta
  - Muestra mensaje informativo al usuario

### 🗄️ Cambios de Base de Datos

#### Columnas Añadidas

```sql
-- En tabla ventas
ALTER TABLE ventas ADD COLUMN observaciones TEXT NULL;
ALTER TABLE ventas ADD COLUMN deuda_origen_id INT NULL;

-- En tabla deudores
ALTER TABLE deudores ADD COLUMN estado ENUM('ACTIVA', 'PAGADA', 'CONVERTIDA', 'ANULADA') DEFAULT 'ACTIVA';
ALTER TABLE deudores ADD COLUMN venta_generada_id INT NULL;
```

#### Script de Migración

Ejecutar: `app/sql/migracion_deuda_to_venta.sql`

### 📱 Experiencia del Usuario

#### Antes

1. Usuario registra pago
2. Deuda cambia estado visual a "PAGADA"
3. Deuda permanece en módulo deudores

#### Después

1. Usuario registra pago final
2. Sistema detecta saldo = 0
3. **Automáticamente** crea venta nueva
4. Deuda cambia a estado "CONVERTIDA"
5. Usuario ve mensaje: _"Pago registrado. La deuda se ha convertido automáticamente a VENTA #123"_

### 🔗 Integración

#### En Módulo Ventas

- Las ventas convertidas aparecen con:
  - Estado: "COMPLETADA"
  - Observaciones: "Venta generada automáticamente de Deuda #X"
  - Campo `deuda_origen_id` para rastreo

#### En Módulo Deudores

- Deudas convertidas muestran:
  - Estado: "CONVERTIDA"
  - Campo `venta_generada_id` para referencia

### 🔄 Flujo de Datos

```
Deuda ACTIVA → Registrar Pago → ¿Saldo = 0?
    ↓                                    ↓ SÍ
Actualizar total_pagado     →    Convertir a Venta
    ↓                                    ↓
Mantener ACTIVA              →    Marcar CONVERTIDA
                                        ↓
                                  Crear registro en tabla ventas
                                        ↓
                                  Notificar al usuario
```

### ⚠️ Consideraciones

1. **Stock**: No se descuenta al convertir (ya descontado en deuda original)
2. **Contabilidad**: Se registra movimiento en `total_gastado` del cliente
3. **Integridad**: Transacciones aseguran consistencia de datos
4. **Rastreo**: Enlaces bidireccionales entre deuda y venta generada

### 🧪 Testing

Para probar la funcionalidad:

1. Crear una deuda nueva
2. Registrar pagos parciales
3. Registrar pago final que iguale el saldo
4. Verificar que aparece en módulo ventas
5. Verificar estado "CONVERTIDA" en deudores

### 📈 Beneficios

- **Automatización**: Sin intervención manual del usuario
- **Integridad**: Datos consistentes entre módulos
- **Trazabilidad**: Historial completo de conversiones
- **UX Mejorada**: Feedback inmediato y claro
