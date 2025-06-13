<?php
require_once '../includes/conexion.php';
session_start();

// Crear directorio de uploads si no existe
$directorio = '../uploads/';
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// Manejo de la foto
$foto_ruta = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $nombreTemporal = $_FILES['foto']['tmp_name'];
    $nombreOriginal = basename($_FILES['foto']['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    // Validar tipo de archivo
    $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $tiposPermitidos)) {
        die("Tipo de archivo no permitido.");
    }

    // Generar nombre único
    $nuevoNombre = uniqid('foto_', true) . '.' . $extension;
    $rutaDestino = $directorio . $nuevoNombre;

    if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
        $foto_ruta = $rutaDestino; // Ruta completa o relativa
    }
}

// Procesar POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre              = $_POST['nombre'];
    $ubicacion_id        = $_POST['ubicacion_id'];
    $marca               = $_POST['marca'];
    $modelo              = $_POST['modelo'];
    $serie               = $_POST['serie'];
    $destinado           = $_POST['destinado'];
    $forma_adquisicion   = $_POST['forma_adquisicion'];
    $vida_util           = $_POST['vida_util'];
    $fecha_adquisicion   = $_POST['fecha_adquisicion'];
    $fecha_garantia      = $_POST['fecha_garantia'];
    $fecha_instalacion   = $_POST['fecha_instalacion'];
    $fabricante          = $_POST['fabricante'];
    $contacto_fabricante = $_POST['contacto_fabricante'];
    $pais                = $_POST['pais'];
    $tecnologia          = $_POST['tecnologia'];
    $frecuencia          = $_POST['frecuencia'];
    $voltaje             = $_POST['voltaje'];
    $corriente           = $_POST['corriente'];
    $peso                = $_POST['peso'];
    $capacidad           = $_POST['capacidad'];
    $fuente_alimentacion = $_POST['fuente_alimentacion'];
    $codigo               = $_POST['codigo'];

    try {
        $sql = "INSERT INTO equipos (
                    codigo, nombre, ubicacion_id, marca, modelo, serie, destinado,
                    forma_adquisicion, vida_util, fecha_adquisicion, fecha_garantia,
                    fecha_instalacion, fabricante, contacto_fabricante, pais, tecnologia,
                    frecuencia, voltaje, corriente, peso, capacidad, fuente_alimentacion, foto
                ) VALUES (
                    :codigo, :nombre, :ubicacion_id, :marca, :modelo, :serie, :destinado,
                    :forma_adquisicion, :vida_util, :fecha_adquisicion, :fecha_garantia,
                    :fecha_instalacion, :fabricante, :contacto_fabricante, :pais, :tecnologia,
                    :frecuencia, :voltaje, :corriente, :peso, :capacidad, :fuente_alimentacion, :foto
                )";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':codigo'               => $codigo,
            ':nombre'              => $nombre,
            ':ubicacion_id'        => $ubicacion_id,
            ':marca'               => $marca,
            ':modelo'              => $modelo,
            ':serie'               => $serie,
            ':destinado'           => $destinado,
            ':forma_adquisicion'   => $forma_adquisicion,
            ':vida_util'           => $vida_util,
            ':fecha_adquisicion'   => $fecha_adquisicion,
            ':fecha_garantia'      => $fecha_garantia,
            ':fecha_instalacion'   => $fecha_instalacion,
            ':fabricante'          => $fabricante,
            ':contacto_fabricante' => $contacto_fabricante,
            ':pais'                => $pais,
            ':tecnologia'          => $tecnologia,
            ':frecuencia'          => $frecuencia,
            ':voltaje'             => $voltaje,
            ':corriente'           => $corriente,
            ':peso'                => $peso,
            ':capacidad'           => $capacidad,
            ':fuente_alimentacion' => $fuente_alimentacion,
            ':foto'                => $foto_ruta
        ]);

        $_SESSION['mensaje_exito'] = '¡Equipo registrado correctamente!';
        header("Location: ../equipos/registrar.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al guardar el equipo: " . $e->getMessage();
    }
}

$manuales = [
    'manual_operativo',
    'manual_servicio',
    'manual_partes',
    'manual_instalacion',
    'plano_electrico',
    'plano_electronico',
    'plano_hidraulico',
    'plano_neumatico'
];

$rutasManuales = [];

foreach ($manuales as $manual) {
    if (isset($_FILES[$manual]) && $_FILES[$manual]['error'] === UPLOAD_ERR_OK) {
        $nombreOriginal = basename($_FILES[$manual]['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if ($extension === 'pdf') {
            $nuevoNombre = uniqid($manual . '_', true) . '.pdf';
            $rutaDestino = "../uploads/pdf/" . $nuevoNombre;
            if (move_uploaded_file($_FILES[$manual]['tmp_name'], $rutaDestino)) {
                $rutasManuales[$manual] = $nuevoNombre; // Guarda solo el nombre o la ruta relativa
            } else {
                $rutasManuales[$manual] = null;
            }
        } else {
            $rutasManuales[$manual] = null;
        }
    } else {
        $rutasManuales[$manual] = null;
    }
}

?>
