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

// Verificar acceso de rol
if (!isset($_SESSION['usuario']) || ($_SESSION['categoria'] !== 'medio' && $_SESSION['categoria'] !== 'avanzado')) {
    header('Location: ../frontend/index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña de Otros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Cambiar Contraseña de Otros Usuarios</h2>

        <!-- Botón Volver -->
        <div class="text-end mb-4">
            <button type="button" class="btn btn-secondary" onclick="location.href='../principal/principal_<?php echo $_SESSION['categoria']; ?>.php'">Volver a Principal</button>
        </div>

        <!-- Formulario de búsqueda -->
        <form id="searchUserForm" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" id="searchUser" placeholder="Buscar usuario por nombre o correo">
                <button type="button" class="btn btn-primary" onclick="buscarUsuariosParaPassword()">Buscar</button>
            </div>
        </form>

        <!-- Tabla para mostrar resultados -->
        <div id="userTable" class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Categoría</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="userResults">
                    <!-- Resultados dinámicos -->
                </tbody>
            </table>
        </div>

        <!-- Modal para cambiar contraseña -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordLabel">Cambiar Contraseña</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="changePasswordForm">
                        <div class="modal-body">
                            <input type="hidden" id="userIdToChange">
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="newPassword" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="cambiarPasswordUsuario()">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function buscarUsuariosParaPassword() {
            var query = $('#searchUser').val();
            $.ajax({
                url: '../backend/buscar_usuarios.php',
                type: 'POST',
                data: { query: query, context: 'password' },
                success: function(response) {
                    $('#userResults').html(response);
                },
                error: function() {
                    $('#modalMessage').text("Error en la conexión al servidor. Inténtelo de nuevo.");
                    var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                    notificationModal.show();
                }
            });
        }

        function abrirModalCambiarPassword(userId) {
            $('#userIdToChange').val(userId);
            var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            changePasswordModal.show();
        }

        function cambiarPasswordUsuario() {
            var userId = $('#userIdToChange').val();
            var newPassword = $('#newPassword').val();

            $.ajax({
                url: '../backend/cambiar_password_usuario.php',
                type: 'POST',
                data: { user_id: userId, new_password: newPassword },
                success: function(response) {
                    var modalMessage = '';
                    if (response === "success") {
                        modalMessage = "Contraseña actualizada correctamente.";

                        // Registrar log
                        $.post('', {
                            user_id: <?php echo $_SESSION['usuario_id']; ?>,
                            accion: `Cambiada la contraseña del usuario con ID ${userId}`
                        });
                    } else if (response === "user_not_found") {
                        modalMessage = "Usuario no encontrado.";
                    } else {
                        modalMessage = "Error al actualizar la contraseña.";
                    }
                    $('#modalMessage').text(modalMessage);

                    var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                    notificationModal.show();

                    var changePasswordModal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                    changePasswordModal.hide();
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
