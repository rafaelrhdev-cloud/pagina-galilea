<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$citas = $conn->query("SELECT * FROM citas ORDER BY (estado='pendiente') DESC, fecha ASC, hora ASC");

function badge($estado) {
    $clases = ['pendiente' => 'badge-pendiente', 'confirmada' => 'badge-confirmada', 'rechazada' => 'badge-rechazada'];
    $textos = ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'rechazada' => 'Rechazada'];
    return '<span class="badge ' . $clases[$estado] . '">' . $textos[$estado] . '</span>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel de citas · Tu Bienestar Psicología</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="admin-header">
  <div>
    <h1>Panel de citas</h1>
    <p>Bienvenida, <?= htmlspecialchars($_SESSION['admin_usuario']) ?></p>
  </div>
  <a href="logout.php" class="logout-link">Cerrar sesión</a>
</header>

<main class="admin-main">
  <p class="admin-help">
    Las citas <strong>pendientes</strong> aparecen primero. Confírmalas solo después de hablar
    personalmente con la persona. Una vez confirmadas, ese horario se bloquea en la página principal.
  </p>

  <table class="citas-table">
    <thead>
      <tr>
        <th>Estado</th>
        <th>Fecha / hora</th>
        <th>Nombre</th>
        <th>Contacto</th>
        <th>Servicio</th>
        <th>Modalidad</th>
        <th>Mensaje</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($citas && $citas->num_rows > 0): while ($c = $citas->fetch_assoc()): ?>
      <tr>
        <td><?= badge($c['estado']) ?></td>
        <td><?= htmlspecialchars(date('d/m/Y', strtotime($c['fecha']))) ?><br><?= substr($c['hora'],0,5) ?> hrs</td>
        <td><?= htmlspecialchars($c['nombre']) ?></td>
        <td><?= htmlspecialchars($c['correo']) ?><br><?= htmlspecialchars($c['telefono']) ?></td>
        <td><?= htmlspecialchars($c['servicio']) ?></td>
        <td><?= $c['modalidad'] === 'presencial' ? 'Presencial' : 'En línea' ?></td>
        <td><?= htmlspecialchars($c['mensaje']) ?></td>
        <td class="acciones">
          <?php if ($c['estado'] === 'pendiente'): ?>
            <form method="post" action="actions.php" style="display:inline">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <input type="hidden" name="accion" value="confirmar">
              <button class="btn-confirmar" type="submit">Confirmar</button>
            </form>
            <form method="post" action="actions.php" style="display:inline">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <input type="hidden" name="accion" value="rechazar">
              <button class="btn-rechazar" type="submit">Rechazar</button>
            </form>
          <?php endif; ?>
          <form method="post" action="actions.php" style="display:inline" onsubmit="return confirm('¿Eliminar esta cita?');">
            <input type="hidden" name="id" value="<?= $c['id'] ?>">
            <input type="hidden" name="accion" value="eliminar">
            <button class="btn-eliminar" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="8" class="vacio">Todavía no hay citas registradas.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</main>
</body>
</html>
