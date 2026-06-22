<?php
/**
 * Recibe una solicitud de cita desde el formulario público.
 * La cita se guarda como 'pendiente': SOLO se agenda en definitiva
 * cuando la psicóloga la confirme manualmente desde el panel.
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

function limpiar($v) {
    return trim(htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'));
}

$nombre    = limpiar($_POST['nombre'] ?? '');
$correo    = limpiar($_POST['correo'] ?? '');
$telefono  = limpiar($_POST['telefono'] ?? '');
$modalidad = limpiar($_POST['modalidad'] ?? '');
$servicio  = limpiar($_POST['servicio'] ?? '');
$fecha     = limpiar($_POST['fecha'] ?? '');
$hora      = limpiar($_POST['hora'] ?? '');
$mensaje   = limpiar($_POST['mensaje'] ?? '');
$acepto    = isset($_POST['acepto_aviso']) && $_POST['acepto_aviso'] === '1' ? 1 : 0;

$errores = [];
if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo no es válido.';
if ($telefono === '') $errores[] = 'El teléfono es obligatorio.';
if (!in_array($modalidad, ['presencial', 'online'], true)) $errores[] = 'Selecciona una modalidad válida.';
if ($servicio === '') $errores[] = 'Selecciona un servicio.';
if (!in_array($hora, horas_disponibles_array(), true)) $errores[] = 'Selecciona un horario válido (3pm a 8pm).';
if (!$acepto) $errores[] = 'Debes aceptar el aviso de tratamiento de datos.';

$fechaTs = DateTime::createFromFormat('Y-m-d', $fecha);
$hoy = new DateTime('today');
if (!$fechaTs || $fechaTs < $hoy) {
    $errores[] = 'Selecciona una fecha válida, a partir de hoy.';
}

if (!empty($errores)) {
    echo json_encode(['ok' => false, 'error' => implode(' ', $errores)]);
    exit;
}

// Revisar que el horario no esté ya pendiente o confirmado
$check = $conn->prepare("SELECT id FROM citas WHERE fecha = ? AND hora = ? AND estado IN ('pendiente','confirmada')");
$check->bind_param('ss', $fecha, $hora);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['ok' => false, 'error' => 'Ese horario ya no está disponible, por favor elige otro.']);
    exit;
}
$check->close();

$stmt = $conn->prepare("INSERT INTO citas (nombre, correo, telefono, modalidad, servicio, fecha, hora, mensaje, estado, acepto_aviso)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente', ?)");
$stmt->bind_param('ssssssssi', $nombre, $correo, $telefono, $modalidad, $servicio, $fecha, $hora, $mensaje, $acepto);

if ($stmt->execute()) {
    echo json_encode([
        'ok' => true,
        'mensaje' => 'Tu solicitud fue enviada. La Lic. Galilea López se pondrá en contacto contigo para confirmar tu cita; no está agendada todavía.',
    ]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Ese horario ya fue tomado, por favor elige otro.']);
}
$stmt->close();
