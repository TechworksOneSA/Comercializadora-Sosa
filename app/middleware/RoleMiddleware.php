<?php
class RoleMiddleware {
  public static function requireAdmin() {
    AuthMiddleware::requireLogin();
    if ($_SESSION["user"]["rol"] !== "ADMIN") {
      redirect('/pos');
    }
  }

  public static function requireVendedor() {
    AuthMiddleware::requireLogin();
    if ($_SESSION["user"]["rol"] !== "VENDEDOR") {
      redirect('/admin/dashboard');
    }
  }

  // Permite acceso a ADMIN y VENDEDOR
  public static function requireAdminOrVendedor() {
    AuthMiddleware::requireLogin();
    if (!in_array($_SESSION["user"]["rol"], ["ADMIN", "VENDEDOR"])) {
      redirect('/login');
    }
  }
}
