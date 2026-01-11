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


      <div class="auth-field" style="margin-top:12px; position:relative;">
        <label>Contraseña</label>
        <input id="passwordInput" name="password" type="password" required class="auth-input" placeholder="********" style="padding-right:38px;" />
        <button type="button" id="togglePassword" style="position:absolute; right:8px; top:34px; background:none; border:none; cursor:pointer; padding:0; display:flex; align-items:center;" tabindex="-1" aria-label="Mostrar u ocultar contraseña">
          <span id="eyeIcon" style="display:flex; align-items:center;">
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:block;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.362-2.675A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.043 5.197M15 12a3 3 0 11-6 0 3 3 0 016 0zm-6.364 6.364L19.07 4.93" />
            </svg>
          </span>
        </button>
      </div>

      <button type="submit" class="auth-btn">Entrar</button>

      <div class="auth-footer">
        Comercializadora Sosa
        <div class="auth-badge">Sistema Interno</div>
      </div>

    </form>
  </div>


  <script>
    const passwordInput = document.getElementById('passwordInput');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    if (togglePassword && passwordInput) {
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      togglePassword.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        if (isPassword) {
          eyeOpen.style.display = 'none';
          eyeClosed.style.display = 'block';
        } else {
          eyeOpen.style.display = 'block';
          eyeClosed.style.display = 'none';
        }
      });
    }
  </script>

</body>

</html>
