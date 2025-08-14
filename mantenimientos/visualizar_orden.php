<?php
include '../includes/proteccion.php';
include '../includes/conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de orden no válido.");
}

$id = (int)$_GET['id'];

// Consulta con JOIN a la tabla equipos para traer nombre y código
$sql = "
    SELECT om.*, e.nombre AS nombre_equipo, e.codigo AS codigo_equipo
    FROM ordenes_mantenimiento om
    JOIN equipos e ON om.equipo_id = e.id
    WHERE om.id = :id
";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
    die("Orden de mantenimiento no encontrada.");
}

// Obtener las facturas asociadas a esta orden
$sql_facturas = "SELECT ruta_factura_imagen FROM orden_facturas WHERE orden_id = :orden_id";
$stmt_facturas = $conexion->prepare($sql_facturas);
$stmt_facturas->execute([':orden_id' => $id]);
$facturas = $stmt_facturas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Orden de Mantenimiento - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox-plus-jquery.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 10px;
            --box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f7ff 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .order-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .order-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .order-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
        }
        
        .section-title {
            background: #e9ecef;
            color: var(--primary);
            font-weight: 600;
            padding: 10px 15px;
            margin-top: 25px;
            border-left: 5px solid var(--primary);
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table th {
            background-color: #f1f5f9;
            text-align: left;
            padding: 12px 15px;
            width: 25%;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .content-box {
            background: #f8fafc;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .img-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .img-item {
            position: relative;
            width: 180px;
            height: 180px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .img-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }
        
        .img-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .img-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px;
            font-size: 0.85rem;
            text-align: center;
        }
        
        .facturas-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .factura-card {
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(0,0,0,0.05);
        }
        
        .factura-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .factura-img {
            height: 180px;
            overflow: hidden;
        }
        
        .factura-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .factura-card:hover .factura-img img {
            transform: scale(1.05);
        }
        
        .factura-info {
            padding: 10px;
            background: white;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .status-completed {
            background: rgba(76, 222, 128, 0.2);
            color: #0f5132;
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
            .order-header {
                padding: 20px 15px;
            }
            
            .info-table th, 
            .info-table td {
                display: block;
                width: 100%;
                text-align: left;
            }
            
            .info-table tr {
                border-bottom: 1px solid #e2e8f0;
                display: block;
                margin-bottom: 15px;
            }
            
            .img-item {
                width: 140px;
                height: 140px;
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
    
    <div class="order-container">
        <div class="order-header">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <i class="fas fa-tools fa-3x me-3"></i>
                <div>
                    <h1 class="mb-1">Orden de Mantenimiento</h1>
                    <p class="mb-0">Sistema de Mantenimiento y Administración de Equipos</p>
                </div>
            </div>
            <div class="d-flex justify-content-center gap-3">
                <span class="badge bg-light text-dark">Orden #<?= $id ?></span>
                <span class="badge bg-success">Completada</span>
                <span class="badge bg-light text-dark">Fecha: <?= date('d/m/Y H:i', strtotime($orden['fecha'])) ?></span>
            </div>
        </div>
        
        <div class="p-4">
            <!-- Información General -->
            <h4 class="section-title"><i class="fas fa-info-circle me-2"></i> Información General</h4>
            <table class="info-table">
                <tr>
                    <th>Equipo</th>
                    <td><?= htmlspecialchars($orden['nombre_equipo']) ?></td>
                    <th>Código</th>
                    <td><?= htmlspecialchars($orden['codigo_equipo']) ?></td>
                </tr>
                <tr>
                    <th>Tipo de Mantenimiento</th>
                    <td><?= htmlspecialchars($orden['tipo_mantenimiento']) ?></td>
                    <th>Acción de Mantenimiento</th>
                    <td><?= htmlspecialchars($orden['accion_mantenimiento']) ?></td>
                </tr>
                <tr>
                    <th>Realizado por</th>
                    <td><?= htmlspecialchars($orden['realizado_por']) ?></td>
                    <th>Responsable</th>
                    <td><?= htmlspecialchars($orden['responsable']) ?></td>
                </tr>
                <tr>
                    <th>Motivo</th>
                    <td colspan="3"><?= htmlspecialchars($orden['motivo']) ?></td>
                </tr>
            </table>
            
            <!-- Descripción del Equipo -->
            <h4 class="section-title"><i class="fas fa-tasks me-2"></i> Descripción del Trabajo</h4>
            <div class="content-box">
                <h5>Descripción del equipo:</h5>
                <p><?= nl2br(htmlspecialchars($orden['descripcion_equipo'])) ?></p>
                
                <h5 class="mt-3">Herramientas utilizadas:</h5>
                <p><?= nl2br(htmlspecialchars($orden['herramientas'])) ?></p>
            </div>
            
            <!-- Imagen del Equipo -->
            <?php if (!empty($orden['imagen_equipo'])): ?>
                <h4 class="section-title"><i class="fas fa-camera me-2"></i> Evidencia Fotográfica</h4>
                <div class="img-container">
                    <div class="img-item">
                        <a href="<?= htmlspecialchars($orden['imagen_equipo']) ?>" data-lightbox="equipo" data-title="Imagen del equipo">
                            <img src="<?= htmlspecialchars($orden['imagen_equipo']) ?>" alt="Imagen del equipo">
                        </a>
                        <div class="img-caption">Imagen del equipo</div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Resultados -->
            <h4 class="section-title"><i class="fas fa-check-circle me-2"></i> Resultados</h4>
            <div class="content-box">
                <h5>Descripción de lo realizado:</h5>
                <p><?= nl2br(htmlspecialchars($orden['descripcion_realizado'])) ?></p>
                
                <h5 class="mt-3">Repuestos utilizados:</h5>
                <p><?= nl2br(htmlspecialchars($orden['repuestos'])) ?></p>
            </div>
            
            <!-- Horarios -->
            <h4 class="section-title"><i class="fas fa-clock me-2"></i> Horarios</h4>
            <table class="info-table">
                <tr>
                    <th>Hora de inicio</th>
                    <td><?= htmlspecialchars($orden['hora_inicio']) ?></td>
                    <th>Hora de finalización</th>
                    <td><?= htmlspecialchars($orden['hora_fin']) ?></td>
                </tr>
                <tr>
                    <th>Duración</th>
                    <td colspan="3">
                        <?php
                        if ($orden['hora_inicio'] && $orden['hora_fin']) {
                            $inicio = new DateTime($orden['hora_inicio']);
                            $fin = new DateTime($orden['hora_fin']);
                            $diferencia = $inicio->diff($fin);
                            echo $diferencia->format('%H horas %I minutos');
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
            </table>
            
            <!-- Gastos cargados a la orden -->
            <h4 class="section-title"><i class="fas fa-receipt me-2"></i> Gastos Cargados a la Orden</h4>
            <div class="content-box">
                <table class="info-table">
                    <tr>
                        <th>Fecha de la factura</th>
                        <td><?= $orden['fecha_factura'] ? htmlspecialchars($orden['fecha_factura']) : 'N/A' ?></td>
                    </tr>
                </table>
                
                <?php if (!empty($facturas)): ?>
                    <h5 class="mt-4">Facturas asociadas:</h5>
                    <div class="facturas-container">
                        <?php foreach ($facturas as $factura): ?>
                            <div class="factura-card">
                                <div class="factura-img">
                                    <a href="<?= htmlspecialchars($factura['ruta_factura_imagen']) ?>" data-lightbox="facturas" data-title="Factura de la orden">
                                        <img src="<?= htmlspecialchars($factura['ruta_factura_imagen']) ?>" alt="Factura">
                                    </a>
                                </div>
                                <div class="factura-info">
                                    <small>Factura asociada</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-3">
                        No hay facturas asociadas a esta orden.
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Botones de acción -->
            <div class="d-flex justify-content-end mt-4 gap-3">
                <a href="listado_mantenimiento.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Volver al listado
                </a>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Imprimir orden
                </button>
            </div>
        </div>
    </div>

    <script>
        // Configuración de Lightbox
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'showImageNumberLabel': true,
            'alwaysShowNavOnTouchDevices': true
        });
    </script>
</body>
</html>