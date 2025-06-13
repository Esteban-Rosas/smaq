<?php
include '../includes/conexion.php';
include '../includes/header.php';

// Consulta equipos con JOIN para mostrar el nombre de la ubicación
$sql = "SELECT e.id, e.nombre, u.nombre AS ubicacion, e.codigo
        FROM equipos e
        LEFT JOIN ubicaciones u ON e.ubicacion_id = u.id
        ORDER BY e.nombre ASC";
$stmt = $conexion->query($sql);
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Equipos - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0"><i class="bi bi-gear-fill text-primary me-2"></i>Listado de Equipos</h3>
            <a href="registrar.php" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Equipo
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Código</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($equipos) > 0): ?>
                        <?php foreach ($equipos as $index => $equipo): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($equipo['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($equipo['ubicacion']); ?></td>
                                <td><?php echo htmlspecialchars($equipo['codigo']); ?></td>
                                <td class="text-center">
                                    <a href="ver_equipo.php?id=<?php echo $equipo['id']; ?>" class="btn btn-sm btn-info me-1" title="Ver">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="editar_equipo.php?id=<?php echo $equipo['id']; ?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <a href="componentes_equipo.php?equipo_id=<?php echo $equipo['id']; ?>" class="btn btn-sm btn-primary" title="Componentes">
                                        <i class="bi bi-puzzle"></i> Componentes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay equipos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="../dashboard.php" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
