<?php
// filepath: c:\xampp\htdocs\smaq\equipos\guardar_componente.php
include '../includes/conexion.php';

$equipo_id = $_POST['equipo_id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$ruta_imagen = null;

// Manejo de la imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $nombre_imagen = uniqid('comp_') . '_' . basename($_FILES['imagen']['name']);
    $ruta_destino = "../uploads/componentes/" . $nombre_imagen;

    // Mueve la imagen subida a la carpeta destino
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
        $ruta_imagen = "../uploads/componentes/" . $nombre_imagen;

        // Redimensionar si es necesario
        list($ancho, $alto, $tipo) = getimagesize($ruta_destino);
        $max_ancho = 1024;
        if ($ancho > $max_ancho) {
            $nuevo_alto = intval($alto * $max_ancho / $ancho);

            // Crear imagen según tipo
            switch ($tipo) {
                case IMAGETYPE_JPEG:
                    $origen = imagecreatefromjpeg($ruta_destino);
                    break;
                case IMAGETYPE_PNG:
                    $origen = imagecreatefrompng($ruta_destino);
                    break;
                default:
                    $origen = null;
            }

            if ($origen) {
                $imagen_redimensionada = imagecreatetruecolor($max_ancho, $nuevo_alto);
                imagecopyresampled($imagen_redimensionada, $origen, 0, 0, 0, 0, $max_ancho, $nuevo_alto, $ancho, $alto);

                // Sobrescribe la imagen original
                if ($tipo == IMAGETYPE_JPEG) {
                    imagejpeg($imagen_redimensionada, $ruta_destino, 85); // 85% calidad
                } elseif ($tipo == IMAGETYPE_PNG) {
                    imagepng($imagen_redimensionada, $ruta_destino, 8);
                }
                imagedestroy($imagen_redimensionada);
                imagedestroy($origen);
            }
        }
    }
}

// Insertar en la base de datos
$sql = "INSERT INTO componentes (equipo_id, nombre, descripcion, imagen) VALUES (:equipo_id, :nombre, :descripcion, :imagen)";
$stmt = $conexion->prepare($sql);
$stmt->execute([
    ':equipo_id' => $equipo_id,
    ':nombre' => $nombre,
    ':descripcion' => $descripcion,
    ':imagen' => $ruta_imagen
]);

header("Location: listado_equipos.php");
exit;