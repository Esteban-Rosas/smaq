<?php
include '../includes/proteccion.php';
?>

<?php
include_once('../includes/conexion.php');
include_once('../includes/header.php'); 

// Obtener equipos
$equipos = $conexion->query("SELECT id, nombre FROM equipos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Obtener cronogramas
$sql = "SELECT c.equipo_id, c.tipo_mantenimiento, c.fecha_inicio, c.fecha_fin, e.nombre AS equipo_nombre
        FROM cronogramas c
        JOIN equipos e ON c.equipo_id = e.id";
$cronogramas = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Organizar datos por equipo, mes y semana
function getWeekOfMonth($date) {
    $firstDay = date('Y-m-01', strtotime($date));
    return ceil((date('d', strtotime($date)) + date('N', strtotime($firstDay)) - 1) / 7);
}

$data = [];
foreach ($cronogramas as $c) {
    $id = $c['equipo_id'];
    $tipo = strtolower(trim($c['tipo_mantenimiento']));
    $start = strtotime($c['fecha_inicio']);
    $end = strtotime($c['fecha_fin']);
    while ($start <= $end) {
        $month = (int)date('n', $start); // 1-12
        $week = getWeekOfMonth(date('Y-m-d', $start)); // 1-5
        $data[$id][$month][$week][] = $tipo;
        $start = strtotime('+1 day', $start);
    }
}

$colores = [
    'mecánico' => 'bg-success text-white',
    'eléctrico' => 'bg-warning text-dark',
    'lubricación' => 'bg-info text-white',
    'limpieza' => 'bg-danger text-white'
];
$iniciales = [
    'mecánico' => 'M',
    'eléctrico' => 'E',
    'lubricación' => 'L',
    'limpieza' => 'I'
];
?>

<?php
$esIngeniero = (isset($_SESSION['usuario_rol']) && strtolower(trim($_SESSION['usuario_rol'])) === 'ingeniero');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cronograma - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-cronograma th, .table-cronograma td {
            text-align: center;
            vertical-align: middle;
            font-size: 0.9em;
            padding: 4px;
        }
        .legend-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 4px;
            margin-right: 6px;
        }
        .hidden { display: none !important; }
        @media (max-width: 576px) {
            .table-cronograma th, .table-cronograma td {
                font-size: 0.75em;
                padding: 2px;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Cronograma de Mantenimientos</h4>
        <div>
            <label class="me-2">Trimestre:</label>
            <div class="btn-group" role="group" aria-label="Trimestres" id="trimestre-group">
                <button type="button" class="btn btn-outline-primary active" data-trimestre="1">Ene-Mar</button>
                <button type="button" class="btn btn-outline-primary" data-trimestre="2">Abr-Jun</button>
                <button type="button" class="btn btn-outline-primary" data-trimestre="3">Jul-Sep</button>
                <button type="button" class="btn btn-outline-primary" data-trimestre="4">Oct-Dic</button>
            </div>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="mb-3">
        <strong>Leyenda:</strong>
        <?php foreach ($colores as $tipo => $clase): ?>
            <span class="legend-box <?= $clase ?>"></span> <?= ucfirst($tipo) ?>
        <?php endforeach; ?>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-cronograma" id="tabla-cronograma">
            <thead class="table-light">
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Equipo</th>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <th colspan="4" class="mes mes-<?= $m ?>"><?= date('M', mktime(0, 0, 0, $m, 1)) ?></th>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <?php for ($s = 1; $s <= 4; $s++): ?>
                            <th class="mes mes-<?= $m ?>"><?= $s ?></th>
                        <?php endfor; ?>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $i => $equipo): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($equipo['nombre']) ?></td>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <?php for ($s = 1; $s <= 4; $s++): ?>
                                <td class="mes mes-<?= $m ?>">
                                    <?php
                                    $actividades = $data[$equipo['id']][$m][$s] ?? [];
                                    foreach (array_unique($actividades) as $tipo) {
                                        $clase = $colores[$tipo] ?? 'bg-secondary text-white';
                                        $letra = $iniciales[$tipo] ?? '?';
                                        echo "<span class='badge $clase' title='" . ucfirst($tipo) . "'>$letra</span> ";
                                    }
                                    ?>
                                </td>
                            <?php endfor; ?>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Después de la tabla, agrega el botón de volver -->
    <?php if ($esIngeniero): ?>
        <div class="d-flex justify-content-end mt-3">
            <a href="listar_cronograma.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a listado de cronogramas
            </a>
        </div>
    <?php endif; ?>
    <a href="exportar_cronograma.php" class="btn btn-success">
    <i class="bi bi-download"></i> Exportar a Excel
</a>

<!-- Modal de confirmación PDF -->
<div class="modal fade" id="modalPdfGuardado" tabindex="-1" aria-labelledby="modalPdfGuardadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header border-0 justify-content-center">
        <h5 class="modal-title" id="modalPdfGuardadoLabel">
          <i class="bi bi-file-earmark-pdf text-danger" style="font-size:2rem;"></i>
        </h5>
      </div>
      <div class="modal-body">
        <p class="mb-3 fw-bold">¡El archivo PDF se guardó correctamente!</p>
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Botones de trimestre
    const btns = document.querySelectorAll('#trimestre-group button');
    function actualizarTabla(tri) {
        const mesesPorTrimestre = {
            1: [1, 2, 3],
            2: [4, 5, 6],
            3: [7, 8, 9],
            4: [10, 11, 12],
        };
        const mesesMostrar = mesesPorTrimestre[tri];

        // Ocultar todos
        document.querySelectorAll('.mes').forEach(el => el.classList.add('hidden'));

        // Mostrar los meses del trimestre seleccionado
        mesesMostrar.forEach(m => {
            document.querySelectorAll('.mes-' + m).forEach(el => el.classList.remove('hidden'));
        });
    }

    btns.forEach(btn => {
        btn.addEventListener('click', function () {
            btns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const valor = parseInt(this.dataset.trimestre);
            actualizarTabla(valor);
        });
    });

    // Mostrar por defecto T1
    actualizarTabla(1);

    // Mostrar modal si se guardó el PDF
    if (window.location.search.includes('pdf=ok')) {
        var modal = new bootstrap.Modal(document.getElementById('modalPdfGuardado'));
        modal.show();
    }
});
</script>
</body>
</html>
