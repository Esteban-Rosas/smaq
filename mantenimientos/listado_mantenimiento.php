<?php
include '../includes/header.php';
include_once('../includes/conexion.php');

// Obtener el parámetro de ordenamiento de la URL, si existe
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha';

// Construir la consulta SQL con base en el orden seleccionado
$sql = "
    SELECT 
        sm.id AS id_solicitud,
        sm.fecha,
        e.nombre AS nombre_equipo,
        e.codigo AS codigo_equipo,
        u.nombre AS ubicacion_equipo,
        sm.descripcion_problema,
        sm.operario,
        sm.estado,
        om.id AS id_orden
    FROM solicitudes_mantenimiento sm
    INNER JOIN equipos e ON sm.equipo_id = e.id
    INNER JOIN ubicaciones u ON e.ubicacion_id = u.id
    LEFT JOIN ordenes_mantenimiento om ON om.solicitud_id = sm.id
    ORDER BY $orden DESC
";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Mantenimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Listado de Solicitudes de Mantenimiento</h2>

        <!-- Formulario para seleccionar el orden -->
        <form method="get" class="mb-3">
            <label for="orden" class="form-label">Ordenar por:</label>
            <select name="orden" id="orden" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                <option value="fecha" <?= $orden == 'fecha' ? 'selected' : '' ?>>Fecha</option>
                <option value="nombre_equipo" <?= $orden == 'nombre_equipo' ? 'selected' : '' ?>>Equipo</option>
                <option value="ubicacion_equipo" <?= $orden == 'ubicacion_equipo' ? 'selected' : '' ?>>Ubicación</option>
            </select>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Equipo</th>
                        <th>Ubicación</th>
                        <th>Descripción del Problema</th>
                        <th>Operario</th>
                        <th>Estado</th>
                        <th>Ver Orden</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_equipo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['ubicacion_equipo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['descripcion_problema']); ?></td>
                            <td><?php echo htmlspecialchars($fila['operario']); ?></td>
                            <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                            <td>
                                <?php if ($fila['id_orden']): ?>
                                    <a href="visualizar_orden.php?id=<?php echo $fila['id_orden']; ?>" class="btn btn-info btn-sm">
                                        Ver Orden
                                    </a>
                                <?php else: ?>
                                    <a href="orden_mantenimiento.php?id=<?php echo $fila['id_solicitud']; ?>" class="btn btn-primary btn-sm">
                                        Orden de Mantenimiento
                                    </a>
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



