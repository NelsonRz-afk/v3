<?php
session_start();
include 'conexion.php';

// Verificar acceso de rol
if (!isset($_SESSION['usuario']) || ($_SESSION['categoria'] !== 'medio' && $_SESSION['categoria'] !== 'avanzado')) {
    header('Location: ../frontend/index.php');
    exit();
}

// Verificar si se recibió la consulta y el contexto
if (isset($_POST['query']) && isset($_POST['context'])) {
    $query = $conexion->real_escape_string($_POST['query']);
    $context = $_POST['context']; // Determina el propósito (contraseña o eliminación)

    // Consulta SQL para buscar usuarios
    $consulta = "SELECT u.id, u.nombre, u.email, c.nombre AS categoria
                 FROM usuarios u
                 JOIN categoria c ON u.categoria_id = c.id
                 WHERE u.nombre LIKE '%$query%' OR u.email LIKE '%$query%'";

    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        while ($usuario = $resultado->fetch_assoc()) {
            echo "<tr>
                    <td>{$usuario['nombre']}</td>
                    <td>{$usuario['email']}</td>
                    <td>{$usuario['categoria']}</td>
                    <td>";
            if ($context === 'password') {
                echo "<button class='btn btn-primary btn-sm' onclick='abrirModalCambiarPassword({$usuario['id']})'>Cambiar Contraseña</button>";
            } elseif ($context === 'delete') {
                echo "<button class='btn btn-danger btn-sm' onclick='abrirModalEliminar({$usuario['id']})'>Eliminar</button>";
            }
            echo "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='4' class='text-center'>No se encontraron usuarios.</td></tr>";
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida.']);
    exit();
}
?>
