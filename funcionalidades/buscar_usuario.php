<?php
session_start();
include '../backend/conexion.php';

// Verificar acceso de rol
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['categoria'], ['medio', 'avanzado'])) {
    echo json_encode(['status' => 'error', 'message' => 'No tiene permiso para realizar esta acción.']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Buscar Usuarios</h2>

        <!-- Botón Volver -->
        <div class="text-end mb-4">
            <button type="button" class="btn btn-secondary" onclick="location.href='../principal/principal_<?php echo $_SESSION['categoria']; ?>.php'">Volver a Principal</button>
        </div>

        <!-- Formulario de búsqueda -->
        <form id="searchUserForm" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" id="searchUser" placeholder="Buscar usuario por nombre o correo">
                <button type="button" class="btn btn-primary" onclick="buscarUsuarios()">Buscar</button>
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
                    </tr>
                </thead>
                <tbody id="userResults">
                    <!-- Resultados dinámicos -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function buscarUsuarios() {
            var query = $('#searchUser').val();
            $.ajax({
                url: '../backend/buscar_usuarios.php',
                type: 'POST',
                data: { query: query, context: 'view' }, // Contexto para solo visualizar
                success: function(response) {
                    $('#userResults').html(response);
                },
                error: function() {
                    alert("Error en la conexión al servidor. Inténtelo de nuevo.");
                }
            });
        }
    </script>
</body>
</html>
