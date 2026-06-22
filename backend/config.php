<?php
/**
 * Configuración de conexión a la base de datos.
 * Rellena estos 4 datos con los que te dé tu proveedor de hosting
 * (cPanel -> "Bases de datos MySQL" suele mostrarlos).
 */
$DB_HOST = 'localhost';
$DB_NAME = 'tubienestar';
$DB_USER = 'tubienestar_user';
$DB_PASS = 'CAMBIA_ESTA_CONTRASENA';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    die('No se pudo conectar a la base de datos. Verifica backend/config.php.');
}
$conn->set_charset('utf8mb4');

// Horario de atención: citas posibles de 3pm a 8pm, cada hora.
$HORAS_DISPONIBLES = ['15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];

function horas_disponibles_array() {
    global $HORAS_DISPONIBLES;
    return $HORAS_DISPONIBLES;
}
