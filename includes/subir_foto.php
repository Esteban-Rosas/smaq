<?php
$directorio = '../uploads/'; // Cambia si deseas otro destino
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true); // Crea la carpeta si no existe
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombreTemporal = $_FILES['foto']['tmp_name'];
        $nombreOriginal = basename($_FILES['foto']['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Generar nombre único para evitar sobrescritura
        $nuevoNombre = uniqid('foto_', true) . '.' . $extension;
        $rutaDestino = $directorio . $nuevoNombre;

        // Validar que sea imagen
        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $tiposPermitidos)) {
            die("Tipo de archivo no permitido.");
        }

        if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
            echo "Foto subida exitosamente: <a href='$rutaDestino' target='_blank'>Ver imagen</a>";
        } else {
            echo "Error al mover la foto al destino.";
        }
    } else {
        echo "No se subió ninguna foto o hubo un error.";
    }
}
?>
