<?php
session_start();

require_once __DIR__ . "/../app/config/env.php";
require_once __DIR__ . "/../app/config/database.php";

require_once __DIR__ . "/../app/core/helpers.php";
require_once __DIR__ . "/../app/core/Router.php";
require_once __DIR__ . "/../app/core/Controller.php";
require_once __DIR__ . "/../app/core/Model.php";
require_once __DIR__ . "/../app/core/View.php";

require_once __DIR__ . "/../app/middleware/AuthMiddleware.php";
require_once __DIR__ . "/../app/middleware/RoleMiddleware.php";

require_once __DIR__ . "/../app/modules/auth/User.php";
require_once __DIR__ . "/../app/modules/auth/AuthController.php";
require_once __DIR__ . "/../app/modules/dashboard/DashboardController.php";
require_once __DIR__ . "/../app/modules/dashboard_vendedor/DashboardVendedorController.php";
require_once __DIR__ . "/../app/modules/productos/ProductosController.php";
require_once __DIR__ . "/../app/modules/compras/ComprasController.php";
require_once __DIR__ . "/../app/modules/proveedores/ProveedoresController.php";

require_once __DIR__ . "/../app/modules/catalogos/CategoriasMarcasController.php";
require_once __DIR__ . "/../app/modules/categorias/CategoriasModel.php";
require_once __DIR__ . "/../app/modules/marcas/MarcasModel.php";

require_once __DIR__ . "/../app/modules/ventas/VentasController.php";

require_once __DIR__ . "/../app/modules/clientes/ClientesController.php";
require_once __DIR__ . "/../app/modules/deudores/DeudoresController.php";



require_once __DIR__ . "/../app/modules/reportes/ReportesController.php";

require_once __DIR__ . "/../app/modules/pos/PosController.php";

require_once __DIR__ . "/../app/modules/caja/CajaController.php";
require_once __DIR__ . "/../app/modules/caja/CajaModel.php";

require_once __DIR__ . "/../app/modules/usuarios/UsuariosController.php";

$router = new Router();


$router->get("/", "AuthController@loginForm");
$router->get("/login", "AuthController@loginForm");
$router->post("/login", "AuthController@login");
$router->get("/logout", "AuthController@logout");

// Dashboard
$router->get("/admin/dashboard", "DashboardController@index");
$router->get("/admin/dashboard-vendedor", "DashboardVendedorController@index");

// PRODUCTOS
$router->get("/admin/productos",                "ProductosController@index");
$router->get("/admin/productos/crear",          "ProductosController@crear");
$router->get("/admin/productos/tabla", "ProductosController@tabla");
$router->post("/admin/productos/guardar",       "ProductosController@guardar");
$router->get("/admin/productos/editar/{id}",    "ProductosController@editar");
$router->post("/admin/productos/actualizar/{id}", "ProductosController@actualizar");
$router->get("/admin/productos/{id}/series",    "ProductosController@series");
$router->post("/admin/productos/desactivar/{id}", "ProductosController@desactivar");
$router->post("/admin/productos/activar/{id}",    "ProductosController@activar");
$router->post("/admin/productos/eliminarPermanente/{id}", "ProductosController@eliminarPermanente");


// COMPRAS
$router->get('/admin/compras',          'ComprasController@index');
$router->get('/admin/compras/crear',    'ComprasController@crear');
$router->post('/admin/compras/guardar', 'ComprasController@guardar');
$router->get('/admin/compras/editar/{id}', 'ComprasController@editar');
$router->post('/admin/compras/actualizar/{id}', 'ComprasController@actualizar');
$router->get('/admin/compras/eliminar/{id}', 'ComprasController@eliminar');
$router->get('/admin/compras/ver/{id}', 'ComprasController@ver');

// PROVEEDORES
$router->get('/admin/proveedores',          'ProveedoresController@index');
$router->get('/admin/proveedores/crear',    'ProveedoresController@crear');
$router->post('/admin/proveedores/guardar', 'ProveedoresController@guardar');
$router->get('/admin/proveedores/editar/{id}', 'ProveedoresController@editar');
$router->post('/admin/proveedores/actualizar/{id}', 'ProveedoresController@actualizar');
$router->post('/admin/proveedores/eliminar/{id}', 'ProveedoresController@eliminar');
$router->post('/admin/proveedores/cambiar-estado/{id}', 'ProveedoresController@cambiarEstado');

// CATEGORÍAS & MARCAS
$router->get('/admin/catalogos',                          'CategoriasMarcasController@index');
$router->post('/admin/catalogos/categorias/guardar',      'CategoriasMarcasController@guardarCategoria');
$router->post('/admin/catalogos/marcas/guardar',          'CategoriasMarcasController@guardarMarca');
$router->post('/admin/catalogos/categorias/cambiar-estado/{id}', 'CategoriasMarcasController@cambiarEstadoCategoria');
$router->post('/admin/catalogos/marcas/cambiar-estado/{id}',     'CategoriasMarcasController@cambiarEstadoMarca');

