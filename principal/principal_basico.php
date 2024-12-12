<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['categoria'] !== 'básico') {
    header('Location: ../frontend/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Básico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Bienvenido, <?php echo $_SESSION['usuario']; ?> (Básico)</h2>
        <div class="text-center">
            <p>Desde aquí puedes realizar las siguientes acciones:</p>
            <button class="btn btn-primary" onclick="location.href='../funcionalidades/cambiar_password.php'">Cambiar Contraseña</button>
            <button class="btn btn-secondary" onclick="location.href='../backend/cerrar_sesion.php'">Cerrar Sesión</button>
        </div>
    </div>
</body>
</html>
