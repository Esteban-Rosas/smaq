<?php
// cronogramas/crear_cronograma.php
include '../includes/conexion.php';
include '../includes/header.php';

// Obtener lista de equipos
$sql = "SELECT id, nombre, codigo FROM equipos ORDER BY nombre ASC";
$stmt = $conexion->query($sql);
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Crear Cronograma - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Crear Cronograma de Mantenimiento</h5>
        </div>
        <div class="card-body">
            <?php if ($mensaje_exito): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($mensaje_exito) ?>
                </div>
            <?php endif; ?>

            <form action="guardar_cronograma.php" method="POST">
                <div class="mb-3">
                    <label for="equipo_id" class="form-label">Equipo</label>
                    <select name="equipo_id" id="equipo_id" class="form-select" required>
                        <option value="">Seleccione un equipo</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?= $equipo['id'] ?>">
                                <?= htmlspecialchars($equipo['nombre']) ?> (<?= htmlspecialchars($equipo['codigo']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipo_mantenimiento" class="form-label">Tipo de Mantenimiento</label>
                    <select name="tipo_mantenimiento" id="tipo_mantenimiento" class="form-select" required>
                        <option value="">Seleccione</option>
                        <option value="preventivo">Preventivo</option>
                        <option value="correctivo">Correctivo</option>
                        <option value="predictivo">Predictivo</option>
                        <option value="calibración">Calibración</option>
                        <option value="inspección">Inspección</option>
                    </select>
                </div>
                    
                <div class="mb-3">
                    <label for="responsable" class="form-label">Responsable</label>
                    <input type="text" name="responsable" id="responsable" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Fecha Final</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                    <div class="form-text text-danger" id="msg_fecha"></div>
                </div>

                <button type="submit" class="btn btn-success">Guardar Cronograma</button>
                <a href="../dashboard.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const msg = document.getElementById('msg_fecha');

    fechaInicio.addEventListener('change', function() {
        if (fechaInicio.value) {
            // Calcular fecha mínima para fecha_fin (inicio + 5 días)
            const inicio = new Date(fechaInicio.value);
            const minFin = new Date(inicio);
            minFin.setDate(inicio.getDate() + 5);

            // Formatear a yyyy-mm-dd
            const yyyy = minFin.getFullYear();
            const mm = String(minFin.getMonth() + 1).padStart(2, '0');
            const dd = String(minFin.getDate()).padStart(2, '0');
            const minFinStr = `${yyyy}-${mm}-${dd}`;

            fechaFin.min = minFinStr;

            // Si la fecha fin actual es menor, la limpia
            if (fechaFin.value && fechaFin.value < minFinStr) {
                fechaFin.value = '';
                msg.textContent = 'La fecha final debe ser al menos 5 días después de la fecha de inicio.';
            } else {
                msg.textContent = '';
            }
        } else {
            fechaFin.min = '';
            msg.textContent = '';
        }
    });

    fechaFin.addEventListener('change', function() {
        if (fechaInicio.value && fechaFin.value) {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);
            const diff = (fin - inicio) / (1000 * 60 * 60 * 24);
            if (diff < 5) {
                msg.textContent = 'La fecha final debe ser al menos 5 días después de la fecha de inicio.';
                fechaFin.value = '';
            } else {
                msg.textContent = '';
            }
        }
    });
});
</script>
</body>
</html>
