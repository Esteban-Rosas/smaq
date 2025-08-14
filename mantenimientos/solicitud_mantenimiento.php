<?php
include '../includes/proteccion.php';
include '../includes/header.php';
include_once('../includes/conexion.php');

$usuario_sesion = $_SESSION['usuario_nombre'] ?? ''; 
// Obtener ubicaciones
$sql_ubicaciones = "SELECT id, nombre FROM ubicaciones ORDER BY nombre ASC";
$stmt_ubicaciones = $conexion->query($sql_ubicaciones);
$ubicaciones = $stmt_ubicaciones->fetchAll(PDO::FETCH_ASSOC);

// Obtener equipos con su ubicación
$sql = "SELECT id, nombre, codigo, ubicacion_id FROM equipos";
$stmt = $conexion->query($sql);
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Solicitud de Mantenimiento</h3>
        <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
                <div>
                    ¡Su solicitud de mantenimiento fue registrada correctamente!
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>
        <form id="formSolicitud" method="POST" action="../includes/guardar_solicitud.php">
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ubicacion_id" class="form-label">Ubicación:</label>
                    <select name="ubicacion_id" id="ubicacion_id" class="form-select" required>
                        <option value="">Seleccione una ubicación</option>
                        <?php foreach ($ubicaciones as $ubic): ?>
                            <option value="<?= $ubic['id']; ?>"><?= htmlspecialchars($ubic['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="equipo_id" class="form-label">Equipo:</label>
                    <select name="equipo_id" id="equipo_id" class="form-select" required>
                        <option value="">Seleccione un equipo</option>
                        <?php foreach ($equipos as $row): ?>
                            <option value="<?= $row['id']; ?>" data-codigo="<?= htmlspecialchars($row['codigo']); ?>" data-ubicacion="<?= $row['ubicacion_id']; ?>">
                                <?= htmlspecialchars($row['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="codigo_equipo" class="form-label">Código del Equipo:</label>
                    <input type="text" id="codigo_equipo" class="form-control" readonly>
                </div>
                <div class="col-md-4">
                    <label for="tipo_accion" class="form-label">Tipo de Acción:</label>
                    <select name="tipo_accion" id="tipo_accion" class="form-select" required>
                        <option value="">Seleccione</option>
                        <option value="Preventivo">Preventivo</option>
                        <option value="Correctivo">Correctivo</option>
                        <option value="Predictivo">Predictivo</option>
                        <option value="Inspección">Inspección</option>
                        <option value="Calibración">Calibración</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del problema:</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="5" required></textarea>
            </div>
            <!-- Campo oculto con el nombre del usuario de sesión -->
            <input type="hidden" name="operario" value="<?= htmlspecialchars($usuario_sesion) ?>">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                <a href="../dashboard.  php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const selectUbicacion = document.getElementById('ubicacion_id');
    const selectEquipo = document.getElementById('equipo_id');
    const inputCodigo = document.getElementById('codigo_equipo');

    // Guardar todas las opciones de equipos
    const allOptions = Array.from(selectEquipo.options);

    selectUbicacion.addEventListener('change', function() {
        const ubicacionId = selectUbicacion.value;
        // Limpiar selección de equipo y código
        selectEquipo.value = '';
        inputCodigo.value = '';

        // Filtrar opciones
        selectEquipo.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Seleccione un equipo';
        selectEquipo.appendChild(defaultOption);

        allOptions.forEach(option => {
            if (option.value === '') return; // Saltar la opción por defecto
            if (option.getAttribute('data-ubicacion') === ubicacionId) {
                selectEquipo.appendChild(option.cloneNode(true));
            }
        });
    });

    selectEquipo.addEventListener('change', function() {
        const selected = selectEquipo.options[selectEquipo.selectedIndex];
        inputCodigo.value = selected.getAttribute('data-codigo') || '';
    });
});
</script>
</body>
</html>
