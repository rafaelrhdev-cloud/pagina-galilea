<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit('No autorizado.');
}

$id = (int)($_POST['id'] ?? 0);
$accion = $_POST['accion'] ?? '';

if ($id > 0 && in_array($accion, ['confirmar', 'rechazar', 'eliminar'], true)) {
    if ($accion === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM citas WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    } else {
        $estado = $accion === 'confirmar' ? 'confirmada' : 'rechazada';
        $stmt = $conn->prepare("UPDATE citas SET estado = ? WHERE id = ?");
        $stmt->bind_param('si', $estado, $id);
        $stmt->execute();
    }
}

header('Location: dashboard.php');
exit;
