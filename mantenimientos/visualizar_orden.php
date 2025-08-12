<?php
include '../includes/proteccion.php';
?>

<?php
include '../includes/conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de orden no válido.");
}

$id = (int)$_GET['id'];

// Consulta con JOIN a la tabla equipos para traer nombre y código
$sql = "
    SELECT om.*, e.nombre AS nombre_equipo, e.codigo AS codigo_equipo
    FROM ordenes_mantenimiento om
    JOIN equipos e ON om.equipo_id = e.id
    WHERE om.id = :id
";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
    die("Orden de mantenimiento no encontrada.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Orden de Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .tabla-borde td, .tabla-borde th { border: 1px solid #000; padding: 6px; }
        .tabla-borde { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .section-title { background-color: #007bff; color: white; padding: 8px; margin-top: 20px; }
        .img-fluid { max-width: 300px; height: auto; border: 1px solid #ccc; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container bg-white p-4 shadow">
        <div class="text-center mb-4">
            <h4>Cárnicos Estefany</h4>
            <h5>ORDEN DE MANTENIMIENTO</h5>
            <small>Código: MAN-R-083 | Versión 01 | Fecha: 2024-06-01</small>
        </div>

        <div class="section-title">Información General</div>
        <table class="tabla-borde">
            <tr>
                <th>Fecha</th>
                <td><?php echo date('d/m/Y H:i', strtotime($orden['fecha'])); ?></td>
                <th>Tipo de Mantenimiento</th>
                <td><?php echo htmlspecialchars($orden['tipo_mantenimiento']); ?></td>
            </tr>
            <tr>
                <th>Equipo</th>
                <td><?php echo htmlspecialchars($orden['nombre_equipo']); ?></td>
                <th>Código</th>
                <td><?php echo htmlspecialchars($orden['codigo_equipo']); ?></td>
            </tr>
            <tr>
                <th>Acción de Mantenimiento</th>
                <td><?php echo htmlspecialchars($orden['accion_mantenimiento']); ?></td>
                <th>Responsable</th>
                <td><?php echo htmlspecialchars($orden['realizado_por']); ?></td>
            </tr>
            <tr>
                <th>Motivo</th>
                <td colspan="3"><?php echo htmlspecialchars($orden['motivo']); ?></td>
            </tr>
        </table>

        <div class="section-title">Descripción del Equipo</div>
        <p><?php echo nl2br(htmlspecialchars($orden['descripcion_equipo'])); ?></p>

        <div class="section-title">Herramientas Utilizadas</div>
        <p><?php echo nl2br(htmlspecialchars($orden['herramientas'])); ?></p>

        <?php if (!empty($orden['imagen_equipo'])): ?>
            <div class="section-title">Imagen/Foto del Equipo</div>
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($orden['imagen_equipo']); ?>" class="img-fluid rounded shadow" style="max-width:500px; width:100%; height:auto;" alt="Imagen equipo">
            </div>
        <?php endif; ?>

        <div class="section-title">Descripción de lo Realizado</div>
        <p><?php echo nl2br(htmlspecialchars($orden['descripcion_realizado'])); ?></p>

        <div class="section-title">Repuestos Utilizados</div>
        <p><?php echo nl2br(htmlspecialchars($orden['repuestos'])); ?></p>

        <div class="section-title">Horarios</div>
        <table class="tabla-borde">
            <tr>
                <th>Hora Inicio</th>
                <td><?php echo htmlspecialchars($orden['hora_inicio']); ?></td>
                <th>Hora Finalización</th>
                <td><?php echo htmlspecialchars($orden['hora_fin']); ?></td>
            </tr>
            <tr>
                <th>Responsable Encargado</th>
                <td colspan="3"><?php echo htmlspecialchars($orden['responsable']); ?></td>
            </tr>
        </table>

        <div class="section-title">Gastos Cargados a la Orden</div>
        <table class="tabla-borde">
            <tr>
                <th>Fecha Factura</th>
                <td><?php echo htmlspecialchars($orden['fecha_factura']); ?></td>
                <th>Archivo Factura</th>
                <td>
                    <?php if (!empty($orden['factura_imagen'])): ?>
                    <div class="campo"><strong>Factura:</strong><br>
                        <img src="<?= $orden['factura_imagen'] ?>"
                        style="max-width:500px; width:100%; height:auto;" 
                        alt="Factura">
                    </div>
                <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="text-center mt-4">
            <a href="listado_mantenimiento.php" class="btn btn-secondary">Volver al listado</a>
        </div>
    </div>
</body>
</html>
