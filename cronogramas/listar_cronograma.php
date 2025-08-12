<?php
include '../includes/proteccion.php';
?>

<?php
include '../includes/conexion.php';
include '../includes/header.php';

// Obtener todos los cronogramas con información del equipo
$sql = "SELECT c.*, e.nombre AS equipo_nombre, e.codigo AS equipo_codigo
        FROM cronogramas c
        JOIN equipos e ON c.equipo_id = e.id
        ORDER BY c.fecha_inicio ASC";
$stmt = $conexion->query($sql);
$cronogramas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensaje de éxito
$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

$esIngeniero = (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'ingeniero');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Cronogramas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .is-valid {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.1rem rgba(40, 167, 69, .25);
        }
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, .25);
        }
    </style>

</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Listado de Cronogramas</h5>
        </div>
        <div class="card-body">
            <?php if ($mensaje_exito): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensaje_exito) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Equipo</th>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($cronogramas) > 0): ?>
                            <?php foreach ($cronogramas as $i => $row): ?>
                                <tr data-id="<?= $row['id'] ?>">
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($row['equipo_nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['equipo_codigo']) ?></td>
                                    <td><?= ucfirst($row['tipo_mantenimiento']) ?></td>
                                    <!-- Editable fecha_inicio -->
                                    <td>
                                        <input type="date" class="form-control form-control-sm editable"
                                               data-campo="fecha_inicio"
                                               value="<?= htmlspecialchars($row['fecha_inicio']) ?>">
                                    </td>
                                    <!-- Editable fecha_fin -->
                                    <td>
                                        <input type="date" class="form-control form-control-sm editable"
                                               data-campo="fecha_fin"
                                               value="<?= htmlspecialchars($row['fecha_fin']) ?>">
                                    </td>
                                    <!-- Responsable solo texto -->
                                    <td>
                                        <?= htmlspecialchars($row['responsable']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted">No hay cronogramas registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="crear_cronograma.php" class="btn btn-secondary mt-3">Crear Cronograma</a>
            <a href="../dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
        <a href="visualizar_cronograma.php" class="btn btn-warning btn-lg float-end">
            Ver cronograma maestro
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script>
function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${tipo} border-0 show`;
    toast.role = 'alert';
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${mensaje}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

document.querySelectorAll('.editable').forEach(input => {
    input.addEventListener('change', function () {
        const row = this.closest('tr');
        const id = row.dataset.id;
        const campo = this.dataset.campo;
        const valor = this.value;

        const fechaInicioInput = row.querySelector('input[data-campo="fecha_inicio"]');
        const fechaFinInput = row.querySelector('input[data-campo="fecha_fin"]');
        const fechaInicio = fechaInicioInput.value;
        const fechaFin = fechaFinInput.value;

        // Validación cruzada de fechas
        if (campo === 'fecha_inicio' && fechaFin && fechaFin < valor) {
            this.classList.add('is-invalid');
            mostrarToast("La fecha fin no puede ser menor que la fecha de inicio.", 'danger');
            return;
        }
        if (campo === 'fecha_fin' && fechaInicio && valor < fechaInicio) {
            this.classList.add('is-invalid');
            mostrarToast("La fecha fin no puede ser menor que la fecha de inicio.", 'danger');
            return;
        }

        // Limpia clases anteriores
        this.classList.remove('is-invalid', 'is-valid');
        this.disabled = true;

        fetch('actualizar_cronograma.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, campo, valor })
        })
        .then(res => res.json())
        .then(data => {
            this.disabled = false;
            if (data.success) {
                this.classList.add('is-valid');
                mostrarToast(`¡${campo.replace('_', ' ')} actualizada!`, 'success');
                setTimeout(() => this.classList.remove('is-valid'), 1500);
            } else {
                this.classList.add('is-invalid');
                mostrarToast("Error al guardar: " + (data.error || "Error desconocido"), 'danger');
            }
        })
        .catch(err => {
            this.disabled = false;
            this.classList.add('is-invalid');
            mostrarToast("Error de red: " + err.message, 'danger');
        });
    });
});
</script>
