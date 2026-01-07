<?php
// Se asume que helpers.php ya está cargado globalmente
$mensaje_timeout = $mensaje_timeout ?? null;
$error = $error ?? null;
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

  <title>Login - Comercializadora Sosa</title>
</head>

<body class="auth-page">

  <div class="auth-card">

    <div class="auth-logo-wrap">
      <img
        src="<?= asset('img/logo_sosa.png') ?>"
        alt="Comercializadora Sosa"
        class="auth-logo" />
    </div>

    <h1 class="auth-title">POS Ferretería</h1>
    <p class="auth-subtitle">Acceso para Administrador y Vendedor</p>

    <?php if (!empty($mensaje_timeout)): ?>
      <div class="auth-error" style="background-color: #fef3c7; color: #92400e; border-left: 4px solid #f59e0b;">
        ⏱️ <?= e($mensaje_timeout) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="auth-error">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form action="<?= url('/login') ?>" method="POST">

      <div class="auth-field">
        <label>Correo</label>
        <input
          name="email"
          type="email"
          required
          class="auth-input"
          placeholder="email@email.com"
          value="<?= e($old['email'] ?? '') ?>" />
      </div>

      <div class="auth-field" style="margin-top:12px;">
        <label>Contraseña</label>
        <input name="password" type="password" required class="auth-input" placeholder="********" />
      </div>

      <button type="submit" class="auth-btn">Entrar</button>

      <div class="auth-footer">
        Comercializadora Sosa
        <div class="auth-badge">Sistema Interno</div>
      </div>

    </form>
  </div>

</body>

</html>
