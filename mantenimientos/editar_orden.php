<?php
include '../includes/proteccion.php';
include '../includes/conexion.php';

// Verificar rol de usuario
$esIngeniero = (isset($_SESSION['usuario_rol']) && strtolower(trim($_SESSION['usuario_rol'])) === 'ingeniero');
if (!$esIngeniero) {
    header("Location: listado_mantenimiento.php?error=Acceso no autorizado");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de orden no válido.");
}

$id = (int)$_GET['id'];

// Obtener datos de la orden
$sql = "SELECT * FROM ordenes_mantenimiento WHERE id = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
    die("Orden de mantenimiento no encontrada.");
}

// Obtener equipo asociado
$sql_equipo = "SELECT nombre, codigo FROM equipos WHERE id = :equipo_id";
$stmt_equipo = $conexion->prepare($sql_equipo);
$stmt_equipo->execute([':equipo_id' => $orden['equipo_id']]);
$equipo = $stmt_equipo->fetch(PDO::FETCH_ASSOC);

// Obtener facturas
$sql_facturas = "SELECT id, ruta_factura_imagen FROM orden_facturas WHERE orden_id = :orden_id";
$stmt_facturas = $conexion->prepare($sql_facturas);
$stmt_facturas->execute([':orden_id' => $id]);
$facturas = $stmt_facturas->fetchAll(PDO::FETCH_ASSOC);

