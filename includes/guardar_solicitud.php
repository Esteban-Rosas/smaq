<?php
include_once('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $equipo_id = $_POST['equipo_id'];
    $descripcion = $_POST['descripcion'];
    $operario = $_POST['operario'];

    $sql = "INSERT INTO solicitudes_mantenimiento (fecha, equipo_id, descripcion_problema, operario)
            VALUES (:fecha, :equipo_id, :descripcion_problema, :operario)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':fecha' => $fecha,
        ':equipo_id' => $equipo_id,
        ':descripcion_problema' => $descripcion,
        ':operario' => $operario
    ]);

    // Redirigir o mostrar mensaje de éxito
    header("Location: ../mantenimientos/solicitud_mantenimiento.php");
    exit;
}
?>
