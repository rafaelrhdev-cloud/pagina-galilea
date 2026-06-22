<?php
/**
 * Devuelve en JSON los horarios ya ocupados (confirmados o pendientes)
 * de los próximos 30 días, para bloquearlos en el formulario público.
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$sql = "SELECT fecha, hora, estado FROM citas
        WHERE fecha >= CURDATE() AND fecha <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND estado IN ('confirmada','pendiente')";
$result = $conn->query($sql);

$ocupadas = [];
while ($result && ($row = $result->fetch_assoc())) {
    $ocupadas[] = [
        'fecha' => $row['fecha'],
        'hora'  => substr($row['hora'], 0, 5),
        'estado' => $row['estado'], // 'confirmada' = ya no se puede agendar, 'pendiente' = en espera de respuesta
    ];
}

echo json_encode([
    'ok' => true,
    'horas_disponibles' => horas_disponibles_array(),
    'ocupadas' => $ocupadas,
]);