// VENTAS
$router->get("/admin/ventas",         "VentasController@index");
$router->get("/admin/ventas/crear",   "VentasController@crear");
$router->post("/admin/ventas/guardar", "VentasController@guardar");

// CLIENTES - CRUD completo
$router->get("/admin/clientes", "ClientesController@index");
$router->get("/admin/clientes/crear", "ClientesController@crear");
$router->post("/admin/clientes/guardar", "ClientesController@guardar");
$router->get("/admin/clientes/editar/{id}", "ClientesController@editar");
$router->post("/admin/clientes/actualizar/{id}", "ClientesController@actualizar");

// USUARIOS - Gestión de usuarios del sistema
$router->get("/admin/usuarios", "UsuariosController@index");
$router->get("/admin/usuarios/crear", "UsuariosController@crear");
$router->post("/admin/usuarios/guardar", "UsuariosController@guardar");
$router->get("/admin/usuarios/editar/{id}", "UsuariosController@editar");
$router->post("/admin/usuarios/actualizar/{id}", "UsuariosController@actualizar");
$router->post("/admin/usuarios/cambiar-estado/{id}", "UsuariosController@cambiarEstado");

// COTIZACIONES
require_once __DIR__ . "/../app/modules/cotizaciones/CotizacionesController.php";

$router->get("/admin/cotizaciones", "CotizacionesController@index");
$router->get("/admin/cotizaciones/crear", "CotizacionesController@crear");
$router->post("/admin/cotizaciones/guardar", "CotizacionesController@guardar");
$router->get("/admin/cotizaciones/ver/{id}", "CotizacionesController@ver");
$router->post("/admin/cotizaciones/convertir/{id}", "CotizacionesController@convertir");
$router->post("/admin/cotizaciones/eliminar/{id}", "CotizacionesController@eliminar");
$router->post("/admin/cotizaciones/limpiar-vencidas", "CotizacionesController@limpiarVencidas");

$router->get("/admin/ventas", "VentasController@index");
$router->get("/admin/ventas/crear", "VentasController@crear");
$router->post("/admin/ventas/guardar", "VentasController@guardar");
$router->get("/admin/ventas/ver", "VentasController@ver");
$router->post("/admin/ventas/anular", "VentasController@anular");
$router->post("/admin/cotizaciones/convertir", "VentasController@convertirDesdeCotizacion");

// DEUDORES
$router->get("/admin/deudores", "DeudoresController@index");
$router->get("/admin/deudores/crear", "DeudoresController@crear");
$router->post("/admin/deudores/guardar", "DeudoresController@guardar");
$router->get("/admin/deudores/ver", "DeudoresController@ver");
$router->post("/admin/deudores/registrarPago", "DeudoresController@registrarPago");
$router->post("/admin/deudores/ampliar", "DeudoresController@ampliar");
// Endpoint de depuración
$router->get("/admin/deudores/debug-clientes", "DeudoresController@debugClientes");



// CLIENTES - Búsqueda AJAX para ventas
$router->get("/admin/clientes/buscar", "ClientesController@buscar");
$router->post("/admin/clientes/crear-rapido", "ClientesController@crearRapido");



// REPORTES
$router->get("/admin/reportes", "ReportesController@index");
$router->get("/admin/reportes/ventas", "ReportesController@ventas");
$router->get("/admin/reportes/compras", "ReportesController@compras");
$router->get("/admin/reportes/inventario", "ReportesController@inventario");
$router->get("/admin/reportes/productos", "ReportesController@productos");
$router->get("/admin/reportes/balance", "ReportesController@balance");

// POS - PUNTO DE VENTA
$router->get("/admin/pos", "PosController@index");
$router->get("/admin/pos/cobrar/{id}", "PosController@cobrar");
$router->post("/admin/pos/registrar-cobro", "PosController@registrarCobro");

// CAJA - GESTIÓN DE MOVIMIENTOS Y GASTOS
$router->get("/admin/caja", "CajaController@index");
$router->get("/admin/caja/movimientos", "CajaController@movimientos");
$router->get("/admin/caja/nuevo-movimiento", "CajaController@nuevoMovimiento");
$router->post("/admin/caja/guardar-movimiento", "CajaController@guardarMovimiento");
$router->post("/admin/caja/eliminar-movimiento/{id}", "CajaController@eliminarMovimiento");

// API - Mantener sesión activa
$router->post("/api/mantener-sesion", "AuthController@mantenerSesion");

$router->dispatch();
