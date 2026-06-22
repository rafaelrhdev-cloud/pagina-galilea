<?php
session_start();
require_once __DIR__ . '/../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = $_POST['clave'] ?? '';

    $stmt = $conn->prepare("SELECT id, password_hash FROM administradores WHERE usuario = ?");
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $admin = $res->fetch_assoc();

    if ($admin && password_verify($clave, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_usuario'] = $usuario;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso · Panel de citas</title>
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-login-body">
  <div class="login-card">
    <h1>Panel de citas</h1>
    <p class="sub">Tu Bienestar Psicología</p>
    <?php if ($error): ?><p class="login-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
      <label>Usuario</label>
      <input type="text" name="usuario" required autofocus>
      <label>Contraseña</label>
      <input type="password" name="clave" required>
      <button type="submit">Entrar</button>
    </form>
  </div>
</body>
</html>
