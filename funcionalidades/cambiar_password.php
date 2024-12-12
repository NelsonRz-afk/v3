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


// Función para normalizar nombres de categoría y dejar todo sin tildes
function normalizarCategoria($categoria) {
    // Convertir a minúsculas y asegurar UTF-8
    $categoria = mb_strtolower($categoria, 'UTF-8');
    
    // Reemplazar caracteres especiales usando strtr (más rápido y compatible)
    $categoria = strtr($categoria, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ñ' => 'n'
    ]);
    
    return $categoria;
}


if (!isset($_SESSION['usuario'])) {
    header('Location: ../frontend/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $password_actual = $conexion->real_escape_string($_POST['password_actual']);
    $password_nuevo = $conexion->real_escape_string($_POST['password_nuevo']);

    // Verificar contraseña actual
    $consulta = "SELECT password FROM usuarios WHERE id = $usuario_id";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password_actual, $usuario['password'])) {
            // Actualizar contraseña
            $password_hash = password_hash($password_nuevo, PASSWORD_BCRYPT);
            $actualizar = "UPDATE usuarios SET password = '$password_hash' WHERE id = $usuario_id";

            if ($conexion->query($actualizar)) {
                // Registrar log del cambio de contraseña
                $accion = "Cambió su propia contraseña";
                registrarLog($conexion, $usuario_id, $accion);

                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "wrong_password";
        }
    } else {
        echo "user_not_found";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Cambiar Contraseña</h2>
        <form id="changePasswordForm">
            <div class="mb-3">
                <label for="password_actual" class="form-label">Contraseña Actual</label>
                <input type="password" class="form-control" id="password_actual" name="password_actual" required>
            </div>
            <div class="mb-3">
                <label for="password_nuevo" class="form-label">Nueva Contraseña</label>
                <input type="password" class="form-control" id="password_nuevo" name="password_nuevo" required>
            </div>
            <button type="button" class="btn btn-primary w-100" onclick="cambiarPassword()">Actualizar Contraseña</button>
            <button type="button" class="btn btn-secondary w-100 mt-2" onclick="location.href='../principal/<?php echo 'principal_' . normalizarCategoria($_SESSION['categoria']) . '.php'; ?>'"> Volver </button>
        </form>
    </div>

    <!-- Modal para notificaciones -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Notificación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalMessage">
                    <!-- Mensaje dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cambiarPassword() {
            var passwordActual = $('#password_actual').val();
            var passwordNuevo = $('#password_nuevo').val();

            $.ajax({
                url: 'cambiar_password.php',
                type: 'POST',
                data: {
                    password_actual: passwordActual,
                    password_nuevo: passwordNuevo
                },
                success: function(response) {
                    var modalMessage = '';
                    if (response === "success") {
                        modalMessage = "Contraseña actualizada correctamente.";
                    } else if (response === "wrong_password") {
                        modalMessage = "La contraseña actual no es correcta.";
                    } else if (response === "user_not_found") {
                        modalMessage = "No se encontró el usuario.";
                    } else {
                        modalMessage = "Hubo un error al actualizar la contraseña. Inténtelo de nuevo.";
                    }
                    $('#modalMessage').text(modalMessage);
                    var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                    notificationModal.show();
                },
                error: function() {
                    $('#modalMessage').text("Error en la conexión al servidor. Inténtelo de nuevo.");
                    var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                    notificationModal.show();
                }
            });
        }
    </script>
</body>
</html>
