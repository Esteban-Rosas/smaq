<?php
include '../includes/proteccion.php';
include '../includes/header.php';
include_once('../includes/conexion.php');

// Agrega esta línea:
$esIngeniero = (isset($_SESSION['usuario_rol']) && strtolower(trim($_SESSION['usuario_rol'])) === 'ingeniero');

// Obtener parámetros
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha';
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Construir consulta SQL
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }
        
        .status-badge {
            padding: 0.4em 0.8em;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pendiente {
            background-color: #fef9e7;
            color: #b7950b;
            border: 1px solid #f1c40f;
        }
        
        .status-en-proceso {
            background-color: #e8f4fc;
            color: #2980b9;
            border: 1px solid #3498db;
        }
        
        .status-completado {
            background-color: #eafaf1;
            color: #27ae60;
            border: 1px solid #2ecc71;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .btn-view {
            background-color: #3498db;
            color: white;
        }
        
        .btn-edit {
            background-color: #f39c12;
            color: white;
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-orden {
            background-color: #27ae60;
            color: white;
        }
        
        .search-container {
            background-color: white;
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .table thead th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 500;
        }
        
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        
        .filter-btn {
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        
        .main-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card main-card mb-4">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Listado de Mantenimientos
                    </h2>
                    <a href="solicitud_mantenimiento.php" class="btn btn-light">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Solicitud
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros y búsqueda -->
                <form method="get" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <div class="search-container">
                                <div class="input-group border-0">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="buscar" id="buscar" class="form-control border-0" 
                                           placeholder="Buscar por equipo o ubicación..." 
                                           value="<?= htmlspecialchars($buscar) ?>">
                                    <button type="submit" class="btn btn-primary filter-btn">
                                        Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <div class="me-2">
                                    <label for="orden" class="form-label mb-1">Ordenar por:</label>
                                    <select name="orden" id="orden" class="form-select" onchange="this.form.submit()">
                                        <option value="fecha" <?= $orden == 'fecha' ? 'selected' : '' ?>>Fecha (más reciente)</option>
                                        <option value="nombre_equipo" <?= $orden == 'nombre_equipo' ? 'selected' : '' ?>>Equipo (A-Z)</option>
                                        <option value="ubicacion_equipo" <?= $orden == 'ubicacion_equipo' ? 'selected' : '' ?>>Ubicación (A-Z)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="estado" class="form-label mb-1">Filtrar por estado:</label>
                                    <select name="estado" id="estado" class="form-select" onchange="this.form.submit()">
                                        <option value="">Todos</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="En proceso">En proceso</option>
                                        <option value="Completado">Completado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Tabla de mantenimientos -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Equipo</th>
                                <th>Ubicación</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Solicitante</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($stmt->rowCount() > 0): ?>
                                <?php while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <?php 
                                    // Clase CSS según estado
                                    $statusClass = '';
                                    if ($fila['estado'] == 'Pendiente') $statusClass = 'status-pendiente';
                                    if ($fila['estado'] == 'En proceso') $statusClass = 'status-en-proceso';
                                    if ($fila['estado'] == 'Completado') $statusClass = 'status-completado';
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($fila['fecha'])) ?></td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($fila['nombre_equipo']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($fila['codigo_equipo']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($fila['ubicacion_equipo']) ?></td>
                                        <td><?= htmlspecialchars($fila['tipo_mantenimiento'] ?? '') ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" 
                                                 title="<?= htmlspecialchars($fila['descripcion_problema']) ?>">
                                                <?= htmlspecialchars($fila['descripcion_problema']) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($fila['operario']) ?></td>
                                        <td>
                                            <span class="status-badge <?= $statusClass ?>">
                                                <?= htmlspecialchars($fila['estado']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <?php if ($fila['id_orden']): ?>
                                                    <a href="visualizar_orden.php?id=<?= $fila['id_orden'] ?>" 
                                                       class="action-btn btn-view" title="Ver Orden">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <?php if ($esIngeniero): ?>
                                                        <a href="orden_mantenimiento.php?id=<?= $fila['id_solicitud'] ?>" 
                                                           class="action-btn btn-orden" title="Crear Orden">
                                                            <i class="fas fa-file-medical"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <a href="editar_solicitud.php?id=<?= $fila['id_solicitud'] ?>" 
                                                   class="action-btn btn-edit" title="Editar Solicitud">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" 
                                                        class="action-btn btn-delete" 
                                                        title="Eliminar Solicitud"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $fila['id_solicitud'] ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h4 class="text-muted mb-2">No se encontraron mantenimientos</h4>
                                            <p class="text-muted mb-4">No hay solicitudes de mantenimiento registradas</p>
                                            <a href="solicitud_mantenimiento.php" class="btn btn-primary">
                                                <i class="fas fa-plus-circle me-2"></i>Crear Nueva Solicitud
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <nav class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Mostrando <?= $stmt->rowCount() ?> registros
                    </div>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación para Eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-trash-alt fa-3x text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5>¿Estás seguro de eliminar esta solicitud?</h5>
                            <p class="mb-0">Esta acción eliminará permanentemente la solicitud de mantenimiento y no se podrá recuperar.</p>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Si esta solicitud tiene órdenes de mantenimiento asociadas, también serán eliminadas.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar Solicitud
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Configurar el modal de eliminación
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const solicitudId = button.getAttribute('data-id');
            const confirmDelete = document.getElementById('confirmDelete');
            
            // Configurar el enlace de eliminación con el ID correcto
            confirmDelete.href = `eliminar_solicitud.php?id=${solicitudId}`;
        });
        
        // Animación para los botones de acción
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.classList.add('animate__animated', 'animate__pulse');
            });
            
            btn.addEventListener('mouseleave', function() {
                this.classList.remove('animate__animated', 'animate__pulse');
            });
        });
    </script>
</body>
</html>