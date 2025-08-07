<?php
include '../includes/proteccion.php';
?>

<?php
// filepath: c:\xampp\htdocs\smaq\equipos\editar_componente.php
include '../includes/conexion.php';

// Verifica que los datos necesarios estén presentes
if (
    isset($_POST['id']) &&
    isset($_POST['nombre']) &&
    isset($_POST['descripcion'])
) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    // Actualiza el componente en la base de datos
    $sql = "UPDATE componentes SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':id' => $id
    ]);

    // Redirige de vuelta a la página del equipo (ajusta la ruta según tu flujo)
    header("Location: ver_equipo.php?id=" . $_POST['equipo_id']);
    exit;
} else {
    echo "<div class='alert alert-danger'>Faltan datos para editar el componente.</div>";
}