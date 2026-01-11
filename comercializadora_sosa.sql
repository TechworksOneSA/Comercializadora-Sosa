-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-01-2026 a las 07:50:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comercializadora_sosa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `margen_porcentaje` decimal(5,2) DEFAULT NULL,
  `margen_fijo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activo`, `margen_porcentaje`, `margen_fijo`) VALUES
(1, 'GENERAL', NULL, 1, NULL, NULL),
(8, 'TORNILLOS', NULL, 1, NULL, NULL),
(9, 'Barrenos', NULL, 1, NULL, NULL),
(10, 'Herramientas', 'Herramientas manuales y el├®ctricas', 1, NULL, NULL),
(11, 'Materiales de Construcci├│n', 'Cemento, arena, ladrillos, etc.', 1, NULL, NULL),
(12, 'Plomería', 'Tuber├¡as, llaves, accesorios', 1, NULL, NULL),
(13, 'Electricidad', 'Cables, interruptores, tomacorrientes', 1, NULL, NULL),
(14, 'Pintura', 'Pinturas, brochas, rodillos', 1, NULL, NULL),
(15, 'H', NULL, 1, NULL, NULL),
(16, 'A', NULL, 1, NULL, NULL),
(17, 'D', NULL, 1, NULL, NULL),
(18, 'X', NULL, 1, NULL, NULL),
(0, 'ss', NULL, 1, NULL, NULL),
(0, 'z', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierres_caja`
--

CREATE TABLE `cierres_caja` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total_sistema` decimal(10,2) NOT NULL,
  `total_reportado` decimal(10,2) NOT NULL,
  `diferencia` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacion_productos`
--

CREATE TABLE `clasificacion_productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('CATEGORIA','MARCA') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `preferencia_metodo_pago` varchar(50) NOT NULL COMMENT 'Preferencia del cliente (no necesariamente el método real de cada venta)',
  `nit` varchar(30) DEFAULT NULL,
  `total_gastado` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Cache: se actualizará desde ventas/pagos',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `telefono`, `direccion`, `preferencia_metodo_pago`, `nit`, `total_gastado`, `created_at`, `updated_at`) VALUES
(2, 'Nestor', 'Verbena', '55644894', 'Gualan, zacapa', 'Efectivo', '1111111', 680.00, '2025-12-12 23:36:14', '2025-12-14 19:23:49'),
(3, 'BRENNER', 'GRANADOS', '55644811', 'Gualan, zacapa', 'Efectivo', '222222', 3552.00, '2025-12-13 05:04:20', '2026-01-11 03:51:31'),
(4, 'Marconi', 'GUTIERREZ', '47840624', 'zacapa', 'Crédito', '1235252', 1550.00, '2025-12-17 14:31:51', '2026-01-11 03:13:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `serie_factura` varchar(30) DEFAULT NULL,
  `numero_factura` varchar(30) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('REGISTRADA','ANULADA') NOT NULL DEFAULT 'REGISTRADA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `anulada_at` datetime DEFAULT NULL,
  `anulada_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `proveedor_id`, `usuario_id`, `fecha`, `serie_factura`, `numero_factura`, `subtotal`, `iva`, `total`, `estado`, `created_at`, `anulada_at`, `anulada_por`) VALUES
(4, 1, 1, '2025-11-26 00:00:00', '', 'prueba', 10.00, 0.00, 10.00, 'REGISTRADA', '2025-11-26 17:07:44', NULL, NULL),
(5, 1, 1, '2025-11-26 00:00:00', '', 'PRUEBA', 50.00, 0.00, 50.00, 'REGISTRADA', '2025-11-26 17:08:56', NULL, NULL),
(6, 1, 1, '2025-11-28 00:00:00', '', '', 50.00, 0.00, 50.00, 'REGISTRADA', '2025-11-28 05:13:59', NULL, NULL),
(8, 2, 1, '2025-12-18 00:00:00', '', '2485618', 400.00, 0.00, 400.00, 'REGISTRADA', '2025-12-18 02:40:24', NULL, NULL),
(9, 2, 1, '2025-12-18 00:00:00', '', '', 1020.00, 0.00, 1020.00, 'REGISTRADA', '2025-12-18 02:42:38', NULL, NULL),
(11, 2, 1, '2026-01-01 00:00:00', '', '1', 5.00, 0.00, 5.00, 'REGISTRADA', '2026-01-06 22:22:12', NULL, NULL),
(12, 3, 1, '2026-01-11 00:00:00', '', 'K', 0.00, 0.00, 0.00, 'REGISTRADA', '2026-01-11 03:49:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_detalle`
--

CREATE TABLE `compras_detalle` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras_detalle`
--

INSERT INTO `compras_detalle` (`id`, `compra_id`, `producto_id`, `cantidad`, `costo_unitario`, `descuento`, `subtotal`) VALUES
(4, 4, 3, 2, 5.00, 0.00, 10.00),
(5, 5, 3, 10, 5.00, 0.00, 50.00),
(6, 6, 3, 5, 10.00, 0.00, 50.00),
(8, 8, 9, 8, 50.00, 0.00, 400.00),
(9, 9, 9, 1, 20.00, 0.00, 20.00),
(10, 9, 8, 500, 2.00, 0.00, 1000.00),
(13, 11, 9, 1, 5.00, 0.00, 5.00),
(0, 12, 9, 1, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion`
--

CREATE TABLE `cotizacion` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_expiracion` date NOT NULL,
  `estado` enum('ACTIVA','VENCIDA','CONVERTIDA') NOT NULL DEFAULT 'ACTIVA',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cotizacion`
--

INSERT INTO `cotizacion` (`id`, `cliente_id`, `fecha`, `fecha_expiracion`, `estado`, `subtotal`, `total`, `created_at`, `updated_at`) VALUES
(10, 2, '2025-12-13', '2025-12-14', 'CONVERTIDA', 20.00, 20.00, '2025-12-13 00:26:06', '2025-12-13 03:42:09'),
(11, 3, '2025-12-13', '2025-12-20', 'CONVERTIDA', 170.00, 170.00, '2025-12-13 05:05:36', '2025-12-13 05:07:01'),
(12, 3, '2025-12-13', '2025-12-20', 'CONVERTIDA', 190.00, 190.00, '2025-12-13 05:54:06', '2025-12-13 05:54:17'),
(13, 2, '2025-12-13', '2025-12-20', 'CONVERTIDA', 510.00, 510.00, '2025-12-13 06:05:47', '2025-12-13 06:08:13'),
(15, 3, '2025-12-18', '2025-12-25', 'CONVERTIDA', 350.00, 350.00, '2025-12-18 02:44:28', '2025-12-18 02:45:33'),
(17, 4, '2026-01-06', '2026-01-16', 'CONVERTIDA', 350.00, 350.00, '2026-01-06 23:29:03', '2026-01-09 05:20:29'),
(18, 3, '2026-01-06', '2026-01-15', 'CONVERTIDA', 350.00, 350.00, '2026-01-06 23:29:40', '2026-01-09 03:02:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones_detalle`
--

CREATE TABLE `cotizaciones_detalle` (
  `id` int(11) NOT NULL,
  `cotizacion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_detalle`
--

CREATE TABLE `cotizacion_detalle` (
  `id` int(11) NOT NULL,
  `cotizacion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `total_linea` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cotizacion_detalle`
--

INSERT INTO `cotizacion_detalle` (`id`, `cotizacion_id`, `producto_id`, `cantidad`, `precio_unitario`, `total_linea`) VALUES
(3, 10, 3, 2, 10.00, 20.00),
(4, 11, 4, 1, 170.00, 170.00),
(5, 12, 7, 5, 38.00, 190.00),
(6, 13, 4, 3, 170.00, 510.00),
(8, 15, 9, 1, 350.00, 350.00),
(9, 17, 9, 1, 350.00, 350.00),
(10, 18, 9, 1, 350.00, 350.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deuda`
--

CREATE TABLE `deuda` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_pagado` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVA','CANCELADA','CONVERTIDA','PAGADA') DEFAULT 'ACTIVA',
  `venta_generada_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `anulada_at` datetime DEFAULT NULL,
  `anulada_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `deuda`
--

INSERT INTO `deuda` (`id`, `cliente_id`, `usuario_id`, `fecha`, `total`, `total_pagado`, `descripcion`, `estado`, `venta_generada_id`, `created_at`, `updated_at`, `anulada_at`, `anulada_por`) VALUES
(3, 4, 1, '2025-12-17 09:17:59', 170.00, 170.00, 'DEUDA', 'ACTIVA', NULL, '2025-12-17 15:17:59', '2025-12-18 02:47:15', NULL, NULL),
(4, 2, 1, '2025-12-17 09:21:01', 152.00, 152.00, 'DEUDADAAAAA', 'ACTIVA', NULL, '2025-12-17 15:21:01', '2026-01-05 03:34:59', NULL, NULL),
(12, 4, 1, '2026-01-05 04:27:31', 350.00, 350.00, '', 'ACTIVA', NULL, '2026-01-05 04:27:31', '2026-01-06 14:58:09', NULL, NULL),
(13, 3, 1, '2026-01-06 18:16:40', 350.00, 350.00, '', 'ACTIVA', NULL, '2026-01-06 18:16:40', '2026-01-06 18:23:33', NULL, NULL),
(14, 4, 1, '2026-01-08 21:22:03', 400.00, 400.00, '', 'PAGADA', 17, '2026-01-09 03:22:03', '2026-01-09 03:39:34', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deuda_detalle`
--

CREATE TABLE `deuda_detalle` (
  `id` int(10) UNSIGNED NOT NULL,
  `deuda_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `deuda_detalle`
--

INSERT INTO `deuda_detalle` (`id`, `deuda_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 3, 4, 1, 170.00, 170.00),
(2, 4, 7, 4, 38.00, 152.00),
(6, 12, 9, 1, 350.00, 350.00),
(7, 13, 9, 1, 350.00, 350.00),
(0, 14, 9, 1, 400.00, 400.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deuda_pagos`
--

CREATE TABLE `deuda_pagos` (
  `id` int(11) NOT NULL,
  `deuda_id` int(11) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT 'Efectivo',
  `fecha` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `deuda_pagos`
--

INSERT INTO `deuda_pagos` (`id`, `deuda_id`, `monto`, `metodo_pago`, `fecha`, `usuario_id`, `created_at`) VALUES
(1, 3, 100.00, 'Efectivo', '2025-12-17 09:20:11', 1, '2025-12-17 15:20:11'),
(2, 3, 70.00, 'Efectivo', '2025-12-17 20:47:15', 1, '2025-12-18 02:47:15'),
(3, 4, 50.00, 'Efectivo', '2026-01-05 03:19:29', 1, '2026-01-05 03:19:29'),
(8, 4, 100.00, 'Efectivo', '2026-01-05 03:34:04', 1, '2026-01-05 03:34:04'),
(9, 4, 2.00, 'Efectivo', '2026-01-05 03:34:59', 1, '2026-01-05 03:34:59'),
(10, 12, 350.00, 'Tarjeta', '2026-01-06 14:58:09', 1, '2026-01-06 14:58:09'),
(11, 13, 350.00, 'Efectivo', '2026-01-06 18:23:33', 1, '2026-01-06 18:23:33'),
(0, 14, 400.00, 'Efectivo', '2026-01-08 21:28:59', 1, '2026-01-09 03:28:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fel_documentos`
--

CREATE TABLE `fel_documentos` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `uuid` varchar(120) NOT NULL,
  `serie` varchar(30) NOT NULL,
  `numero` varchar(30) NOT NULL,
  `fecha_certificacion` datetime NOT NULL,
  `nit_emisor` varchar(30) NOT NULL,
  `nit_receptor` varchar(30) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('CERTIFICADA','ANULADA','ERROR') NOT NULL DEFAULT 'CERTIFICADA',
  `xml_path` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_movimientos`
--

CREATE TABLE `inventario_movimientos` (
  `id` bigint(20) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('ENTRADA','SALIDA','AJUSTE') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo_unitario` decimal(10,2) DEFAULT NULL,
  `motivo` varchar(150) NOT NULL,
  `referencia_tipo` enum('COMPRA','VENTA','COTIZACION','AJUSTE') NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `nombre`, `activo`) VALUES
(1, 'GENÉRICA', 1),
(2, 'dewal', 1),
(3, 'Q', 1),
(0, 'sanley', 1),
(0, 'v', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL,
  `tipo` enum('ingreso','gasto','retiro') NOT NULL COMMENT 'ingreso=cobros, gasto=gastos operativos, retiro=retiro para banco',
  `concepto` varchar(255) NOT NULL COMMENT 'Descripci¾n del movimiento',
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL COMMENT 'Efectivo, Transferencia, Tarjeta, etc.',
  `observaciones` text DEFAULT NULL,
  `venta_id` int(11) DEFAULT NULL COMMENT 'Si es un cobro de venta',
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos_caja`
--

INSERT INTO `movimientos_caja` (`id`, `tipo`, `concepto`, `monto`, `metodo_pago`, `observaciones`, `venta_id`, `usuario_id`, `fecha`) VALUES
(1, 'ingreso', 'Cobro de venta', 510.00, 'Efectivo', '', 2, 1, '2025-12-18 18:24:32'),
(2, 'gasto', 'Gaseosa', 20.00, 'Efectivo', 'Almuerzo', NULL, 1, '2025-12-18 19:06:01'),
(3, 'ingreso', 'Cobro de venta', 152.00, 'Tarjeta', '', 3, 1, '2025-12-18 19:11:50'),
(0, 'ingreso', 'Cobro de venta', 170.00, 'Transferencia', '', 4, 1, '2026-01-08 23:19:25'),
(0, 'ingreso', 'Cobro de venta', 350.00, 'Transferencia', '', 5, 1, '2026-01-08 23:21:00'),
(0, 'ingreso', 'Cobro de venta', 360.50, 'Tarjeta', '', 7, 1, '2026-01-08 23:25:20'),
(0, 'ingreso', 'Cobro de venta', 1200.00, 'Mixto', '', 9, 1, '2026-01-08 23:25:48'),
(0, 'ingreso', 'Cobro de venta', 100.00, 'Efectivo', 'Venta de productos varios', NULL, 1, '2026-01-09 00:36:49'),
(0, 'ingreso', 'Cobro de venta', 250.50, 'Efectivo', 'Otra venta en efectivo', NULL, 1, '2026-01-09 00:36:49'),
(0, 'gasto', 'Compra de materiales', 50.00, 'Efectivo', 'Gastos operativos', NULL, 1, '2026-01-09 00:36:49'),
(0, 'gasto', 'Prueba de gasto', 25.00, 'Efectivo', 'Gasto de prueba desde script', NULL, 1, '2026-01-09 00:37:44'),
(0, 'ingreso', 'Cobro de venta', 400.00, 'Efectivo', '', 19, 4, '2026-01-10 15:31:02'),
(0, 'ingreso', 'Cobro de venta', 750.00, 'Efectivo', '', 10, 4, '2026-01-10 20:07:57'),
(0, 'ingreso', 'Cobro de venta', 350.00, 'Cheque', '', 16, 4, '2026-01-10 20:08:10'),
(0, 'ingreso', 'Cobro de venta', 350.00, 'Efectivo', '', 18, 4, '2026-01-10 20:08:31'),
(0, 'ingreso', 'Cobro de venta', 400.00, 'Cheque', '1', 22, 1, '2026-01-10 21:14:20'),
(0, 'ingreso', 'Cobro de venta', 400.00, 'Cheque', '', 23, 1, '2026-01-10 21:53:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('ENTRADA','SALIDA','AJUSTE') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(12,2) DEFAULT NULL,
  `origen` enum('COMPRA','VENTA','DEVOLUCION','AJUSTE') NOT NULL,
  `origen_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `es_reverso` tinyint(1) NOT NULL DEFAULT 0,
  `movimiento_ref_id` int(11) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_inventario`
--

INSERT INTO `movimientos_inventario` (`id`, `producto_id`, `tipo`, `cantidad`, `costo_unitario`, `origen`, `origen_id`, `usuario_id`, `es_reverso`, `movimiento_ref_id`, `motivo`, `created_at`) VALUES
(1, 3, 'ENTRADA', 2.00, 5.00, 'COMPRA', 4, 1, 0, NULL, 'BACKFILL COMPRA', '2025-11-26 17:07:44'),
(2, 3, 'ENTRADA', 10.00, 5.00, 'COMPRA', 5, 1, 0, NULL, 'BACKFILL COMPRA', '2025-11-26 17:08:56'),
(3, 3, 'ENTRADA', 5.00, 10.00, 'COMPRA', 6, 1, 0, NULL, 'BACKFILL COMPRA', '2025-11-28 05:13:59'),
(4, 8, 'ENTRADA', 500.00, 0.05, 'COMPRA', 7, 1, 0, NULL, 'BACKFILL COMPRA', '2025-12-17 16:50:32'),
(5, 9, 'ENTRADA', 8.00, 50.00, 'COMPRA', 8, 1, 0, NULL, 'BACKFILL COMPRA', '2025-12-18 02:40:24'),
(6, 9, 'ENTRADA', 1.00, 20.00, 'COMPRA', 9, 1, 0, NULL, 'BACKFILL COMPRA', '2025-12-18 02:42:38'),
(7, 8, 'ENTRADA', 500.00, 2.00, 'COMPRA', 9, 1, 0, NULL, 'BACKFILL COMPRA', '2025-12-18 02:42:38'),
(9, 8, 'SALIDA', 10.00, 1.50, 'VENTA', 12, 1, 0, NULL, 'Venta al cliente', '2025-12-18 20:15:54'),
(0, 9, 'SALIDA', 1.00, 0.00, 'VENTA', 22, 1, 0, NULL, 'Venta manual #22', '2026-01-11 03:13:24'),
(0, 9, 'SALIDA', 1.00, 0.00, 'VENTA', 23, 1, 0, NULL, 'Venta manual #23', '2026-01-11 03:51:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `sku` varchar(60) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo_producto` enum('UNIDAD','MISC') NOT NULL DEFAULT 'UNIDAD',
  `codigo_barra` varchar(60) DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `subcategoria_id` int(11) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `unidad_medida_id` smallint(6) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `costo_actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 5.00,
  `unidad_medida` varchar(20) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_path` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `costo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `sku`, `nombre`, `tipo_producto`, `codigo_barra`, `categoria_id`, `subcategoria_id`, `marca_id`, `unidad_medida_id`, `precio_venta`, `costo_actual`, `stock`, `stock_minimo`, `unidad_medida`, `descripcion`, `imagen_path`, `activo`, `created_at`, `updated_at`, `estado`, `costo`) VALUES
(4, 'Comer_sosa_31864', 'Martillo de acero 12\"', 'MISC', '......................', 1, NULL, 1, 1, 170.00, 30.00, 0.00, 3.00, NULL, '', NULL, 1, '2025-12-12 08:18:30', '2026-01-07 20:28:48', 'INACTIVO', 0.00),
(5, 'Comer_sosa_89247', 'Martillo de acero 12\"', 'MISC', '8888888888888888888', 1, NULL, 1, 1, 170.00, 30.00, 0.00, 3.00, NULL, '', NULL, 1, '2025-12-12 08:19:27', '2026-01-07 20:28:48', 'ACTIVO', 0.00),
(7, 'Comer_sosa_70645', 'Martillo de acero 12\"', 'MISC', NULL, 1, NULL, 1, 1, 38.00, 17.00, 0.00, 2.00, NULL, '', NULL, 1, '2025-12-12 08:51:19', '2026-01-07 20:28:48', 'ACTIVO', 0.00),
(8, 'Comer_sosa_75482', 'torniloossssssss', 'MISC', NULL, 8, NULL, 2, 1, 1.50, 2.00, 0.00, 100.00, NULL, '', NULL, 1, '2025-12-17 16:30:05', '2026-01-07 20:28:48', 'ACTIVO', 0.00),
(9, 'Comer_sosa_65476', 'barreno hidradulico con maartillo', 'MISC', 'wsqw31q', 9, NULL, 2, 1, 400.00, 0.00, 0.00, 10.00, NULL, '', NULL, 1, '2025-12-18 02:32:38', '2026-01-11 03:51:31', 'ACTIVO', 0.00),
(10, 'Comer_sosa_90937', 'PRUEBA', 'MISC', 'PRUEBA', 1, NULL, 1, 1, 10.00, 5.00, 0.00, 2.00, NULL, '', NULL, 1, '2025-12-18 07:58:25', '2026-01-07 20:28:48', 'ACTIVO', 0.00),
(13, 'Comer_sosa_58469', 'canal', 'MISC', NULL, 9, NULL, 2, 1, 150.00, 0.00, 0.00, 5.00, NULL, '', NULL, 1, '2025-12-18 19:39:20', '2026-01-07 20:28:48', 'ACTIVO', 0.00),
(14, 'COMER_SOSA_77576', 'tornilla', 'MISC', '', 9, NULL, 2, 1, 0.00, 0.00, 0.00, 5.00, NULL, '', NULL, 1, '2025-12-18 19:46:31', '2026-01-11 00:34:33', 'INACTIVO', 0.00),
(16, 'Comer_sosa_22476', 'JJ', 'MISC', NULL, 16, NULL, 2, 1, 10.00, 5.00, 0.00, 5.00, NULL, '', NULL, 1, '2026-01-06 22:08:26', '2026-01-07 20:28:48', 'ACTIVO', 0.00),
(17, 'Comer_sosa_90290', 'desarmador', 'MISC', '1231321313', 9, NULL, 2, 1, 50.00, 14.00, 0.00, 5.00, NULL, '', NULL, 1, '2026-01-07 07:51:30', '2026-01-07 19:36:33', 'ACTIVO', 0.00),
(18, 'Comer_sosa_98899', 'codos pvc  pulagas', 'MISC', NULL, 13, NULL, 1, 1, 10.00, 4.50, 0.00, 5.00, NULL, '', NULL, 1, '2026-01-07 15:15:22', '2026-01-07 19:36:33', 'ACTIVO', 0.00),
(19, 'Comer_sosa_12001', 'codos pvc  pulagas 3333', 'MISC', NULL, 13, NULL, 1, 1, 50.00, 8.50, 0.00, 5.00, NULL, '', NULL, 1, '2026-01-07 18:11:20', '2026-01-07 19:36:33', 'ACTIVO', 0.00),
(20, 'Comer_sosa_59591', 'codos pvc  pulagas cdwsss', 'MISC', NULL, 9, NULL, 1, 1, 56.00, 25.00, 0.00, 11.00, NULL, '', NULL, 1, '2026-01-07 18:29:04', '2026-01-07 19:36:33', 'ACTIVO', 0.00),
(21, 'Comer_sosa_87477', 'LATA EN AEROSOL', 'UNIDAD', NULL, 13, NULL, 2, 1, 23.00, 12.00, 0.00, 2.00, NULL, '', NULL, 1, '2026-01-07 19:40:20', NULL, 'ACTIVO', 0.00),
(32, 'COMER_SOSA_90897', 'Pruebá 3.1', 'UNIDAD', '', 12, NULL, 2, 1, 0.00, 0.00, 0.00, 5.00, NULL, '', NULL, 1, '2026-01-10 21:55:09', '2026-01-11 00:36:35', 'ACTIVO', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_series`
--

CREATE TABLE `productos_series` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `numero_serie` varchar(100) NOT NULL,
  `compra_id` int(11) DEFAULT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `estado` enum('EN_STOCK','VENDIDO','DEFECTUOSO','DEVUELTO') NOT NULL DEFAULT 'EN_STOCK',
  `fecha_ingreso` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_venta` timestamp NULL DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos_series`
--

INSERT INTO `productos_series` (`id`, `producto_id`, `numero_serie`, `compra_id`, `venta_id`, `estado`, `fecha_ingreso`, `fecha_venta`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 21, 'SERIE-PRUEBA-001', NULL, NULL, 'EN_STOCK', '2026-01-10 23:31:59', NULL, 'Serie agregada manualmente', '2026-01-10 23:31:59', NULL),
(2, 21, 'SERIE-PRUEBA-002', NULL, NULL, 'EN_STOCK', '2026-01-10 23:31:59', NULL, 'Serie agregada manualmente', '2026-01-10 23:31:59', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_series`
--

CREATE TABLE `producto_series` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `serie` varchar(100) NOT NULL,
  `estado` enum('DISPONIBLE','VENDIDO','DA??ADO') DEFAULT 'DISPONIBLE',
  `venta_id` int(11) DEFAULT NULL COMMENT 'ID de la venta si fue vendido',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nit` varchar(30) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nit`, `nombre`, `direccion`, `telefono`, `correo`, `activo`, `created_at`) VALUES
(1, 'CF', 'Proveedor demo', 'Dirección demo', '00000000', 'demo@correo.com', 0, '2025-11-26 16:49:47'),
(2, '7351278891', 'Prueba', 'guatemala', '77841628', 'vinicio@gmail.com', 1, '2025-12-17 15:38:22'),
(3, '1', 'PRUE A', 'A', '30619604', 'BRENNERGRANADOS@GMAIL.COM', 1, '2026-01-06 23:25:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias`
--

CREATE TABLE `subcategorias` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `margen_porcentaje` decimal(5,2) DEFAULT NULL COMMENT 'Margen en porcentaje para calcular precio venta',
  `margen_fijo` decimal(10,2) DEFAULT NULL COMMENT 'Margen fijo en quetzales',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `abreviatura` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`id`, `nombre`, `abreviatura`) VALUES
(1, 'Unidad', 'u');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('ADMIN','VENDEDOR') NOT NULL DEFAULT 'VENDEDOR',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `foto`, `password_hash`, `rol`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Administrador Principal', 'admin@comercializadora.com', NULL, '$2y$10$JHPpxv312BqPC2CeV0XfheJtefNEXgQw4rvstDrptBmT24vVYOGWS', 'ADMIN', 1, '2025-11-24 19:49:05', '2025-12-19 04:09:30'),
(2, 'Vendedor 1', 'vendedor@comercializadora.com', NULL, '$2y$10$qff3kfjQqhVQFDL2BiocLu5mt.y8NfuL/UjBKJvMiQJ/ahFjPMhtG', 'VENDEDOR', 0, '2025-11-24 19:49:05', '2026-01-07 00:55:53'),
(4, 'preuba', 'nestor123@gmail.com', NULL, '$2y$10$k4B9InSI7amXrziUp4p60eObXbhxgzGaj9a/FuWocHrL5PTPtCmlG', 'VENDEDOR', 1, '2025-12-19 03:43:08', '2025-12-19 04:12:24'),
(5, 'Jose perez', 'admin@demo.com', NULL, '$2y$10$PdV7G9yejGOywIbsK9OoHu8CEEsee3BwOVlFw3wGEDCpdwm2sJ7Tm', 'ADMIN', 1, '2025-12-19 04:00:06', '2026-01-10 23:48:17'),
(6, 'qqq', 'demo@ejemplo.com', NULL, '$2y$10$vb0RITc8vKmUDE.oJVAP4uMQHwmPu2jDV0TmQxBEO3Iou9jztqXJO', 'VENDEDOR', 1, '2026-01-11 00:21:00', '2026-01-11 00:21:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cotizacion_id` int(11) DEFAULT NULL,
  `fecha_venta` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('CONFIRMADA','ANULADA') NOT NULL DEFAULT 'CONFIRMADA',
  `metodo_pago` varchar(50) NOT NULL DEFAULT 'Efectivo',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_pagado` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `deuda_origen_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `numero_cheque` varchar(100) DEFAULT NULL,
  `numero_boleta` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id`, `cliente_id`, `usuario_id`, `cotizacion_id`, `fecha_venta`, `estado`, `metodo_pago`, `subtotal`, `total`, `total_pagado`, `observaciones`, `deuda_origen_id`, `created_at`, `updated_at`, `numero_cheque`, `numero_boleta`) VALUES
(2, 2, 1, 13, '2025-12-13 00:08:13', 'CONFIRMADA', 'Efectivo', 510.00, 510.00, 510.00, NULL, NULL, '2025-12-13 06:08:13', '2025-12-19 00:24:32', NULL, NULL),
(3, 3, 1, NULL, '2025-12-13 00:17:48', 'CONFIRMADA', 'Efectivo', 152.00, 152.00, 152.00, NULL, NULL, '2025-12-13 06:17:48', '2025-12-19 01:11:50', NULL, NULL),
(4, 2, 1, NULL, '2025-12-14 13:23:49', 'CONFIRMADA', 'Efectivo', 170.00, 170.00, 170.00, NULL, NULL, '2025-12-14 19:23:49', '2026-01-09 05:19:25', NULL, NULL),
(5, 3, 1, 15, '2025-12-17 20:45:33', 'CONFIRMADA', 'Efectivo', 350.00, 350.00, 350.00, NULL, NULL, '2025-12-18 02:45:33', '2026-01-09 05:21:00', NULL, NULL),
(7, 3, 1, NULL, '2025-12-17 21:39:24', 'CONFIRMADA', 'Efectivo', 350.00, 350.00, 360.50, NULL, NULL, '2025-12-18 03:39:24', '2026-01-09 05:25:20', NULL, NULL),
(9, 3, 1, NULL, '2025-12-18 03:30:40', 'CONFIRMADA', 'Efectivo', 1200.00, 1200.00, 1200.00, NULL, NULL, '2025-12-18 09:30:40', '2026-01-09 05:25:48', NULL, NULL),
(10, 3, 1, NULL, '2025-12-18 14:10:31', 'CONFIRMADA', 'Efectivo', 750.00, 750.00, 750.00, NULL, NULL, '2025-12-18 20:10:31', '2026-01-11 02:07:57', NULL, NULL),
(12, 3, 1, NULL, '2025-12-18 14:15:54', 'ANULADA', 'Efectivo', 15.00, 15.00, 0.00, NULL, NULL, '2025-12-18 20:15:54', '2026-01-06 17:50:08', NULL, NULL),
(16, 3, 1, 18, '2026-01-08 21:02:48', 'CONFIRMADA', 'Efectivo', 350.00, 350.00, 350.00, 'Convertida desde cotización #18', NULL, '2026-01-09 03:02:48', '2026-01-11 02:08:10', NULL, NULL),
(17, 4, 1, NULL, '2026-01-08 21:28:59', 'CONFIRMADA', 'Efectivo', 400.00, 400.00, 400.00, 'Venta generada automáticamente de Deuda #14', 14, '2026-01-09 03:28:59', NULL, NULL, NULL),
(18, 4, 1, 17, '2026-01-08 23:20:29', 'CONFIRMADA', 'Efectivo', 350.00, 350.00, 350.00, 'Convertida desde cotización #17', NULL, '2026-01-09 05:20:29', '2026-01-11 02:08:31', NULL, NULL),
(19, 4, 4, NULL, '2026-01-10 15:30:10', 'CONFIRMADA', 'PENDIENTE', 400.00, 400.00, 400.00, 'Venta manual desde formulario', NULL, '2026-01-10 21:30:10', '2026-01-10 21:31:02', NULL, NULL),
(22, 4, 1, NULL, '2026-01-10 21:13:24', 'CONFIRMADA', 'PENDIENTE', 400.00, 400.00, 400.00, 'Venta manual desde formulario', NULL, '2026-01-11 03:13:24', '2026-01-11 03:14:20', NULL, NULL),
(23, 3, 1, NULL, '2026-01-10 21:51:31', 'CONFIRMADA', 'Cheque', 400.00, 400.00, 400.00, 'Venta manual desde formulario', NULL, '2026-01-11 03:51:31', '2026-01-11 03:53:12', '1', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalle`
--

CREATE TABLE `venta_detalle` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta_detalle`
--

INSERT INTO `venta_detalle` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(3, 16, 9, 1.00, 350.00, 350.00),
(4, 17, 9, 1.00, 400.00, 400.00),
(5, 18, 9, 1.00, 350.00, 350.00),
(6, 19, 9, 1.00, 400.00, 400.00),
(9, 22, 9, 1.00, 400.00, 400.00),
(10, 23, 9, 1.00, 400.00, 400.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `deuda`
--
ALTER TABLE `deuda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_venta` (`venta_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos_series`
--
ALTER TABLE `productos_series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `idx_numero_serie` (`numero_serie`),
  ADD KEY `idx_producto_id` (`producto_id`),
  ADD KEY `idx_producto_estado` (`producto_id`,`estado`),
  ADD KEY `idx_compra` (`compra_id`),
  ADD KEY `idx_venta` (`venta_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venta` (`venta_id`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `deuda`
--
ALTER TABLE `deuda`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `productos_series`
--
ALTER TABLE `productos_series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD CONSTRAINT `venta_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `venta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
