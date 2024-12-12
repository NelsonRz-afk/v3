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

// Verificar acceso de rol
if (!isset($_SESSION['usuario']) || $_SESSION['categoria'] !== 'avanzado') {
    die("No tiene permiso para realizar esta acción.");
}

// Verificar si se reciben los datos necesarios
if (isset($_POST['nombre'], $_POST['email'], $_POST['password'], $_POST['categoria'])) {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $email = $conexion->real_escape_string($_POST['email']);
    $password = $conexion->real_escape_string($_POST['password']);
    $categoria = intval($_POST['categoria']);

    // Verificar que el correo no esté ya registrado
    $consulta = "SELECT id FROM usuarios WHERE email = '$email'";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        echo "email_exists"; // El correo ya está registrado
    } else {
        // Encriptar la contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insertar nuevo usuario
        $insertar = "INSERT INTO usuarios (nombre, email, password, categoria_id) VALUES ('$nombre', '$email', '$password_hash', $categoria)";
        if ($conexion->query($insertar)) {
            // Obtener ID del usuario recién creado para el log
            $nuevo_usuario_id = $conexion->insert_id;

            // Registrar log
            $accion = "Creó un nuevo usuario con ID $nuevo_usuario_id y nombre $nombre";
            registrarLog($conexion, $_SESSION['usuario_id'], $accion);

            echo "success"; // Usuario creado correctamente
        } else {
            echo "error"; // Error al insertar el usuario
        }
    }
} else {
    echo "invalid_request"; // Datos incompletos o solicitud no válida
}
?>
