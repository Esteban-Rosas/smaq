<?php
ob_start();
include '../includes/proteccion.php';
include '../includes/header.php';
include_once('../includes/conexion.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listado_mantenimiento.php?error=id_invalido');
    exit();
}

$solicitud_id = $_GET['id'];

// Obtener datos de la solicitud
$sql = "SELECT * FROM solicitudes_mantenimiento WHERE id = :id";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id', $solicitud_id, PDO::PARAM_INT);
$stmt->execute();
$solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra la solicitud
if (!$solicitud) {
    header('Location: listado_mantenimiento.php?error=no_encontrado');
    exit();
}

// Obtener equipos para el dropdown
$sql_equipos = "SELECT id, nombre FROM equipos ORDER BY nombre ASC";
$stmt_equipos = $conexion->query($sql_equipos);
$equipos = $stmt_equipos->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y validar datos del formulario
    $equipo_id = $_POST['equipo_id'] ?? '';
    $tipo_mantenimiento = $_POST['tipo_mantenimiento'] ?? '';
    $descripcion_problema = $_POST['descripcion_problema'] ?? '';
    $operario = $_POST['operario'] ?? '';
    $estado = $_POST['estado'] ?? '';
    
    // Validaciones básicas
    $errores = [];
    
    if (empty($equipo_id)) {
        $errores[] = "Debe seleccionar un equipo";
    }
    
    if (empty($tipo_mantenimiento)) {
        $errores[] = "Debe seleccionar el tipo de mantenimiento";
    }
    
    if (empty($descripcion_problema)) {
        $errores[] = "La descripción del problema es obligatoria";
    }
    
    if (empty($operario)) {
        $errores[] = "El nombre del solicitante es obligatorio";
    }
    
    if (empty($estado)) {
        $errores[] = "Debe seleccionar el estado";
    }
    
    // Si no hay errores, actualizar en la base de datos
    if (empty($errores)) {
        try {
            $sql_update = "UPDATE solicitudes_mantenimiento SET
                            equipo_id = :equipo_id,
                            tipo_mantenimiento = :tipo_mantenimiento,
                            descripcion_problema = :descripcion_problema,
                            operario = :operario,
                            estado = :estado
                          WHERE id = :id";
            
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bindParam(':equipo_id', $equipo_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':tipo_mantenimiento', $tipo_mantenimiento, PDO::PARAM_STR);
            $stmt_update->bindParam(':descripcion_problema', $descripcion_problema, PDO::PARAM_STR);
            $stmt_update->bindParam(':operario', $operario, PDO::PARAM_STR);
            $stmt_update->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt_update->bindParam(':id', $solicitud_id, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                // Redirigir con mensaje de éxito
                header('Location: listado_mantenimiento.php?success=actualizado');
                exit();
            } else {
                $errores[] = "Error al actualizar la solicitud: " . implode(", ", $stmt_update->errorInfo());
            }
        } catch (PDOException $e) {
            $errores[] = "Error de base de datos: " . $e->getMessage();
        }
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Solicitud de Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }
        
        .main-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .form-label {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .error-container {
            border-left: 4px solid var(--danger-color);
            background-color: #ffe6e6;
        }
        
        .info-container {
            border-left: 4px solid var(--primary-color);
            background-color: #e8f4fc;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card main-card mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Editar Solicitud de Mantenimiento
                            </h2>
                            <a href="listado_mantenimiento.php" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger error-container mb-4">
                                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Error en el formulario</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info info-container mb-4">
                            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Información de la solicitud</h5>
                            <p class="mb-0">
                                <strong>ID:</strong> <?php echo htmlspecialchars($solicitud['id']); ?> | 
                                <strong>Fecha de creación:</strong> <?php echo date('d/m/Y', strtotime($solicitud['fecha'])); ?>
                            </p>
                        </div>
                        
                        <form method="POST" id="editarSolicitudForm">
                            <div class="row g-3">
                                <!-- Equipo -->
                                <div class="col-md-6">
                                    <label for="equipo_id" class="form-label">Equipo</label>
                                    <select class="form-select" id="equipo_id" name="equipo_id" required>
                                        <option value="">Seleccione un equipo</option>
                                        <?php foreach ($equipos as $equipo): ?>
                                            <option value="<?php echo $equipo['id']; ?>" 
                                                <?php echo ($solicitud['equipo_id'] == $equipo['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($equipo['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Tipo de mantenimiento -->
                                <div class="col-md-6">
                                    <label for="tipo_mantenimiento" class="form-label">Tipo de Mantenimiento</label>
                                    <select class="form-select" id="tipo_mantenimiento" name="tipo_mantenimiento" required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="Preventivo" <?php echo ($solicitud['tipo_mantenimiento'] == 'Preventivo') ? 'selected' : ''; ?>>Preventivo</option>
                                        <option value="Correctivo" <?php echo ($solicitud['tipo_mantenimiento'] == 'Correctivo') ? 'selected' : ''; ?>>Correctivo</option>
                                        <option value="Predictivo" <?php echo ($solicitud['tipo_mantenimiento'] == 'Predictivo') ? 'selected' : ''; ?>>Predictivo</option>
                                        <option value="Otro" <?php echo ($solicitud['tipo_mantenimiento'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                    </select>
                                </div>
                                
                                <!-- Solicitante -->
                                <div class="col-md-6">
                                    <label for="operario" class="form-label">Solicitante</label>
                                    <input type="text" class="form-control" id="operario" name="operario" 
                                           value="<?php echo htmlspecialchars($solicitud['operario']); ?>" required>
                                </div>
                                
                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="">Seleccione estado</option>
                                        <option value="Pendiente" <?php echo ($solicitud['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="En proceso" <?php echo ($solicitud['estado'] == 'En proceso') ? 'selected' : ''; ?>>En proceso</option>
                                        <option value="Completado" <?php echo ($solicitud['estado'] == 'Completado') ? 'selected' : ''; ?>>Completado</option>
                                        <option value="Cancelado" <?php echo ($solicitud['estado'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>
                                
                                <!-- Descripción del problema -->
                                <div class="col-12">
                                    <label for="descripcion_problema" class="form-label">Descripción del Problema</label>
                                    <textarea class="form-control" id="descripcion_problema" name="descripcion_problema" 
                                              rows="4" required><?php echo htmlspecialchars($solicitud['descripcion_problema']); ?></textarea>
                                </div>
                                
                                <!-- Botones -->
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-danger" id="btnEliminar"
                                                data-id="<?php echo $solicitud_id; ?>">
                                            <i class="fas fa-trash-alt me-2"></i>Eliminar Solicitud
                                        </button>
                                        
                                        <button type="submit" class="btn btn-submit text-white">
                                            <i class="fas fa-save me-2"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
        const btnEliminar = document.getElementById('btnEliminar');
        if (btnEliminar) {
            btnEliminar.addEventListener('click', function() {
                const solicitudId = this.getAttribute('data-id');
                const confirmDelete = document.getElementById('confirmDelete');
                
                // Configurar el enlace de eliminación con el ID correcto
                confirmDelete.href = `eliminar_solicitud.php?id=${solicitudId}`;
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            });
        }
        
        // Validación del formulario antes de enviar
        const form = document.getElementById('editarSolicitudForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Validación simple de campos requeridos
                const requiredFields = form.querySelectorAll('[required]');
                let valid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    alert('Por favor complete todos los campos requeridos');
                }
            });
        }
    </script>
</body>
</html>