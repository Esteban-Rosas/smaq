<?php
include_once('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $equipo_id = $_POST['equipo_id'];
    $tipo_mantenimiento = $_POST['tipo_accion']; // Aquí tomas el valor del select
    $descripcion = $_POST['descripcion'];
    $operario = $_POST['operario'];
    $estado = 'pendiente'; // O el valor que corresponda

    $sql = "INSERT INTO solicitudes_mantenimiento (fecha, equipo_id, tipo_mantenimiento, descripcion_problema, operario, estado)
            VALUES (:fecha, :equipo_id, :tipo_mantenimiento, :descripcion, :operario, :estado)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':fecha' => $fecha,
        ':equipo_id' => $equipo_id,
        ':tipo_mantenimiento' => $tipo_mantenimiento,
        ':descripcion' => $descripcion,
        ':operario' => $operario,
        ':estado' => $estado
    ]);

    // Redirigir o mostrar mensaje de éxito
    header("Location: ../mantenimientos/solicitud_mantenimiento.php?exito=1");
    exit;
}
?>
