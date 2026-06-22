<?php
/**
 * EJECUTA ESTE ARCHIVO UNA SOLA VEZ desde tu navegador, por ejemplo:
 * https://tudominio.com/backend/setup_admin.php
 *
 * Crea (o actualiza) el usuario administrador del panel con la contraseña que escribas.
 * Cuando termines, BORRA este archivo del servidor por seguridad.
 */
require_once __DIR__ . '/config.php';

$mensaje = '';
$listo = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = $_POST['clave'] ?? '';

    if ($usuario === '' || strlen($clave) < 6) {
        $mensaje = 'El usuario no puede estar vacío y la contraseña debe tener al menos 6 caracteres.';
    } else {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO administradores (usuario, password_hash) VALUES (?, ?)
                                ON DUPLICATE KEY UPDATE password_hash = ?");
        $stmt->bind_param('sss', $usuario, $hash, $hash);
        if ($stmt->execute()) {
            $listo = true;
            $mensaje = 'Usuario administrador creado/actualizado correctamente. Ya puedes borrar este archivo (setup_admin.php) e iniciar sesión en backend/admin/login.php.';
        } else {
            $mensaje = 'Ocurrió un error al guardar. Revisa la conexión a la base de datos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear usuario administrador</title>
<style>
body{font-family:Arial,sans-serif;background:#F7F5EF;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
.box{background:#fff;padding:32px;border-radius:12px;max-width:400px;box-shadow:0 4px 20px rgba(0,0,0,.08);}
input{width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:6px;box-sizing:border-box;}
button{background:#828F73;color:#fff;border:0;padding:10px 18px;border-radius:6px;cursor:pointer;}
.ok{color:#3a6b3a;} .err{color:#a33;}
</style>
</head>
<body>
<div class="box">
<h2>Crear usuario administrador</h2>
<?php if ($mensaje): ?>
  <p class="<?= $listo ? 'ok' : 'err' ?>"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>
<?php if (!$listo): ?>
<form method="post">
  <label>Usuario</label>
  <input type="text" name="usuario" value="galilea" required>
  <label>Contraseña</label>
  <input type="password" name="clave" minlength="6" required>
  <button type="submit">Crear / actualizar</button>
</form>
<?php endif; ?>
</div>
</body>
</html>
