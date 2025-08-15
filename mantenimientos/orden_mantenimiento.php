<?php
include '../includes/proteccion.php';
include '../includes/conexion.php';

$equipo_codigo = '';
$solicitud = null;
$mensaje = '';

if (isset($_GET['id'])) {
    // Traer todos los datos de la solicitud y del equipo
    $stmt = $conexion->prepare("
        SELECT sm.*, e.codigo, e.nombre AS nombre_equipo
        FROM solicitudes_mantenimiento sm
        INNER JOIN equipos e ON sm.equipo_id = e.id
        WHERE sm.id = :id
    ");
    $stmt->execute([':id' => $_GET['id']]);
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($solicitud) {
        $equipo_codigo = $solicitud['codigo'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipo_codigo = $_POST['equipo_codigo'];
    $realizado_por = $_POST['realizado_por'];
    $motivo = $_POST['motivo'];
    $tipo_mantenimiento = $_POST['tipo_mantenimiento'];
    $accion_mantenimiento = $_POST['accion_mantenimiento'];
    $descripcion_equipo = $_POST['descripcion_equipo'];
    $herramientas = $_POST['herramientas'];
    $descripcion_realizado = $_POST['descripcion_realizado'];
    $repuestos = $_POST['repuestos'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $responsable = $_POST['responsable'];
    $fecha_factura = $_POST['fecha_factura'];

    // Manejar la subida de imágenes del equipo
    $ruta_imagen_equipo = null;
    if (isset($_FILES['imagen_equipo']) && $_FILES['imagen_equipo']['error'] == 0) {
        $dir_evidencias = "uploads/evidencias/";
        if (!is_dir($dir_evidencias)) {
            mkdir($dir_evidencias, 0777, true);
        }
        $nombre_archivo = uniqid() . '_' . basename($_FILES['imagen_equipo']['name']);
        $ruta_imagen_equipo = $dir_evidencias . $nombre_archivo;
        move_uploaded_file($_FILES['imagen_equipo']['tmp_name'], $ruta_imagen_equipo);
        // Comprimir y redimensionar
        comprimirImagen($ruta_imagen_equipo, $ruta_imagen_equipo, 70, 1200, 1200);
    }

    // Obtener el ID del equipo a partir del código
    $stmt_equipo = $conexion->prepare("SELECT id FROM equipos WHERE codigo = :codigo");
    $stmt_equipo->execute([':codigo' => $equipo_codigo]);
    $fila_equipo = $stmt_equipo->fetch(PDO::FETCH_ASSOC);
    $equipo_id = $fila_equipo ? $fila_equipo['id'] : null;

    if ($equipo_id) {
        $sql = "INSERT INTO ordenes_mantenimiento (
            solicitud_id,
            fecha,
            equipo_id,
            realizado_por,
            motivo,
            tipo_mantenimiento,
            accion_mantenimiento,
            descripcion_equipo,
            herramientas,
            imagen_equipo,
            descripcion_realizado,
            repuestos,
            hora_inicio,
            hora_fin,
            responsable,
            fecha_factura
        ) VALUES (
            :solicitud_id,
            NOW(), :equipo_id, :realizado_por, :motivo, :tipo_mantenimiento, :accion_mantenimiento, :descripcion_equipo, :herramientas, :imagen_equipo, :descripcion_realizado, :repuestos, :hora_inicio, :hora_fin, :responsable, :fecha_factura
        )";

        $stmt = $conexion->prepare($sql);
        $ok = $stmt->execute([
            ':solicitud_id' => $_POST['solicitud_id'],
            ':equipo_id' => $equipo_id,
            ':realizado_por' => $realizado_por,
            ':motivo' => $motivo,
            ':tipo_mantenimiento' => $tipo_mantenimiento,
            ':accion_mantenimiento' => $accion_mantenimiento,
            ':descripcion_equipo' => $descripcion_equipo,
            ':herramientas' => $herramientas,
            ':imagen_equipo' => $ruta_imagen_equipo,
            ':descripcion_realizado' => $descripcion_realizado,
            ':repuestos' => $repuestos,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':responsable' => $responsable,
            ':fecha_factura' => $fecha_factura ?: null
        ]);

        if ($ok) {
            $orden_id = $conexion->lastInsertId();

            // Manejar la subida de múltiples imágenes de facturas
            if (!empty($_FILES['factura_imagen']['name'][0])) {
                $dir_facturas = "uploads/facturas/";
                if (!is_dir($dir_facturas)) {
                    mkdir($dir_facturas, 0777, true);
                }

                foreach ($_FILES['factura_imagen']['name'] as $key => $name) {
                    if ($_FILES['factura_imagen']['error'][$key] == 0) {
                        $nombre_archivo = uniqid() . '_' . basename($name);
                        $ruta_factura_imagen = $dir_facturas . $nombre_archivo;
                        if (move_uploaded_file($_FILES['factura_imagen']['tmp_name'][$key], $ruta_factura_imagen)) {
                            // Guardar en la tabla orden_facturas
                            $stmt_factura = $conexion->prepare("INSERT INTO orden_facturas (orden_id, ruta_factura_imagen) VALUES (:orden_id, :ruta)");
                            $stmt_factura->execute([
                                ':orden_id' => $orden_id,
                                ':ruta' => $ruta_factura_imagen
                            ]);
                        }
                    }
                }
            }

            // Actualizar el estado de la solicitud a "Realizado"
            if (isset($_POST['solicitud_id'])) {
                $stmt_estado = $conexion->prepare("UPDATE solicitudes_mantenimiento SET estado = 'Realizado' WHERE id = :id");
                $stmt_estado->execute([':id' => $_POST['solicitud_id']]);
            }
            header("Location: listado_mantenimiento.php");
            exit;
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al registrar la orden.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-warning'>Error: Código de equipo no encontrado.</div>";
    }
}
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Orden de Mantenimiento - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --success: #4ade80;
            --danger: #f87171;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f7ff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-card {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            background: white;
            transition: transform 0.3s ease;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
        }
        
        .form-section {
            background: #f8fafc;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        .section-title {
            color: var(--primary);
            font-weight: 600;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-label {
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.2);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }
        
        .btn-secondary {
            background: #64748b;
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-3px);
        }
        
        .file-upload-container {
            background: white;
            border: 2px dashed #cbd5e1;
            border-radius: var(--border-radius);
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .file-upload-container:hover {
            border-color: var(--accent);
            background: rgba(76, 201, 240, 0.05);
        }
        
        .file-upload-container i {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 15px;
        }
        
        .file-upload-container input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        
        .preview-item {
            position: relative;
            width: 140px;
            height: 140px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .preview-item:hover {
            transform: translateY(-5px);
        }
        
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .preview-item .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 30px;
            height: 30px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
        }
        
        .shape-1 {
            width: 180px;
            height: 180px;
            background: var(--primary);
            top: 10%;
            left: 5%;
        }
        
        .shape-2 {
            width: 120px;
            height: 120px;
            background: var(--accent);
            bottom: 15%;
            right: 7%;
        }
        
        .shape-3 {
            width: 90px;
            height: 90px;
            background: var(--secondary);
            top: 40%;
            right: 20%;
        }
        
        @media (max-width: 768px) {
            .card-header {
                padding: 20px 15px;
            }
            
            .form-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="register-card">
                    <div class="card-header">
                        <h2 class="mb-0"><i class="fas fa-tools me-3"></i>Registrar Orden de Mantenimiento</h2>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-danger animate__animated animate__shakeX mb-4">
                                <?= $mensaje ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="orden_mantenimiento.php" method="post" enctype="multipart/form-data" id="mainForm">
                            <div class="form-section animate__animated animate__fadeIn">
                                <h4 class="section-title"><i class="fas fa-info-circle"></i> Información básica</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Código del equipo:</label>
                                        <input type="text" name="equipo_codigo" class="form-control" value="<?= htmlspecialchars($equipo_codigo); ?>" readonly required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Realizado por:</label>
                                        <input type="text" name="realizado_por" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Motivo del mantenimiento:</label>
                                    <input type="text" name="motivo" class="form-control" value="<?= htmlspecialchars($solicitud['descripcion_problema'] ?? '') ?>" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de mantenimiento:</label>
                                        <select name="tipo_mantenimiento" class="form-select" required>
                                            <option value="">Seleccione</option>
                                            <option>Mecánico</option>
                                            <option>Eléctrico</option>
                                            <option>Instrumentación</option>
                                            <option>Limpieza</option>
                                            <option>Lubricación</option>
                                            <option>Construcción</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Acción de mantenimiento:</label>
                                        <select name="accion_mantenimiento" class="form-select" required>
                                            <option value="">Seleccione</option>
                                            <option>Preventivo</option>
                                            <option>Correctivo</option>
                                            <option>Predictivo</option>
                                            <option>Mejoras</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-section animate__animated animate__fadeIn animate__delay-1s">
                                <h4 class="section-title"><i class="fas fa-tasks"></i> Detalles del trabajo</h4>
                                <div class="mb-3">
                                    <label class="form-label">Descripción del trabajo:</label>
                                    <textarea name="descripcion_equipo" class="form-control" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Herramientas a utilizar:</label>
                                    <textarea name="herramientas" class="form-control" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Información del equipo/Dibujo de piezas:</label>
                                    <div class="file-upload-container">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <h5>Haga clic para subir una imagen</h5>
                                        <p class="text-muted">Formatos: JPG, PNG, GIF (Máx. 5MB)</p>
                                        <input type="file" name="imagen_equipo" class="form-control" accept="image/*">
                                    </div>
                                </div>
                                
                                <div class="preview-container" id="imagenEquipoPreview"></div>
                            </div>
                            
                            <div class="form-section animate__animated animate__fadeIn animate__delay-2s">
                                <h4 class="section-title"><i class="fas fa-check-circle"></i> Resultados</h4>
                                <div class="mb-3">
                                    <label class="form-label">Descripción de lo realizado:</label>
                                    <textarea name="descripcion_realizado" class="form-control" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Repuestos reemplazados:</label>
                                    <textarea name="repuestos" class="form-control"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora de inicio:</label>
                                        <input type="time" name="hora_inicio" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora de finalización:</label>
                                        <input type="time" name="hora_fin" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Responsable encargado:</label>
                                    <input type="text" name="responsable" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-section animate__animated animate__fadeIn animate__delay-3s">
                                <h4 class="section-title"><i class="fas fa-receipt"></i> Gastos cargados a la orden</h4>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de la factura:</label>
                                        <input type="date" name="fecha_factura" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Imágenes de facturas:</label>
                                    <div class="file-upload-container">
                                        <i class="fas fa-file-invoice"></i>
                                        <h5>Haga clic para subir imágenes de facturas</h5>
                                        <p class="text-muted">Puede seleccionar múltiples imágenes (Máx. 5)</p>
                                        <input type="file" name="factura_imagen[]" id="facturaInput" multiple accept="image/*">
                                    </div>
                                </div>
                                
                                <div class="preview-container" id="facturaPreview"></div>
                            </div>
                            
                            <?php if (isset($_GET['id'])): ?>
                                <input type="hidden" name="solicitud_id" value="<?= intval($_GET['id']); ?>">
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-end mt-4 gap-3">
                                <a href="listado_mantenimiento.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Volver al listado
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i> Registrar orden
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animaciones con Anime.js
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de formas flotantes
            anime({
                targets: '.shape-1',
                translateY: [0, -40],
                translateX: [0, 30],
                duration: 6000,
                direction: 'alternate',
                loop: true,
                easing: 'easeInOutSine'
            });
            
            anime({
                targets: '.shape-2',
                translateY: [0, 50],
                translateX: [0, -40],
                duration: 7000,
                direction: 'alternate',
                loop: true,
                easing: 'easeInOutSine',
                delay: 1000
            });
            
            anime({
                targets: '.shape-3',
                translateY: [0, -60],
                translateX: [0, 50],
                duration: 5500,
                direction: 'alternate',
                loop: true,
                easing: 'easeInOutSine',
                delay: 1500
            });
            
            // Animación al enviar el formulario
            document.getElementById('mainForm').addEventListener('submit', function(e) {
                anime({
                    targets: this,
                    translateY: [0, -10],
                    duration: 200,
                    direction: 'alternate',
                    easing: 'easeInOutSine',
                    loop: 2
                });
            });
        });

        // Vista previa de las imágenes de facturas
        document.getElementById('facturaInput').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('facturaPreview');
            previewContainer.innerHTML = '';
            
            const files = e.target.files;
            const maxFiles = 5;
            
            if (files.length > maxFiles) {
                alert(`Solo puedes subir un máximo de ${maxFiles} imágenes.`);
                this.value = '';
                return;
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.match('image.*')) continue;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="remove-btn" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);
                    
                    // Animación al agregar
                    anime({
                        targets: previewItem,
                        opacity: [0, 1],
                        scale: [0.8, 1],
                        duration: 500,
                        easing: 'easeOutBack'
                    });
                }
                reader.readAsDataURL(file);
            }
        });

        // Vista previa de la imagen del equipo
        document.getElementById('imagenEquipoInput').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('imagenEquipoPreview');
            previewContainer.innerHTML = '';
            const files = e.target.files;
            const maxFiles = 5;

            if (files.length > maxFiles) {
                alert(`Solo puedes subir un máximo de ${maxFiles} imágenes.`);
                this.value = '';
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.match('image.*')) continue;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="remove-btn" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);

                    anime({
                        targets: previewItem,
                        opacity: [0, 1],
                        scale: [0.8, 1],
                        duration: 500,
                        easing: 'easeOutBack'
                    });
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

