<?php
include '../includes/conexion.php';
include '../includes/header.php';

$equipo_codigo = '';
$solicitud = null;

if (isset($_GET['id'])) {
    // Traer todos los datos de la solicitud y del equipo
    $stmt = $conexion->prepare("
        SELECT sm.*, e.codigo, e.nombre AS nombre_equipo
        FROM solicitudes_mantenimiento sm
        INNER JOIN equipos e ON sm.equipo_id = e.id
        WHERE sm.id = :id
    ");
    $stmt->execute([':id' => $_GET['id']]);
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($solicitud) {
        $equipo_codigo = $solicitud['codigo'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipo_codigo = $_POST['equipo_codigo'];
    $realizado_por = $_POST['realizado_por'];
    $motivo = $_POST['motivo'];
    $tipo_mantenimiento = $_POST['tipo_mantenimiento'];
    $accion_mantenimiento = $_POST['accion_mantenimiento']; // NUEVO CAMPO
    $descripcion_equipo = $_POST['descripcion_equipo'];
    $herramientas = $_POST['herramientas'];
    $descripcion_realizado = $_POST['descripcion_realizado'];
    $repuestos = $_POST['repuestos'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $responsable = $_POST['responsable'];
    $fecha_factura = $_POST['fecha_factura'];

    // Manejar la subida de imágenes
    $ruta_imagen_equipo = null;
    $ruta_factura_imagen = null;

    if (isset($_FILES['imagen_equipo']) && $_FILES['imagen_equipo']['error'] == 0) {
        $dir_evidencias = "uploads/evidencias/";
        if (!is_dir($dir_evidencias)) {
            mkdir($dir_evidencias, 0777, true);
        }
        $ruta_imagen_equipo = $dir_evidencias . basename($_FILES['imagen_equipo']['name']);
        move_uploaded_file($_FILES['imagen_equipo']['tmp_name'], $ruta_imagen_equipo);
    }

    if (isset($_FILES['factura_imagen']) && $_FILES['factura_imagen']['error'] == 0) {
        $dir_facturas = "uploads/facturas/";
        if (!is_dir($dir_facturas)) {
            mkdir($dir_facturas, 0777, true);
        }
        $ruta_factura_imagen = $dir_facturas . basename($_FILES['factura_imagen']['name']);
        move_uploaded_file($_FILES['factura_imagen']['tmp_name'], $ruta_factura_imagen);
    }

    // Obtener el ID del equipo a partir del código
    $stmt_equipo = $conexion->prepare("SELECT id FROM equipos WHERE codigo = :codigo");
    $stmt_equipo->execute([':codigo' => $equipo_codigo]);
    $fila_equipo = $stmt_equipo->fetch(PDO::FETCH_ASSOC);
    $equipo_id = $fila_equipo ? $fila_equipo['id'] : null;

    if ($equipo_id) {
        $sql = "INSERT INTO ordenes_mantenimiento (
            solicitud_id,  -- <--- agrega este campo
            fecha,
            equipo_id,
            realizado_por,
            motivo,
            tipo_mantenimiento,
            accion_mantenimiento,           
            descripcion_equipo,
            herramientas,
            imagen_equipo,
            descripcion_realizado,
            repuestos,
            hora_inicio,
            hora_fin,
            responsable,
            fecha_factura,
            factura_imagen
        ) VALUES (
            :solicitud_id,  -- <--- agrega este valor
            NOW(), :equipo_id, :realizado_por, :motivo, :tipo_mantenimiento, :accion_mantenimiento, :descripcion_equipo, :herramientas, :imagen_equipo, :descripcion_realizado, :repuestos, :hora_inicio, :hora_fin, :responsable, :fecha_factura, :factura_imagen
        )";

        $stmt = $conexion->prepare($sql);
        $ok = $stmt->execute([
            ':solicitud_id' => $_POST['solicitud_id'],
            ':equipo_id' => $equipo_id,
            ':realizado_por' => $realizado_por,
            ':motivo' => $motivo,
            ':tipo_mantenimiento' => $tipo_mantenimiento,
            ':accion_mantenimiento' => $_POST['accion_mantenimiento'],
            ':descripcion_equipo' => $descripcion_equipo,
            ':herramientas' => $herramientas,
            ':imagen_equipo' => $ruta_imagen_equipo,
            ':descripcion_realizado' => $descripcion_realizado,
            ':repuestos' => $repuestos,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':responsable' => $responsable,
            ':fecha_factura' => $fecha_factura ?: null,
            ':factura_imagen' => $ruta_factura_imagen
        ]);

        if ($ok) {
            // Actualizar el estado de la solicitud a "Realizado"
            if (isset($_POST['solicitud_id'])) {
                $stmt_estado = $conexion->prepare("UPDATE solicitudes_mantenimiento SET estado = 'Realizado' WHERE id = :id");
                $stmt_estado->execute([':id' => $_POST['solicitud_id']]);
            }
            header("Location: listado_mantenimiento.php");
            exit;
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al registrar la orden.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-warning'>Error: Código de equipo no encontrado.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Orden de Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registrar Orden de Mantenimiento</h4>
            </div>
            <div class="card-body">
                <?php if (isset($mensaje)) echo $mensaje; ?>
                <form action="orden_mantenimiento.php" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Código del equipo:</label>
                            <input type="text" name="equipo_codigo" class="form-control" value="<?= htmlspecialchars($equipo_codigo); ?>" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Realizado por:</label>
                            <input type="text" name="realizado_por" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo del mantenimiento:</label>
                        <input type="text" name="motivo" class="form-control" value="<?= htmlspecialchars($solicitud['descripcion_problema'] ?? '') ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipo_mantenimiento" class="form-label">Tipo</label>
                            <select name="tipo_mantenimiento" id="tipo_mantenimiento" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option>Mecánico</option>
                                <option>Eléctrico</option>
                                <option>Instrumentación</option>
                                <option>Limpieza</option>
                                <option>Lubricación</option>
                                <option>Construcción</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="accion_mantenimiento" class="form-label">Acción</label>
                            <select name="accion_mantenimiento" id="accion_mantenimiento" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option>Preventivo</option>
                                <option>Correctivo</option>
                                <option>Predictivo</option>
                                <option>Mejoras</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción del trabajo:</label>
                        <textarea name="descripcion_equipo" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Herramientas a utilizar:</label>
                        <textarea name="herramientas" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Información del equipo/Dibujo de piezas:</label>
                        <input type="file" name="imagen_equipo" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción de lo realizado:</label>
                        <textarea name="descripcion_realizado" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Repuestos a reemplazar:</label>
                        <textarea name="repuestos" class="form-control"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hora de inicio:</label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de finalización:</label>
                            <input type="time" name="hora_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsable encargado:</label>
                        <input type="text" name="responsable" class="form-control" required>
                    </div>
                    <h5 class="mt-4">Gastos cargados a la orden</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de la factura:</label>
                            <input type="date" name="fecha_factura" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Imagen de la factura:</label>
                            <input type="file" name="factura_imagen" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <?php if (isset($_GET['id'])): ?>
                        <input type="hidden" name="solicitud_id" value="<?= intval($_GET['id']); ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success">Registrar orden</button>
                    <a href="listado_mantenimiento.php" class="btn btn-secondary">Volver al listado</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php include '../includes/footer.php'; ?>