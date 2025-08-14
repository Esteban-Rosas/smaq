<?php
include '../includes/proteccion.php';
include '../includes/header.php';
include_once('../includes/conexion.php');

// Obtener ubicaciones
$sql_ubicaciones = "SELECT id, nombre FROM ubicaciones ORDER BY nombre ASC";
$stmt = $conexion->query($sql_ubicaciones);
$ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Equipo - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a73e8;
            --secondary: #4285f4;
            --accent: #34a853;
            --light-bg: #f8f9fa;
            --dark-bg: #202124;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f1 100%);
            min-height: 100vh;
            padding-bottom: 40px;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: none;
            margin-top: 20px;
        }
        
        .card-header {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            border-radius: 12px 12px 0 0 !important;
            padding: 20px 25px;
        }
        
        .nav-tabs .nav-link {
            color: #5f6368;
            font-weight: 500;
            padding: 12px 20px;
            border: none;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary);
            background: transparent;
            border-bottom: 3px solid var(--primary);
        }
        
        .section-title {
            color: var(--primary);
            border-left: 4px solid var(--accent);
            padding-left: 12px;
            margin: 25px 0 15px;
            font-weight: 600;
        }
        
        .file-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #eaeaea;
        }
        
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .file-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .file-card i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .file-card .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .btn-primary {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            border: none;
            padding: 10px 25px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(120deg, #0d5bbd, #2b6ad9);
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            padding: 10px 25px;
        }
        
        .preview-container {
            margin-top: 15px;
            text-align: center;
            display: none;
        }
        
        .preview-container img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .required:after {
            content: " *";
            color: #dc3545;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 text-white"><i class="fas fa-server me-2"></i>Registrar Nuevo Equipo</h3>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($mensaje_exito): ?>
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo $mensaje_exito; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="../includes/guardar_equipo.php" method="POST" enctype="multipart/form-data">
                            <!-- Pestañas de navegación -->
                            <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="basico-tab" data-bs-toggle="tab" data-bs-target="#basico" type="button" role="tab">
                                        <i class="fas fa-info-circle me-1"></i> Datos Básicos
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historico" type="button" role="tab">
                                        <i class="fas fa-history me-1"></i> Histórico
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tecnico-tab" data-bs-toggle="tab" data-bs-target="#tecnico" type="button" role="tab">
                                        <i class="fas fa-microchip me-1"></i> Técnico
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="archivos-tab" data-bs-toggle="tab" data-bs-target="#archivos" type="button" role="tab">
                                        <i class="fas fa-file-alt me-1"></i> Archivos
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Contenido de las pestañas -->
                            <div class="tab-content" id="formTabsContent">
                                <!-- Pestaña 1: Datos Básicos -->
                                <div class="tab-pane fade show active" id="basico" role="tabpanel">
                                    <h5 class="section-title">Información General</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="codigo" class="form-label required">Código del Equipo</label>
                                            <input type="text" name="codigo" id="codigo" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="nombre" class="form-label required">Nombre del Equipo</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="ubicacion_id" class="form-label required">Ubicación</label>
                                            <select class="form-select" name="ubicacion_id" id="ubicacion_id" required>
                                                <option value="">Seleccione una ubicación</option>
                                                <?php foreach ($ubicaciones as $row): ?>
                                                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="destinado" class="form-label required">Función Principal</label>
                                            <textarea name="destinado" id="destinado" class="form-control" rows="2" required placeholder="¿Para qué está destinado este equipo?"></textarea>
                                        </div>
                                    </div>
                                    
                                    <h5 class="section-title">Especificaciones del Fabricante</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label for="marca" class="form-label required">Marca</label>
                                            <input type="text" name="marca" id="marca" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="modelo" class="form-label required">Modelo</label>
                                            <input type="text" name="modelo" id="modelo" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="serie" class="form-label required">Número de Serie</label>
                                            <input type="text" name="serie" id="serie" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pestaña 2: Histórico -->
                                <div class="tab-pane fade" id="historico" role="tabpanel">
                                    <h5 class="section-title">Adquisición</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label for="forma_adquisicion" class="form-label required">Forma de adquisición</label>
                                            <select name="forma_adquisicion" id="forma_adquisicion" class="form-select" required>
                                                <option value="">Seleccione</option>
                                                <option>Compra Directa</option>
                                                <option>Arrendamiento</option>
                                                <option>Donación</option>
                                                <option>Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="vida_util" class="form-label required">Vida útil (años)</label>
                                            <input type="number" name="vida_util" id="vida_util" class="form-control" min="0" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="fecha_adquisicion" class="form-label required">Fecha de adquisición</label>
                                            <input type="date" name="fecha_adquisicion" id="fecha_adquisicion" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label for="fecha_garantia" class="form-label">Fecha fin de garantía</label>
                                            <input type="date" name="fecha_garantia" id="fecha_garantia" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="fecha_instalacion" class="form-label">Fecha de instalación</label>
                                            <input type="date" name="fecha_instalacion" id="fecha_instalacion" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <h5 class="section-title">Información del Fabricante</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="fabricante" class="form-label">Fabricante</label>
                                            <input type="text" name="fabricante" id="fabricante" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="contacto_fabricante" class="form-label">Contacto</label>
                                            <input type="text" name="contacto_fabricante" id="contacto_fabricante" class="form-control" placeholder="Teléfono o email">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="pais" class="form-label">País de origen</label>
                                            <input type="text" name="pais" id="pais" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pestaña 3: Técnico -->
                                <div class="tab-pane fade" id="tecnico" role="tabpanel">
                                    <h5 class="section-title">Características Técnicas</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label for="tecnologia" class="form-label">Tecnología</label>
                                            <input type="text" name="tecnologia" id="tecnologia" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="frecuencia" class="form-label">Frecuencia (Hz)</label>
                                            <input type="text" name="frecuencia" id="frecuencia" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="voltaje" class="form-label">Voltaje (V)</label>
                                            <input type="text" name="voltaje" id="voltaje" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="corriente" class="form-label">Corriente (A)</label>
                                            <input type="text" name="corriente" id="corriente" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="peso" class="form-label">Peso (kg)</label>
                                            <input type="text" name="peso" id="peso" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="capacidad" class="form-label">Capacidad</label>
                                            <input type="text" name="capacidad" id="capacidad" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <h5 class="section-title">Alimentación</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="fuente_alimentacion" class="form-label required">Fuente de Alimentación</label>
                                            <select name="fuente_alimentacion" id="fuente_alimentacion" class="form-select" required>
                                                <option value="">Seleccione</option>
                                                <option>ACPM</option>
                                                <option>Electricidad</option>
                                                <option>Energía Solar</option>
                                                <option>Aire</option>
                                                <option>Agua</option>
                                                <option>Vapor</option>
                                                <option>Gas</option>
                                                <option>Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pestaña 4: Archivos -->
                                <div class="tab-pane fade" id="archivos" role="tabpanel">
                                    <div class="file-section">
                                        <h5 class="section-title"><i class="fas fa-camera me-2"></i>Imagen del Equipo</h5>
                                        <div class="mb-4">
                                            <input type="file" name="foto" id="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
                                        </div>
                                        <div class="preview-container" id="imagePreview">
                                            <img id="preview" src="#" alt="Vista previa de la imagen">
                                        </div>
                                    </div>
                                    
                                    <div class="file-section">
                                        <h5 class="section-title"><i class="fas fa-book me-2"></i>Documentación Técnica</h5>
                                        <div class="file-grid">
                                            <div class="file-card">
                                                <i class="fas fa-book-open"></i>
                                                <div class="form-label">Manual Operativo</div>
                                                <input type="file" name="manual_operativo" class="form-control" accept="application/pdf">
                                            </div>
                                            
                                            <div class="file-card">
                                                <i class="fas fa-tools"></i>
                                                <div class="form-label">Manual de Servicio</div>
                                                <input type="file" name="manual_servicio" class="form-control" accept="application/pdf">
                                            </div>
                                            
                                            <div class="file-card">
                                                <i class="fas fa-puzzle-piece"></i>
                                                <div class="form-label">Manual de Partes</div>
                                                <input type="file" name="manual_partes" class="form-control" accept="application/pdf">
                                            </div>
                                            
                                            <div class="file-card">
                                                <i class="fas fa-wrench"></i>
                                                <div class="form-label">Manual de Instalación</div>
                                                <input type="file" name="manual_instalacion" class="form-control" accept="application/pdf">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="file-section">
                                        <h5 class="section-title"><i class="fas fa-drafting-compass me-2"></i>Planos</h5>
                                        <div class="file-grid">
                                            <div class="file-card">
                                                <i class="fas fa-bolt"></i>
                                                <div class="form-label">Plano Eléctrico</div>
                                                <input type="file" name="plano_electrico" class="form-control" accept="application/pdf">
                                            </div>
                                            
                                            <div class="file-card">
                                                <i class="fas fa-microchip"></i>
                                                <div class="form-label">Plano Electrónico</div>
                                                <input type="file" name="plano_electronico" class="form-control" accept="application/pdf">
                                            </div>
                                            
                                            <div class="file-card">
                                                <i class="fas fa-tint"></i>
                                                <div class="form-label">Plano Hidráulico</div>
                                                <input type="file" name="plano_hidraulico" class="form-control" accept="application/pdf">
                                            </div>
                                            
                                            <div class="file-card">
                                                <i class="fas fa-wind"></i>
                                                <div class="form-label">Plano Neumático</div>
                                                <input type="file" name="plano_neumatico" class="form-control" accept="application/pdf">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="../dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Equipo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Vista previa de imagen
        function previewImage(event) {
            const previewContainer = document.getElementById('imagePreview');
            const preview = document.getElementById('preview');
            
            if (event.target.files.length > 0) {
                const file = event.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        }
        
        // Validación básica de campos requeridos
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            let mensajes = [];
            const requiredFields = document.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                    // Mensaje personalizado por campo
                    switch(field.name) {
                        case 'codigo': mensajes.push('Código del Equipo es obligatorio.'); break;
                        case 'nombre': mensajes.push('Nombre del Equipo es obligatorio.'); break;
                        case 'ubicacion_id': mensajes.push('Ubicación es obligatoria.'); break;
                        case 'destinado': mensajes.push('Función Principal es obligatoria.'); break;
                        case 'marca': mensajes.push('Marca es obligatoria.'); break;
                        case 'modelo': mensajes.push('Modelo es obligatorio.'); break;
                        case 'serie': mensajes.push('Número de Serie es obligatorio.'); break;
                        case 'forma_adquisicion': mensajes.push('Forma de adquisición es obligatoria.'); break;
                        case 'vida_util': mensajes.push('Vida útil es obligatoria.'); break;
                        case 'fecha_adquisicion': mensajes.push('Fecha de adquisición es obligatoria.'); break;
                        case 'fuente_alimentacion': mensajes.push('Fuente de Alimentación es obligatoria.'); break;
                    }
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos:\n\n' + mensajes.join('\n'));
                // Cambia de pestaña al primer campo inválido
                const firstInvalid = document.querySelector('.is-invalid');
                if (firstInvalid) {
                    const tabId = firstInvalid.closest('.tab-pane').id;
                    const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                    if (tabButton) {
                        new bootstrap.Tab(tabButton).show();
                        firstInvalid.focus();
                    }
                }
            }
        });
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>