<?php
session_start();
include '../backend/conexion.php';

// Función para registrar logs
function registrarLog($conexion, $usuario_id, $accion) {
    $consulta = "INSERT INTO logs (usuario_id, accion) VALUES (?, ?)";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("is", $usuario_id, $accion); // "i" para INT, "s" para STRING
    $stmt->execute();
    $stmt->close();
}

// Registrar log si el usuario está autenticado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $accion = "Cerró sesión";
    registrarLog($conexion, $usuario_id, $accion);
}

// Destruir todas las sesiones activas
session_unset();
session_destroy();

// Redirigir al formulario de login
header('Location: ../frontend/index.php');
exit();
?>
