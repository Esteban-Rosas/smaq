<?php
include '../includes/proteccion.php';
?>

<?php
// filepath: c:\xampp\htdocs\smaq\mantenimientos\listado_realizados.php
include_once('../includes/conexion.php');
include_once('../includes/header.php');

$equipo_id = isset($_GET['equipo_id']) ? intval($_GET['equipo_id']) : 0;

if ($equipo_id <= 0) {
    echo "<div class='alert alert-danger'>Equipo no válido.</div>";
    exit;
}

// Consulta para obtener los mantenimientos realizados de ese equipo
$sql = "
    SELECT 
        sm.tipo_mantenimiento,
        sm.descripcion_problema,
        sm.operario,
        sm.estado,
        om.id AS id_orden
    FROM solicitudes_mantenimiento sm
    LEFT JOIN ordenes_mantenimiento om ON om.solicitud_id = sm.id
    WHERE sm.equipo_id = :equipo_id
      AND sm.estado = 'Realizado'
    ORDER BY sm.fecha DESC
";
$stmt = $conexion->prepare($sql);
$stmt->execute([':equipo_id' => $equipo_id]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mantenimientos Realizados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Mantenimientos Realizados del Equipo</h3>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tipo de Mantenimiento</th>
                    <th>Descripción</th>
                    <th>Operario</th>
                    <th>Ver Orden</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['tipo_mantenimiento']) ?></td>
                    <td><?= htmlspecialchars($fila['descripcion_problema']) ?></td>
                    <td><?= htmlspecialchars($fila['operario']) ?></td>
                    <td>
                        <?php if ($fila['id_orden']): ?>
                            <a href="visualizar_orden.php?id=<?= $fila['id_orden'] ?>" class="btn btn-info btn-sm">Ver Orden</a>
                        <?php else: ?>
                            <span class="text-muted">Sin orden</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>