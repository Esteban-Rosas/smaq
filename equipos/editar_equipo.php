<?php
include '../includes/proteccion.php';
?>

<?php
// filepath: c:\xampp\htdocs\smaq\equipos\editar_equipo.php
session_start();
include '../includes/header.php';
include_once('../includes/conexion.php');

// Procesar el formulario si se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $ubicacion_id = $_POST['ubicacion_id'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $serie = $_POST['serie'];
    $destinado = $_POST['destinado'];
    $forma_adquisicion = $_POST['forma_adquisicion'];
    $vida_util = $_POST['vida_util'];
    $fecha_adquisicion = $_POST['fecha_adquisicion'];
    $fecha_garantia = $_POST['fecha_garantia'];
    $fecha_instalacion = $_POST['fecha_instalacion'];
    $fabricante = $_POST['fabricante'];
    $contacto_fabricante = $_POST['contacto_fabricante'];
    $pais = $_POST['pais'];
    $tecnologia = $_POST['tecnologia'];
    $frecuencia = $_POST['frecuencia'];
    $voltaje = $_POST['voltaje'];
    $corriente = $_POST['corriente'];
    $peso = $_POST['peso'];
    $capacidad = $_POST['capacidad'];
    $fuente_alimentacion = $_POST['fuente_alimentacion'];

    // Manejo de la foto (opcional)
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $foto_nombre = 'foto_' . uniqid() . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $ruta_destino = '../uploads/' . $foto_nombre;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            $foto = "../uploads/" . $foto_nombre;
        }
    }

    // Actualizar en la base de datos
    $sql = "UPDATE equipos SET
        nombre = :nombre,
        ubicacion_id = :ubicacion_id,
        marca = :marca,
        modelo = :modelo,
        serie = :serie,
        destinado = :destinado,
        forma_adquisicion = :forma_adquisicion,
        vida_util = :vida_util,
        fecha_adquisicion = :fecha_adquisicion,
        fecha_garantia = :fecha_garantia,
        fecha_instalacion = :fecha_instalacion,
        fabricante = :fabricante,
        contacto_fabricante = :contacto_fabricante,
        pais = :pais,
        tecnologia = :tecnologia,
        frecuencia = :frecuencia,
        voltaje = :voltaje,
        corriente = :corriente,
        peso = :peso,
        capacidad = :capacidad,
        fuente_alimentacion = :fuente_alimentacion"
        . ($foto ? ", foto = :foto" : "") . "
        WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $params = [
        ':nombre' => $nombre,
        ':ubicacion_id' => $ubicacion_id,
        ':marca' => $marca,
        ':modelo' => $modelo,
        ':serie' => $serie,
        ':destinado' => $destinado,
        ':forma_adquisicion' => $forma_adquisicion,
        ':vida_util' => $vida_util,
        ':fecha_adquisicion' => $fecha_adquisicion,
        ':fecha_garantia' => $fecha_garantia,
        ':fecha_instalacion' => $fecha_instalacion,
        ':fabricante' => $fabricante,
        ':contacto_fabricante' => $contacto_fabricante,
        ':pais' => $pais,
        ':tecnologia' => $tecnologia,
        ':frecuencia' => $frecuencia,
        ':voltaje' => $voltaje,
        ':corriente' => $corriente,
        ':peso' => $peso,
        ':capacidad' => $capacidad,
        ':fuente_alimentacion' => $fuente_alimentacion,
        ':id' => $id
    ];
    if ($foto) {
        $params[':foto'] = $foto;
    }
    $stmt->execute($params);

    // Redirigir al listado
    header("Location: listado_equipos.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de equipo no válido.");
}
$id = $_GET['id'];

$sql_ubicaciones = "SELECT id, nombre FROM ubicaciones ORDER BY nombre ASC";
$stmt = $conexion->query($sql_ubicaciones);
$ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM equipos WHERE id = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$equipo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipo) {
    die("Equipo no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Equipo - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Editar Equipo</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $equipo['id'] ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre del Equipo</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($equipo['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ubicacion_id" class="form-label">Ubicación</label>
                        <select name="ubicacion_id" class="form-select" required>
                            <option value="">Seleccione una ubicación</option>
                            <?php foreach ($ubicaciones as $row): ?>
                                <option value="<?= $row['id'] ?>" <?= $row['id'] == $equipo['ubicacion_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Marca</label>
                        <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($equipo['marca']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Modelo</label>
                        <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($equipo['modelo']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Serie</label>
                        <input type="text" name="serie" class="form-control" value="<?= htmlspecialchars($equipo['serie']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Destinado para</label>
                    <textarea name="destinado" class="form-control" rows="2"><?= htmlspecialchars($equipo['destinado']) ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Forma de Adquisición</label>
                        <input type="text" name="forma_adquisicion" class="form-control" value="<?= htmlspecialchars($equipo['forma_adquisicion']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Vida Útil (años)</label>
                        <input type="number" name="vida_util" class="form-control" value="<?= htmlspecialchars($equipo['vida_util']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Fecha de Adquisición</label>
                        <input type="date" name="fecha_adquisicion" class="form-control" value="<?= htmlspecialchars($equipo['fecha_adquisicion']) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Fecha Fin Garantía</label>
                        <input type="date" name="fecha_garantia" class="form-control" value="<?= htmlspecialchars($equipo['fecha_garantia']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Fecha Instalación</label>
                        <input type="date" name="fecha_instalacion" class="form-control" value="<?= htmlspecialchars($equipo['fecha_instalacion']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Fabricante</label>
                        <input type="text" name="fabricante" class="form-control" value="<?= htmlspecialchars($equipo['fabricante']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Contacto Fabricante</label>
                    <input type="text" name="contacto_fabricante" class="form-control" value="<?= htmlspecialchars($equipo['contacto_fabricante']) ?>">
                </div>

                <div class="mb-3">
                    <label>País</label>
                    <input type="text" name="pais" class="form-control" value="<?= htmlspecialchars($equipo['pais']) ?>">
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Tecnología</label>
                        <input type="text" name="tecnologia" class="form-control" value="<?= htmlspecialchars($equipo['tecnologia']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Frecuencia (Hz)</label>
                        <input type="text" name="frecuencia" class="form-control" value="<?= htmlspecialchars($equipo['frecuencia']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Voltaje (V)</label>
                        <input type="text" name="voltaje" class="form-control" value="<?= htmlspecialchars($equipo['voltaje']) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Corriente (A)</label>
                        <input type="text" name="corriente" class="form-control" value="<?= htmlspecialchars($equipo['corriente']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Peso (kg)</label>
                        <input type="text" name="peso" class="form-control" value="<?= htmlspecialchars($equipo['peso']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Capacidad</label>
                        <input type="text" name="capacidad" class="form-control" value="<?= htmlspecialchars($equipo['capacidad']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Fuente de Alimentación</label>
                    <input type="text" name="fuente_alimentacion" class="form-control" value="<?= htmlspecialchars($equipo['fuente_alimentacion']) ?>">
                </div>

                <div class="mb-3">
                    <label>Actualizar Foto del Equipo</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                <a href="listado_equipos.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
