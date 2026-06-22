-- Esquema de base de datos para "Tu Bienestar Psicología"
-- Importa este archivo en phpMyAdmin o por consola: mysql -u usuario -p nombre_bd < schema.sql

CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    modalidad ENUM('presencial', 'online') NOT NULL,
    servicio VARCHAR(120) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    mensaje TEXT NULL,
    estado ENUM('pendiente', 'confirmada', 'rechazada') NOT NULL DEFAULT 'pendiente',
    acepto_aviso TINYINT(1) NOT NULL DEFAULT 0,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unico_horario_activo (fecha, hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- NOTA: el usuario administrador NO se crea aquí porque la contraseña debe quedar
-- correctamente cifrada por PHP en tu propio servidor. Usa backend/setup_admin.php
-- una sola vez (instrucciones en README.md) para crear el usuario "galilea" con tu contraseña.

-- Citas de EJEMPLO para que puedas ver cómo se comporta el sistema (puedes borrarlas desde el panel)
INSERT INTO citas (nombre, correo, telefono, modalidad, servicio, fecha, hora, mensaje, estado, acepto_aviso) VALUES
('Cita de ejemplo (pendiente)', 'ejemplo@correo.com', '4270000000', 'presencial', 'Terapia Individual', CURDATE() + INTERVAL 2 DAY, '16:00:00', 'Cita de prueba, puedes eliminarla.', 'pendiente', 1),
('Cita de ejemplo (confirmada)', 'ejemplo2@correo.com', '4270000001', 'online', 'Terapia Individual', CURDATE() + INTERVAL 3 DAY, '18:00:00', 'Cita de prueba, puedes eliminarla.', 'confirmada', 1);
