<?php
include '../includes/conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de equipo no válido.");
}

$id = $_GET['id'];
$sql = "SELECT e.*, u.nombre AS ubicacion
        FROM equipos e
        LEFT JOIN ubicaciones u ON e.ubicacion_id = u.id
        WHERE e.id = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$equipo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipo) {
    die("Equipo no encontrado.");
}

// Obtener componentes asociados a este equipo
$sql_comp = "SELECT * FROM componentes WHERE equipo_id = :equipo_id";
$stmt_comp = $conexion->prepare($sql_comp);
$stmt_comp->execute([':equipo_id' => $id]);
$componentes = $stmt_comp->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Vida del Equipo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .section-title {
            background-color: #6c757d;
            color: white;
            padding: 8px;
            margin-top: 20px;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
        .titulo {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
<div class="container bg-white p-4 mt-4 shadow-sm">

    <div class="text-center mb-4">
        <div class="titulo">HOJA DE VIDA DE EQUIPO</div>
        <div>Fecha de Actualización: <?= date('d-m-Y') ?></div>
    </div>

    <div class="row mb-4 align-items-center">
        <?php if (!empty($equipo['foto'])): ?>
            <div class="col-md-4 text-center mb-3 mb-md-0">
                <img src="<?= str_replace('..', '/smaq', htmlspecialchars($equipo['foto'])) ?>" alt="Foto del equipo" class="img-fluid rounded shadow" style="max-width:150%;max-height:270px;">
            </div>
        <?php endif; ?>
        <div class="col-md-8">
            <div class="section-title">Datos del Equipo</div>
            <table class="table table-bordered">
                <tr><th>Nombre</th><td><?= htmlspecialchars($equipo['nombre']) ?></td></tr>
                <tr><th>Código</th><td><?= htmlspecialchars($equipo['codigo']) ?></td></tr>
                <tr><th>Ubicación</th><td><?= htmlspecialchars($equipo['ubicacion']) ?></td></tr>
                <tr><th>Marca</th><td><?= htmlspecialchars($equipo['marca']) ?></td></tr>
                <tr><th>Modelo</th><td><?= htmlspecialchars($equipo['modelo']) ?></td></tr>
                <tr><th>Serie</th><td><?= htmlspecialchars($equipo['serie']) ?></td></tr>
                <tr><th>Destinado para</th><td><?= htmlspecialchars($equipo['destinado']) ?></td></tr>
                <tr><th>Fecha de Registro</th><td><?= date('d/m/Y', strtotime($equipo['fecha_registro'])) ?></td></tr>
            </table>
        </div>
    </div>

    <!-- Registro histórico -->
    <div class="section-title">Registro Histórico</div>
    <table class="table table-bordered">
        <tr><th>Forma de Adquisición</th><td><?= htmlspecialchars($equipo['forma_adquisicion']) ?></td></tr>
        <tr><th>Fecha Adquisición</th><td><?= htmlspecialchars($equipo['fecha_adquisicion']) ?></td></tr>
        <tr><th>Vida Útil</th><td><?= htmlspecialchars($equipo['vida_util']) ?> años</td></tr>
        <tr><th>Fecha Fin Garantía</th><td><?= htmlspecialchars($equipo['fecha_garantia']) ?></td></tr>
        <tr><th>Fecha Instalación</th><td><?= htmlspecialchars($equipo['fecha_instalacion']) ?></td></tr>
        <tr><th>Fabricante</th><td><?= htmlspecialchars($equipo['fabricante']) ?></td></tr>
        <tr><th>País</th><td><?= htmlspecialchars($equipo['pais']) ?></td></tr>
        <tr><th>Contacto del Fabricante</th><td><?= htmlspecialchars($equipo['contacto_fabricante']) ?></td></tr>
    </table>

    <!-- Características técnicas -->
    <div class="section-title">Características Técnicas</div>
    <table class="table table-bordered">
        <tr>
            <th>Tecnología</th><td><?= htmlspecialchars($equipo['tecnologia']) ?></td>
            <th>Frecuencia</th><td><?= htmlspecialchars($equipo['frecuencia']) ?></td>
        </tr>
        <tr>
            <th>Voltaje</th><td><?= htmlspecialchars($equipo['voltaje']) ?></td>
            <th>Corriente</th><td><?= htmlspecialchars($equipo['corriente']) ?></td>
        </tr>
        <tr>
            <th>Capacidad</th><td><?= htmlspecialchars($equipo['capacidad']) ?></td>
            <th>Peso</th><td><?= htmlspecialchars($equipo['peso']) ?></td>
        </tr>
    </table>

    <!-- Fuente de alimentación -->
    <div class="section-title">Fuente de Alimentación</div>
    <p><?= htmlspecialchars($equipo['fuente_alimentacion']) ?></p>

    <!-- Observaciones y Recomendaciones -->
    <?php if (!empty($equipo['observaciones'])): ?>
        <div class="section-title">Observaciones y Recomendaciones</div>
        <p><?= nl2br(htmlspecialchars($equipo['observaciones'])) ?></p>
    <?php endif; ?>

    <!-- Rutina de Mantenimiento -->
    <?php if (!empty($equipo['rutina_mantenimiento'])): ?>
        <div class="section-title">Rutina de Mantenimiento Preventivo</div>
        <p><?= nl2br(htmlspecialchars($equipo['rutina_mantenimiento'])) ?></p>
    <?php endif; ?>

    <?php if (count($componentes) > 0): ?>
        <div class="section-title">Componentes del Equipo</div>
        <div class="row">
            <?php foreach ($componentes as $componente): ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <?php if (!empty($componente['imagen'])): ?>
                            <img src="<?= str_replace('..', '/smaq', htmlspecialchars($componente['imagen'])) ?>" class="card-img-top" alt="Imagen del componente" style="max-height:180px;object-fit:contain;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($componente['nombre']) ?></h6>
                            <?php if (!empty($componente['descripcion'])): ?>
                                <p class="card-text"><?= nl2br(htmlspecialchars($componente['descripcion'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="section-title">Componentes del Equipo</div>
        <p class="text-muted">No hay componentes registrados para este equipo.</p>
    <?php endif; ?>

    <!-- Botón volver -->
    <div class="text-center mt-4">
        <a href="listado_equipos.php" class="btn btn-secondary">Volver al listado</a>
    </div>

</div>
</body>
</html>
