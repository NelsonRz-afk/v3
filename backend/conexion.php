<?php
$servidor = 'localhost';
$usuario = 'root';
$contraseña = '';
$base_datos = 'sistema_login';
$puerto = 3306;

$conexion = new mysqli($servidor, $usuario, $contraseña, $base_datos, $puerto);

if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}

function registrar_log($conexion, $usuario_id, $accion) {
    $consulta = $conexion->prepare("INSERT INTO logs (usuario_id, accion) VALUES (?, ?)");
    $consulta->bind_param('is', $usuario_id, $accion);
    $consulta->execute();
}
?>
