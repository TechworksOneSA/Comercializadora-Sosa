<?php
class AuthMiddleware {

  /**
   * Tiempo máximo de inactividad en segundos (5 segundos para prueba)
   */
  private const TIMEOUT_INACTIVIDAD = 900; // 15 minutos

  public static function requireLogin() {
    if (empty($_SESSION["user"])) {
      header("Location: /login");
      exit;
    }

    // Verificar timeout por inactividad
    self::verificarTimeout();

    // Actualizar última actividad
    self::actualizarUltimaActividad();
  }

  /**
   * Verificar si la sesión ha expirado por inactividad
   */
  private static function verificarTimeout() {
    if (isset($_SESSION['ultima_actividad'])) {
      $tiempoInactivo = time() - $_SESSION['ultima_actividad'];

      if ($tiempoInactivo > self::TIMEOUT_INACTIVIDAD) {
        // Cerrar sesión por inactividad
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['mensaje_timeout'] = 'Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente.';
        header("Location: /login");
        exit;
      }
    }
  }

  /**
   * Actualizar el timestamp de última actividad
   */
  private static function actualizarUltimaActividad() {
    $_SESSION['ultima_actividad'] = time();
  }
}
