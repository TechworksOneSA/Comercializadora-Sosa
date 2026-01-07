<?php

class AuthController extends Controller
{
  public function loginForm()
  {
    $mensaje = $_SESSION['mensaje_timeout'] ?? null;
    unset($_SESSION['mensaje_timeout']);

    $this->view("modules/auth/views/login", [
      'mensaje_timeout' => $mensaje,
      'old' => [
        'email' => $_POST['email'] ?? ''
      ]
    ]);
  }

  public function login()
  {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
      return $this->view("modules/auth/views/login", [
        "error" => "Debe ingresar correo y contraseña.",
        "old" => ["email" => $email]
      ]);
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    // Nota: estándar recomendado es password_hash
    if (!$user || empty($user["password_hash"]) || !password_verify($password, $user["password_hash"])) {
      return $this->view("modules/auth/views/login", [
        "error" => "Credenciales inválidas.",
        "old" => ["email" => $email]
      ]);
    }

    // Verificar que el usuario esté activo
    if (!$user["activo"]) {
      return $this->view("modules/auth/views/login", [
        "error" => "Usuario no activo. Contacte al administrador del sistema.",
        "old" => ["email" => $email]
      ]);
    }

    $_SESSION["user"] = [
      "id" => $user["id"],
      "nombre" => $user["nombre"],
      "email" => $user["email"],
      "rol" => $user["rol"]
    ];

    // Timestamp de última actividad
    $_SESSION['ultima_actividad'] = time();

    // Redirect según rol
    if ($user["rol"] === "ADMIN") {
      redirect('/admin/dashboard');
    } elseif ($user["rol"] === "VENDEDOR") {
      redirect('/admin/dashboard-vendedor');
    } else {
      redirect('/admin/pos');
    }
  }

  public function logout()
  {
    session_destroy();
    redirect('/login');
  }

  public function mantenerSesion()
  {
    if (isset($_SESSION['user'])) {
      $_SESSION['ultima_actividad'] = time();
      header('Content-Type: application/json');
      echo json_encode(['success' => true, 'timestamp' => time()]);
    } else {
      http_response_code(401);
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    }
    exit;
  }
}
