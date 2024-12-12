<?php
session_start();
include 'conexion.php';

// Función para registrar logs
function registrarLog($conexion, $usuario_id, $accion) {
    $consulta = "INSERT INTO logs (usuario_id, accion) VALUES (?, ?)";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("is", $usuario_id, $accion); // "i" para INT, "s" para STRING
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['usuario']) && isset($_POST['password'])) {
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $password = $conexion->real_escape_string($_POST['password']);

    // Consulta para obtener los datos del usuario
    $consulta = "SELECT u.id, u.password, c.nombre AS categoria
                 FROM usuarios u
                 JOIN categoria c ON u.categoria_id = c.id
                 WHERE u.nombre = '$usuario'";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        $usuario_datos = $resultado->fetch_assoc();

        if (password_verify($password, $usuario_datos['password'])) {
            // Iniciar sesión
            $_SESSION['usuario'] = $usuario;
            $_SESSION['categoria'] = $usuario_datos['categoria'];
            $_SESSION['usuario_id'] = $usuario_datos['id'];

            // Registrar el log del inicio de sesión
            $accion = "Inicio de sesión exitoso";
            registrarLog($conexion, $_SESSION['usuario_id'], $accion);

            // Redirigir según el rol
            if ($usuario_datos['categoria'] === 'avanzado') echo 1;
            elseif ($usuario_datos['categoria'] === 'medio') echo 2;
            elseif ($usuario_datos['categoria'] === 'básico') echo 3;
        } else {
            echo 0; // Contraseña incorrecta
        }
    } else {
        echo 4; // Usuario no encontrado
    }
}
?>
