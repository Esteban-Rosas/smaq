<?php
include '../includes/conexion.php';
include '../includes/header.php';

// Obtener todos los cronogramas con información del equipo
$sql = "SELECT c.*, e.nombre AS equipo_nombre, e.codigo AS equipo_codigo
        FROM cronogramas c
        JOIN equipos e ON c.equipo_id = e.id
        ORDER BY c.fecha_inicio ASC";
$stmt = $conexion->query($sql);
$cronogramas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensaje de éxito
$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Cronogramas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Listado de Cronogramas</h5>
        </div>
        <div class="card-body">
            <?php if ($mensaje_exito): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensaje_exito) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Equipo</th>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Responsable</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($cronogramas) > 0): ?>
                            <?php foreach ($cronogramas as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($row['equipo_nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['equipo_codigo']) ?></td>
                                    <td><?= ucfirst($row['tipo_mantenimiento']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_inicio']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_fin']) ?></td>
                                    <td><?= htmlspecialchars($row['responsable']) ?></td>
                                    <td>
                                        <!-- Aquí podrías enlazar un detalle si lo implementas -->
                                        <a href="ver_cronograma.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted">No hay cronogramas registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="../dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
