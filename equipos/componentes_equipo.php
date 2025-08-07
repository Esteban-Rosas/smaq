<?php
include '../includes/proteccion.php';
?>

<?php
// filepath: c:\xampp\htdocs\smaq\equipos\componentes_equipo.php
include '../includes/conexion.php';

if (!isset($_GET['equipo_id']) || !is_numeric($_GET['equipo_id'])) {
    die("ID de equipo no válido.");
}
$equipo_id = $_GET['equipo_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Componente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">Agregar Componente al Equipo #<?= $equipo_id ?></h5>
        </div>
        <div class="card-body">
            <form action="guardar_componente.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="equipo_id" value="<?= $equipo_id ?>">

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Componente</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen del Componente (opcional)</label>
                    <input type="file" class="form-control" name="imagen" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Guardar Componente</button>
                <a href="editar_equipo.php?id=<?= $equipo_id ?>" class="btn btn-secondary">Volver</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
