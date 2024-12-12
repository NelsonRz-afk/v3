<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Inicio de Sesión</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="button" class="btn btn-primary w-100" onclick="confirmar()">Iniciar Sesión</button>
                </form>
                <div id="result" class="mt-3"></div>
            </div>
        </div>
    </div>
    <script>



function confirmar() {
    var user = $('#usuario').val();
    var pass = $('#password').val();

    $.ajax({
        url: '../backend/validar.php', // Ruta al archivo validar.php
        type: 'POST',
        data: { usuario: user, password: pass },
        success: function(resp) {
            console.log("Respuesta del servidor:", resp); // Depuración
            resp = parseInt(resp); // Convertir la respuesta a número

            if (resp === 1) {
                location.href = '../principal/principal_avanzado.php';
            } else if (resp === 2) {
                location.href = '../principal/principal_medio.php';
            } else if (resp === 3) {
                location.href = '../principal/principal_basico.php';
            } else if (resp === 0) {
                $('#result').html("<div class='alert alert-danger'>Contraseña incorrecta</div>");
            } else if (resp === 4) {
                $('#result').html("<div class='alert alert-danger'>Usuario no encontrado</div>");
            } else {
                $('#result').html("<div class='alert alert-danger'>Error desconocido: " + resp + "</div>");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud Ajax:", error); // Depuración
            $('#result').html("<div class='alert alert-danger'>Error en la conexión al servidor</div>");
        }
    });
}




    </script>
</body>
</html>
