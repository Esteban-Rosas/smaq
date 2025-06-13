<?php
// filepath: c:\xampp\htdocs\smaq\equipos\guardar_componente.php
include '../includes/conexion.php';

$equipo_id = $_POST['equipo_id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$ruta_imagen = null;

// Manejo de la imagen
if (!empty($_FILES['imagen']['name'])) {
    $nombre_imagen = 'componente_' . uniqid() . '.' . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $ruta_destino = '../uploads/componentes/' . $nombre_imagen;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
        $ruta_imagen = "../uploads/componentes/" . $nombre_imagen;
    }
}

// Insertar en la base de datos
$sql = "INSERT INTO componentes (equipo_id, nombre, descripcion, imagen) VALUES (:equipo_id, :nombre, :descripcion, :imagen)";
$stmt = $conexion->prepare($sql);
$stmt->execute([
    ':equipo_id' => $equipo_id,
    ':nombre' => $nombre,
    ':descripcion' => $descripcion,
    ':imagen' => $ruta_imagen
]);

header("Location: listado_equipos.php");
exit;