// Procesar actualización
$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $realizado_por = $_POST['realizado_por'];
    $motivo = $_POST['motivo'];
    $tipo_mantenimiento = $_POST['tipo_mantenimiento'];
    $accion_mantenimiento = $_POST['accion_mantenimiento'];
    $descripcion_equipo = $_POST['descripcion_equipo'];
    $herramientas = $_POST['herramientas'];
    $descripcion_realizado = $_POST['descripcion_realizado'];
    $repuestos = $_POST['repuestos'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $responsable = $_POST['responsable'];
    $fecha_factura = $_POST['fecha_factura'];
    
    // Manejar eliminación de facturas
    if (isset($_POST['eliminar_facturas'])) {
        foreach ($_POST['eliminar_facturas'] as $factura_id) {
            $stmt_delete = $conexion->prepare("DELETE FROM orden_facturas WHERE id = :id");
            $stmt_delete->execute([':id' => $factura_id]);
        }
    }
    
    // Manejar nuevas facturas
    if (!empty($_FILES['nuevas_facturas']['name'][0])) {
        $dir_facturas = "uploads/facturas/";
        if (!is_dir($dir_facturas)) mkdir($dir_facturas, 0777, true);
        
        foreach ($_FILES['nuevas_facturas']['name'] as $key => $name) {
            if ($_FILES['nuevas_facturas']['error'][$key] == 0) {
                $nombre_archivo = uniqid() . '_' . str_replace(' ', '_', basename($name));
                $ruta_factura_imagen = $dir_facturas . $nombre_archivo;
                
                if (move_uploaded_file($_FILES['nuevas_facturas']['tmp_name'][$key], $ruta_factura_imagen)) {
                    $stmt_insert = $conexion->prepare("INSERT INTO orden_facturas (orden_id, ruta_factura_imagen) VALUES (:orden_id, :ruta)");
                    $stmt_insert->execute([
                        ':orden_id' => $id,
                        ':ruta' => $ruta_factura_imagen
                    ]);
                }
            }
        }
    }
    
    // Actualizar la orden
    $sql_update = "
        UPDATE ordenes_mantenimiento SET
            realizado_por = :realizado_por,
            motivo = :motivo,
            tipo_mantenimiento = :tipo_mantenimiento,
            accion_mantenimiento = :accion_mantenimiento,
            descripcion_equipo = :descripcion_equipo,
            herramientas = :herramientas,
            descripcion_realizado = :descripcion_realizado,
            repuestos = :repuestos,
            hora_inicio = :hora_inicio,
            hora_fin = :hora_fin,
            responsable = :responsable,
            fecha_factura = :fecha_factura
        WHERE id = :id
    ";
    
    $stmt_update = $conexion->prepare($sql_update);
    $ok = $stmt_update->execute([
        ':realizado_por' => $realizado_por,
        ':motivo' => $motivo,
        ':tipo_mantenimiento' => $tipo_mantenimiento,
        ':accion_mantenimiento' => $accion_mantenimiento,
        ':descripcion_equipo' => $descripcion_equipo,
        ':herramientas' => $herramientas,
        ':descripcion_realizado' => $descripcion_realizado,
        ':repuestos' => $repuestos,
        ':hora_inicio' => $hora_inicio,
        ':hora_fin' => $hora_fin,
        ':responsable' => $responsable,
        ':fecha_factura' => $fecha_factura,
        ':id' => $id
    ]);
    
    if ($ok) {
        $mensaje = "<div class='alert alert-success'>Orden actualizada correctamente</div>";
        // Refrescar datos
        $stmt->execute([':id' => $id]);
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar la orden</div>";
    }
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Orden de Mantenimiento - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos similares a orden_mantenimiento.php */
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --warning: #ff9800;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .edit-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            max-width: 1200px;
            margin: 2rem auto;
        }
        
        .edit-header {
            background: linear-gradient(135deg, var(--warning), #ff8c00);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .form-section {
            background: #f8fafc;
            border-radius: var(--border-radius);
            padding: 25px;
            margin: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        .factura-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .factura-preview {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .factura-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            <h1><i class="fas fa-edit me-3"></i>Editar Orden de Mantenimiento #<?= $id ?></h1>
        </div>
        
        <div class="p-4">
            <?= $mensaje ?>
            
            <form action="editar_orden.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
                <!-- Información básica -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle me-2"></i> Información básica</h3>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Equipo:</label>
                            <input type="text" class="form-control" 
                                value="<?= htmlspecialchars($equipo['nombre']) ?> (<?= htmlspecialchars($equipo['codigo']) ?>)" 
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Realizado por:</label>
                            <input type="text" name="realizado_por" class="form-control" 
                                value="<?= htmlspecialchars($orden['realizado_por']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Motivo del mantenimiento:</label>
                        <input type="text" name="motivo" class="form-control" 
                            value="<?= htmlspecialchars($orden['motivo']) ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de mantenimiento:</label>
                            <select name="tipo_mantenimiento" class="form-select" required>
                                <option value="Mecánico" <?= $orden['tipo_mantenimiento'] == 'Mecánico' ? 'selected' : '' ?>>Mecánico</option>
                                <option value="Eléctrico" <?= $orden['tipo_mantenimiento'] == 'Eléctrico' ? 'selected' : '' ?>>Eléctrico</option>
                                <option value="Instrumentación" <?= $orden['tipo_mantenimiento'] == 'Instrumentación' ? 'selected' : '' ?>>Instrumentación</option>
                                <option value="Limpieza" <?= $orden['tipo_mantenimiento'] == 'Limpieza' ? 'selected' : '' ?>>Limpieza</option>
                                <option value="Lubricación" <?= $orden['tipo_mantenimiento'] == 'Lubricación' ? 'selected' : '' ?>>Lubricación</option>
                                <option value="Construcción" <?= $orden['tipo_mantenimiento'] == 'Construcción' ? 'selected' : '' ?>>Construcción</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Acción de mantenimiento:</label>
                            <select name="accion_mantenimiento" class="form-select" required>
                                <option value="Preventivo" <?= $orden['accion_mantenimiento'] == 'Preventivo' ? 'selected' : '' ?>>Preventivo</option>
                                <option value="Correctivo" <?= $orden['accion_mantenimiento'] == 'Correctivo' ? 'selected' : '' ?>>Correctivo</option>
                                <option value="Predictivo" <?= $orden['accion_mantenimiento'] == 'Predictivo' ? 'selected' : '' ?>>Predictivo</option>
                                <option value="Mejoras" <?= $orden['accion_mantenimiento'] == 'Mejoras' ? 'selected' : '' ?>>Mejoras</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Detalles del trabajo -->
                <div class="form-section">
                    <h3><i class="fas fa-tasks me-2"></i> Detalles del trabajo</h3>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción del trabajo:</label>
                        <textarea name="descripcion_equipo" class="form-control" required><?= htmlspecialchars($orden['descripcion_equipo']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Herramientas utilizadas:</label>
                        <textarea name="herramientas" class="form-control" required><?= htmlspecialchars($orden['herramientas']) ?></textarea>
                    </div>
                </div>
                
                <!-- Resultados -->
                <div class="form-section">
                    <h3><i class="fas fa-check-circle me-2"></i> Resultados</h3>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción de lo realizado:</label>
                        <textarea name="descripcion_realizado" class="form-control" required><?= htmlspecialchars($orden['descripcion_realizado']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Repuestos utilizados:</label>
                        <textarea name="repuestos" class="form-control"><?= htmlspecialchars($orden['repuestos']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora de inicio:</label>
                            <input type="time" name="hora_inicio" class="form-control" 
                                value="<?= htmlspecialchars($orden['hora_inicio']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora de finalización:</label>
                            <input type="time" name="hora_fin" class="form-control" 
                                value="<?= htmlspecialchars($orden['hora_fin']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Responsable:</label>
                        <input type="text" name="responsable" class="form-control" 
                            value="<?= htmlspecialchars($orden['responsable']) ?>" required>
                    </div>
                </div>
                
                <!-- Gastos y facturas -->
                <div class="form-section">
                    <h3><i class="fas fa-receipt me-2"></i> Gastos y facturas</h3>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de la factura:</label>
                            <input type="date" name="fecha_factura" class="form-control" 
                                value="<?= htmlspecialchars($orden['fecha_factura']) ?>">
                        </div>
                    </div>
                    
                    <!-- Facturas existentes -->
                    <?php if (!empty($facturas)): ?>
                        <h4>Facturas existentes:</h4>
                        <div class="mb-4">
                            <?php foreach ($facturas as $factura): ?>
                                <div class="factura-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="eliminar_facturas[]" value="<?= $factura['id'] ?>" id="del-<?= $factura['id'] ?>">
                                        <label class="form-check-label" for="del-<?= $factura['id'] ?>">Eliminar</label>
                                    </div>
                                    <div class="factura-preview">
                                        <img src="<?= htmlspecialchars($factura['ruta_factura_imagen']) ?>" 
                                            alt="Factura <?= $factura['id'] ?>">
                                    </div>
                                    <div>
                                        <small><?= basename($factura['ruta_factura_imagen']) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Nuevas facturas -->
                    <div class="mb-3">
                        <label class="form-label">Agregar nuevas facturas:</label>
                        <input type="file" name="nuevas_facturas[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">Puede seleccionar múltiples imágenes (Máx. 5)</small>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="visualizar_orden.php?id=<?= $id ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>