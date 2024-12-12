<?php
session_start();
include '../backend/conexion.php';

// Verificar acceso de rol
if (!isset($_SESSION['usuario']) || $_SESSION['categoria'] !== 'avanzado') {
    header('Location: ../frontend/index.php');
    exit();
}

// Consultar logs
$consulta = "SELECT l.id, u.nombre AS usuario, l.accion, l.fecha 
             FROM logs l
             JOIN usuarios u ON l.usuario_id = u.id
             ORDER BY l.fecha DESC";
$resultado = $conexion->query($consulta);

// Manejo de error en la consulta
if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Logs del Sistema</h2>

        <!-- Botón Volver -->
        <div class="text-end mb-4">
            <button type="button" class="btn btn-secondary" onclick="location.href='../principal/principal_avanzado.php'">Volver a Principal</button>
        </div>

        <!-- Tabla de logs -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado->num_rows > 0) { ?>
                        <?php while ($log = $resultado->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td><?php echo $log['usuario']; ?></td>
                                <td><?php echo $log['accion']; ?></td>
                                <td><?php echo $log['fecha']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="text-center">No hay registros disponibles.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

