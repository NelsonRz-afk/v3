<?php
session_start();
include 'conexion.php';

// Función para registrar logs
function registrarLog($conexion, $usuario_id, $accion) {
    $consulta = "INSERT INTO logs (usuario_id, accion) VALUES (?, ?)";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("is", $usuario_id, $accion); // "i" para INT, "s" para STRING
    if (!$stmt->execute()) {
        error_log("Error al registrar log: " . $stmt->error);
    }
    $stmt->close();
}

// Verificar acceso de rol
if (!isset($_SESSION['usuario']) || ($_SESSION['categoria'] !== 'medio' && $_SESSION['categoria'] !== 'avanzado')) {
    die("No tiene permiso para realizar esta acción.");
}

// Verificar si se reciben los datos necesarios
if (isset($_POST['user_id']) && isset($_POST['new_password'])) {
    $user_id = intval($_POST['user_id']);
    $new_password = $conexion->real_escape_string($_POST['new_password']);

    // Verificar que el usuario existe
    $consulta = "SELECT id FROM usuarios WHERE id = $user_id";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        // Generar el hash de la nueva contraseña
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $actualizar = "UPDATE usuarios SET password = ? WHERE id = ?";
        $stmt = $conexion->prepare($actualizar);
        $stmt->bind_param("si", $password_hash, $user_id);

        if ($stmt->execute()) {
            // Registrar log
            $accion = "Se cambió la contraseña del usuario con ID $user_id";
            registrarLog($conexion, $_SESSION['usuario_id'], $accion);

            echo "success"; // Contraseña actualizada correctamente
        } else {
            echo "error"; // Error al actualizar la contraseña
        }
        $stmt->close();
    } else {
        echo "user_not_found"; // Usuario no encontrado
    }
} else {
    echo "invalid_request"; // Solicitud no válida
}
?>
