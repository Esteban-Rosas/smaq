<?php
include '../includes/conexion.php';

session_start();

$equipo_id = $_POST['equipo_id'];
$tipo_mantenimiento = $_POST['tipo_mantenimiento'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$responsable = $_POST['responsable'];
$observaciones = $_POST['observaciones'];

// Guardar en cronogramas
$sql = "INSERT INTO cronogramas (equipo_id, tipo_mantenimiento, fecha_inicio, fecha_fin, responsable, observaciones)
        VALUES (:equipo_id, :tipo_mantenimiento, :fecha_inicio, :fecha_fin, :responsable, :observaciones)";
$stmt = $conexion->prepare($sql);
$stmt->execute([
    ':equipo_id' => $equipo_id,
    ':tipo_mantenimiento' => $tipo_mantenimiento,
    ':fecha_inicio' => $fecha_inicio,
    ':fecha_fin' => $fecha_fin,
    ':responsable' => $responsable,
    ':observaciones' => $observaciones
]);

// Validar que no exista una solicitud programada igual
$descripcion = "Mantenimiento programado ($tipo_mantenimiento) del $fecha_inicio al $fecha_fin. " . $observaciones;
$sql_check = "SELECT COUNT(*) FROM solicitudes_mantenimiento 
              WHERE fecha = :fecha AND equipo_id = :equipo_id AND estado = 'programado'";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->execute([
    ':fecha' => $fecha_inicio,
    ':equipo_id' => $equipo_id
]);
$existe = $stmt_check->fetchColumn();

if ($existe == 0) {
    // Insertar solo si no existe una solicitud similar
    $sql2 = "INSERT INTO solicitudes_mantenimiento (fecha, equipo_id, descripcion_problema, operario, estado, tipo_mantenimiento)
             VALUES (:fecha, :equipo_id, :descripcion, :operario, :estado, :tipo_accion)";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->execute([
        ':fecha' => $fecha_inicio,
        ':equipo_id' => $equipo_id,
        ':descripcion' => $descripcion,
        ':operario' => $responsable,
        ':estado' => 'programado',
        ':tipo_accion' => 'Preventivo'
    ]);
}

$_SESSION['mensaje_exito'] = "¡Cronograma guardado correctamente!";
header("Location: listar_cronograma.php");
exit;