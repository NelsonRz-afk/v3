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
if (!isset($_SESSION['usuario']) || $_SESSION['categoria'] !== 'avanzado') {
    die("No tiene permiso para realizar esta acción.");
}

// Verificar si se recibe el ID del usuario
if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Verificar que el usuario no se está eliminando a sí mismo
    if ($_SESSION['usuario_id'] === $user_id) {
        echo "no_self_delete"; // No puede eliminarse a sí mismo
        exit();
    }

    // Verificar que el usuario existe
    $consulta = "SELECT id FROM usuarios WHERE id = $user_id";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        // Eliminar usuario
        $eliminar = "DELETE FROM usuarios WHERE id = $user_id";
        if ($conexion->query($eliminar)) {
            // Registrar log
            $accion = "Se eliminó al usuario con ID $user_id";
            registrarLog($conexion, $_SESSION['usuario_id'], $accion);

            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "user_not_found"; // Usuario no encontrado
    }
} else {
    echo "invalid_request"; // Solicitud no válida
}
?>
