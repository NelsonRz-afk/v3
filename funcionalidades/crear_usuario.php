<?php
session_start();
include '../backend/conexion.php';

// Verificar acceso de rol
if (!isset($_SESSION['usuario']) || $_SESSION['categoria'] !== 'avanzado') {
    header('Location: ../frontend/index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Crear Nuevo Usuario</h2>

        <!-- Botón Volver -->
        <div class="text-end mb-4">
            <button type="button" class="btn btn-secondary" onclick="location.href='../principal/principal_avanzado.php'">Volver a Principal</button>
        </div>

        <!-- Formulario para crear usuario -->
        <form id="createUserForm">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del usuario" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Correo del usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <option value="">Seleccione una categoría</option>
                    <option value="1">Básico</option>
                    <option value="2">Medio</option>
                    <option value="3">Avanzado</option>
                </select>
            </div>
            <button type="button" class="btn btn-primary w-100" onclick="crearUsuario()">Crear Usuario</button>
        </form>

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
        function crearUsuario() {
            var nombre = $('#nombre').val();
            var email = $('#email').val();
            var password = $('#password').val();
            var categoria = $('#categoria').val();

            if (!nombre || !email || !password || !categoria) {
                $('#modalMessage').text("Todos los campos son obligatorios.");
                var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                notificationModal.show();
                return;
            }

            $.ajax({
                url: '../backend/procesar_creacion_usuario.php',
                type: 'POST',
                data: {
                    nombre: nombre,
                    email: email,
                    password: password,
                    categoria: categoria
                },
                success: function(response) {
                    if (response === "success") {
                        $('#modalMessage').text("Usuario creado correctamente.");
                        $('#createUserForm')[0].reset(); // Limpiar formulario
                    } else if (response === "email_exists") {
                        $('#modalMessage').text("El correo ya está registrado.");
                    } else {
                        $('#modalMessage').text("Error al crear el usuario: " + response);
                    }
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
