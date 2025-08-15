<?php

require_once __DIR__ . '/conexion.php';
session_start();

// Crear directorios si no existen
$directorioImagenes = '../uploads/';
$directorioPDFs = '../uploads/pdf/';

if (!file_exists($directorioImagenes)) {
    mkdir($directorioImagenes, 0777, true);
}

if (!file_exists($directorioPDFs)) {
    mkdir($directorioPDFs, 0777, true);
}

// Manejo de la foto
$foto_ruta = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $nombreTemporal = $_FILES['foto']['tmp_name'];
    $nombreOriginal = basename($_FILES['foto']['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    // Validar tipo de archivo
    $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($extension, $tiposPermitidos)) {
        // Generar nombre único
        $nuevoNombre = uniqid('foto_', true) . '.' . $extension;
        $rutaDestino = $directorioImagenes . $nuevoNombre;

        if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
            $foto_ruta = $nuevoNombre; // Guardar solo el nombre del archivo
        }
    }
}

// Procesar POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $datos = [
        'codigo' => $_POST['codigo'],
        'nombre' => $_POST['nombre'],
        'ubicacion_id' => $_POST['ubicacion_id'],
        'marca' => $_POST['marca'],
        'modelo' => $_POST['modelo'],
        'serie' => $_POST['serie'],
        'destinado' => $_POST['destinado'],
        'forma_adquisicion' => $_POST['forma_adquisicion'],
        'vida_util' => $_POST['vida_util'],
        'fecha_adquisicion' => $_POST['fecha_adquisicion'],
        'fecha_garantia' => $_POST['fecha_garantia'] ?: null,
        'fecha_instalacion' => $_POST['fecha_instalacion'] ?: null,
        'fabricante' => $_POST['fabricante'] ?: null,
        'contacto_fabricante' => $_POST['contacto_fabricante'] ?: null,
        'pais' => $_POST['pais'] ?: null,
        'tecnologia' => $_POST['tecnologia'] ?: null,
        'frecuencia' => $_POST['frecuencia'] ?: null,
        'voltaje' => $_POST['voltaje'] ?: null,
        'corriente' => $_POST['corriente'] ?: null,
        'peso' => $_POST['peso'] ?: null,
        'capacidad' => $_POST['capacidad'] ?: null,
        'fuente_alimentacion' => $_POST['fuente_alimentacion'],
        'foto' => $foto_ruta
    ];

    // Validación de campos obligatorios
    $camposObligatorios = ['codigo', 'nombre', 'ubicacion_id', 'marca', 'modelo', 'serie', 'destinado', 'forma_adquisicion', 'vida_util', 'fecha_adquisicion', 'fuente_alimentacion'];
    foreach ($camposObligatorios as $campo) {
        if (empty($datos[$campo])) {
            $_SESSION['mensaje_exito'] = "El campo '$campo' es obligatorio.";
            header("Location: ../equipos/registrar.php");
            exit;
        }
    }

    try {
        // Preparar la consulta SQL
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
        $stmt->execute($datos);

        $equipo_id = $conexion->lastInsertId();

        // Procesar archivos PDF
        $tiposDocumentos = [
            'manual_operativo' => 'Manual Operativo',
            'manual_servicio' => 'Manual de Servicio',
            'manual_partes' => 'Manual de Partes',
            'manual_instalacion' => 'Manual de Instalación',
            'plano_electrico' => 'Plano Eléctrico',
            'plano_electronico' => 'Plano Electrónico',
            'plano_hidraulico' => 'Plano Hidráulico',
            'plano_neumatico' => 'Plano Neumático'
        ];

        foreach ($tiposDocumentos as $campo => $tipo) {
            if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
                $nombreOriginal = basename($_FILES[$campo]['name']);
                $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
                
                if ($extension === 'pdf') {
                    $nuevoNombre = uniqid($campo . '_', true) . '.pdf';
                    $rutaDestino = $directorioPDFs . $nuevoNombre;
                    
                    if (move_uploaded_file($_FILES[$campo]['tmp_name'], $rutaDestino)) {
                        $sqlDocumento = "INSERT INTO documentos_equipos 
                                        (equipo_id, tipo_documento, nombre_archivo, nombre_original) 
                                        VALUES (:equipo_id, :tipo, :archivo, :original)";
                        
                        $stmtDoc = $conexion->prepare($sqlDocumento);
                        $stmtDoc->execute([
                            ':equipo_id' => $equipo_id,
                            ':tipo' => $tipo,
                            ':archivo' => $nuevoNombre,
                            ':original' => $nombreOriginal
                        ]);
                    }
                }
            }
        }

        $_SESSION['mensaje_exito'] = '¡Equipo registrado correctamente!';
        header("Location: ../equipos/registrar.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['mensaje_exito'] = "Error al guardar el equipo: " . $e->getMessage();
        header("Location: ../equipos/registrar.php");
        exit;
    }

} else {
    header("Location: ../equipos/registrar.php");
    exit;
}
?>