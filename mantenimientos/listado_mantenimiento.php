<?php
include '../includes/header.php';
include_once('../includes/conexion.php');

// Obtener el parámetro de ordenamiento de la URL, si existe
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha';

// Obtener el parámetro de búsqueda unificado
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Construir la consulta SQL
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
        sm.tipo_mantenimiento,
        om.id AS id_orden
    FROM solicitudes_mantenimiento sm
    INNER JOIN equipos e ON sm.equipo_id = e.id
    INNER JOIN ubicaciones u ON e.ubicacion_id = u.id
    LEFT JOIN ordenes_mantenimiento om ON om.solicitud_id = sm.id
    WHERE 1=1
";

if (!empty($buscar)) {
    $sql .= " AND (e.nombre ILIKE :buscar OR u.nombre ILIKE :buscar)";
}

// Agregar ordenamiento
$sql .= " ORDER BY $orden DESC";

$stmt = $conexion->prepare($sql);

if (!empty($buscar)) {
    $stmt->bindValue(':buscar', "%$buscar%", PDO::PARAM_STR);
}

$stmt->execute();
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

        <!-- Formulario para seleccionar el orden y buscar -->
        <form method="get" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="buscar" class="form-label mb-0">Buscar</label>
                    <div class="input-group">
                        <input type="text" name="buscar" id="buscar" class="form-control"
                               value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
                        <button type="submit" class="btn btn-primary btn-sm" title="Buscar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-search" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.442 1.398a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="orden" class="form-label mb-0">Ordenar por:</label>
                    <select name="orden" id="orden" class="form-select"
                            onchange="this.form.submit()">
                        <option value="fecha" <?= $orden == 'fecha' ? 'selected' : '' ?>>Fecha</option>
                        <option value="nombre_equipo" <?= $orden == 'nombre_equipo' ? 'selected' : '' ?>>Equipo</option>
                        <option value="ubicacion_equipo" <?= $orden == 'ubicacion_equipo' ? 'selected' : '' ?>>Ubicación</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Equipo</th>
                        <th>Ubicación</th>
                        <th>Tipo de Acción</th>
                        <th>Descripción del Problema</th>
                        <th>Operario</th>
                        <th>Estado</th>
                        <th>Ver Orden</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_equipo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['ubicacion_equipo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['tipo_mantenimiento'] ?? ''); ?></td> <!-- Nuevo -->
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