<?php include '../includes/footer.php'; ?><?php
function comprimirImagen($rutaOriginal, $rutaDestino, $calidad = 70, $maxWidth = 1200, $maxHeight = 1200) {
    $info = getimagesize($rutaOriginal);
    if (!$info) return false;

    list($width, $height) = $info;
    $mime = $info['mime'];

    // Calcula nuevo tamaño manteniendo proporción
    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    $newWidth = intval($width * $ratio);
    $newHeight = intval($height * $ratio);

    switch ($mime) {
        case 'image/jpeg':
            $srcImg = imagecreatefromjpeg($rutaOriginal);
            break;
        case 'image/png':
            $srcImg = imagecreatefrompng($rutaOriginal);
            break;
        case 'image/gif':
            $srcImg = imagecreatefromgif($rutaOriginal);
            break;
        default:
            return false;
    }

    $dstImg = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Guarda la imagen comprimida
    if ($mime === 'image/jpeg') {
        imagejpeg($dstImg, $rutaDestino, $calidad);
    } elseif ($mime === 'image/png') {
        imagepng($dstImg, $rutaDestino, 8); // 0-9, menor es mejor calidad
    } elseif ($mime === 'image/gif') {
        imagegif($dstImg, $rutaDestino);
    }

    imagedestroy($srcImg);
    imagedestroy($dstImg);
    return true;
}