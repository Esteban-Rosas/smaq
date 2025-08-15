<?php
include '../includes/proteccion.php';
include '../includes/conexion.php';
include '../includes/header.php';

// Inicializar $equipos como array vacío por defecto
$equipos = [];

// Manejar eliminación de equipos
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    
    try {
        // Eliminar el equipo
        $sql = "DELETE FROM equipos WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Mensaje de éxito
        $mensajeExito = "Equipo eliminado correctamente.";
    } catch (PDOException $e) {
        $mensajeError = "Error al eliminar el equipo: " . $e->getMessage();
    }
}

try {
    // Consulta equipos con JOIN para mostrar el nombre de la ubicación
    $sql = "SELECT e.id, e.nombre, u.nombre AS ubicacion, e.codigo
            FROM equipos e
            LEFT JOIN ubicaciones u ON e.ubicacion_id = u.id
            ORDER BY e.nombre ASC";
    $stmt = $conexion->query($sql);
    
    if ($stmt) {
        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        throw new Exception("Error en la consulta SQL");
    }
} catch (Exception $e) {
    // Registrar el error para depuración
    error_log("Error en listado_equipos.php: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Equipos - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Anime.js -->
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --success: #4ade80;
            --danger: #f87171;
            --warning: #fbbf24;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }
        
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f7ff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-equipo {
            border-radius: var(--border-radius);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            margin-bottom: 25px;
            overflow: hidden;
            position: relative;
            background: white;
        }
        
        .card-equipo:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(67, 97, 238, 0.2);
        }
        
        .card-header-equipo {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .card-header-equipo::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
        }
        
        .badge-custom {
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5em 0.9em;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
        }
        
        .action-btn {
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 4px;
            transition: var(--transition);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .action-btn:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .action-view {
            background-color: var(--accent);
            color: white;
        }
        
        .action-edit {
            background-color: var(--warning);
            color: white;
        }
        
        .action-components {
            background-color: #74b9ff;
            color: white;
        }
        
        .action-delete {
            background-color: var(--danger);
            color: white;
        }
        
        .search-container {
            background-color: white;
            border-radius: 30px;
            padding: 10px 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        .search-btn {
            border-radius: 30px;
            padding: 10px 25px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            font-weight: 500;
        }
        
        .stat-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: var(--transition);
            position: relative;
            z-index: 1;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            z-index: -1;
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
            opacity: 0.08;
        }
        
        .shape-1 {
            width: 150px;
            height: 150px;
            background: var(--primary);
            top: 10%;
            left: 5%;
        }
        
        .shape-2 {
            width: 100px;
            height: 100px;
            background: var(--accent);
            bottom: 15%;
            right: 7%;
        }
        
        .shape-3 {
            width: 80px;
            height: 80px;
            background: var(--secondary);
            top: 40%;
            right: 20%;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 30px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }
        
        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            transition: var(--transition);
            z-index: -1;
        }
        
        .btn-primary-custom:hover::before {
            width: 100%;
        }
        
        .empty-state {
            border-radius: var(--border-radius);
            overflow: hidden;
            background: white;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            transition: var(--transition);
        }
        
        .empty-state:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .delete-modal-content {
            border-radius: var(--border-radius);
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .delete-modal-header {
            background: linear-gradient(135deg, var(--danger), #e53e3e);
            color: white;
            border: none;
        }
        
        .delete-icon {
            font-size: 4rem;
            color: var(--danger);
            opacity: 0.8;
            margin-bottom: 20px;
        }
        
        .alert-fixed {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            min-width: 400px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .card-header-equipo {
                padding: 15px;
            }
            
            .action-btn {
                width: 38px;
                height: 38px;
                font-size: 0.9rem;
            }
            
            .alert-fixed {
                min-width: 90%;
                left: 5%;
                transform: none;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="container py-5">
        <!-- Mostrar mensajes de éxito/error -->
        <?php if (isset($mensajeExito)): ?>
            <div class="alert alert-success alert-dismissible fade show alert-fixed">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $mensajeExito ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($mensajeError)): ?>
            <div class="alert alert-danger alert-dismissible fade show alert-fixed">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $mensajeError ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1 fw-bold text-primary"><i class="bi bi-gear-wide-connected me-2"></i>Listado de Equipos</h1>
                <p class="text-muted">Administra y mantén un control detallado de todos tus equipos</p>
            </div>
            <a href="registrar.php" class="btn btn-primary-custom btn-lg">
                <i class="bi bi-plus-circle me-2"></i> Nuevo Equipo
            </a>
        </div>

        <!-- Panel de búsqueda y filtros -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <div class="input-group search-container">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0" placeholder="Buscar equipos por nombre, código o ubicación...">
                            <button class="btn btn-primary search-btn">
                                Buscar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>
                            <button class="btn btn-outline-primary">
                                <i class="bi bi-sort-down me-1"></i> Ordenar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="card-title mb-1">Total Equipos</p>
                                <h2 class="mb-0"><?php echo count($equipos); ?></h2>
                            </div>
                            <div class="display-4 opacity-25">
                                <i class="bi bi-pc-display-horizontal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="card-title mb-1">Operativos</p>
                                <h2 class="mb-0"><?php echo count($equipos); ?></h2>
                            </div>
                            <div class="display-4 opacity-25">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="card-title mb-1">En Mantenimiento</p>
                                <h2 class="mb-0">0</h2>
                            </div>
                            <div class="display-4 opacity-25">
                                <i class="bi bi-tools"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="card-title mb-1">Inoperativos</p>
                                <h2 class="mb-0">0</h2>
                            </div>
                            <div class="display-4 opacity-25">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de equipos en formato de tarjetas -->
        <div class="row" id="equiposContainer">
            <?php if (count($equipos) > 0): ?>
                <?php foreach ($equipos as $index => $equipo): ?>
                    <div class="col-md-6 col-lg-4 equipos-card">
                        <div class="card card-equipo">
                            <div class="card-header card-header-equipo py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 fw-bold">
                                        <i class="bi bi-gear-wide-connected me-2"></i>
                                        <?php echo htmlspecialchars($equipo['nombre']); ?>
                                    </h5>
                                    <span class="badge badge-custom">
                                        #<?php echo htmlspecialchars($equipo['id']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <p class="mb-1 text-muted"><i class="bi bi-geo-alt me-2"></i> Ubicación</p>
                                    <p class="fw-bold"><?php echo htmlspecialchars($equipo['ubicacion']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted"><i class="bi bi-upc-scan me-2"></i> Código</p>
                                    <p class="fw-bold"><?php echo htmlspecialchars($equipo['codigo']); ?></p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                                    <div class="d-flex">
                                        <a href="ver_equipo.php?id=<?= $equipo['id'] ?>" 
                                           class="action-btn action-view" 
                                           title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="editar_equipo.php?id=<?= $equipo['id'] ?>" 
                                           class="action-btn action-edit" 
                                           title="Editar equipo">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="componentes_equipo.php?equipo_id=<?= $equipo['id'] ?>" 
                                           class="action-btn action-components" 
                                           title="Componentes">
                                            <i class="bi bi-motherboard"></i>
                                        </a>
                                        <a href="#" 
                                           class="action-btn action-delete" 
                                           title="Eliminar equipo"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#deleteModal"
                                           data-id="<?= $equipo['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                    <span class="badge bg-success py-2 px-3 rounded-pill">Operativo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card empty-state text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-pc-display-horizontal display-4 text-muted mb-4"></i>
                            <h3 class="text-muted mb-3">
                                <?= (isset($e) ? 'Error al cargar equipos' : 'No hay equipos registrados') ?>
                            </h3>
                            <p class="text-muted mb-4">
                                Parece que aún no has agregado ningún equipo al sistema
                            </p>
                            <a href="registrar.php" class="btn btn-primary-custom">
                                <i class="bi bi-plus-circle me-2"></i> Registrar Primer Equipo
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between mt-5 pt-3">
            <a href="../dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Volver al Dashboard
            </a>
            <nav>
                <ul class="pagination" id="paginationContainer">
                    <!-- La paginación se generará dinámicamente con JavaScript -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content delete-modal-content">
                <div class="modal-header delete-modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body text-center p-4">
                        <div class="delete-icon">
                            <i class="bi bi-trash-fill"></i>
                        </div>
                        <h4 class="mb-3">¿Eliminar este equipo?</h4>
                        <p>Esta acción eliminará permanentemente el equipo y todos sus componentes asociados. ¿Estás seguro de que deseas continuar?</p>
                        <input type="hidden" name="delete_id" id="deleteIdInput">
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animaciones con Anime.js
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada de las tarjetas
            anime({
                targets: '.equipos-card',
                translateY: [30, 0],
                opacity: [0, 1],
                scale: [0.95, 1],
                duration: 800,
                delay: anime.stagger(100, {start: 300}),
                easing: 'easeOutElastic(1, .8)'
            });
            
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
            
            // Animación al pasar el mouse sobre botones de acción
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    anime({
                        targets: this,
                        scale: 1.15,
                        duration: 300,
                        easing: 'easeOutBack'
                    });
                });
                
                btn.addEventListener('mouseleave', function() {
                    anime({
                        targets: this,
                        scale: 1,
                        duration: 300,
                        easing: 'easeOutBack'
                    });
                });
            });
        });

        // Configurar el ID a eliminar en el modal
        const deleteModal = document.getElementById('deleteModal');
        const deleteIdInput = document.getElementById('deleteIdInput');
        
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            deleteIdInput.value = id;
        });

        // Paginación dinámica
        <?php if (count($equipos) > 0): ?>
            const equiposPorPagina = 6;
            const tarjetas = Array.from(document.querySelectorAll('.equipos-card'));
            const paginacion = document.getElementById('paginationContainer');
            let paginaActual = 1;
            const totalPaginas = Math.ceil(tarjetas.length / equiposPorPagina);

            function mostrarPagina(pagina) {
                // Ocultar todas las tarjetas
                tarjetas.forEach(card => card.style.display = 'none');
                
                // Mostrar solo las tarjetas de la página actual
                const inicio = (pagina - 1) * equiposPorPagina;
                const fin = inicio + equiposPorPagina;
                
                for (let i = inicio; i < fin && i < tarjetas.length; i++) {
                    tarjetas[i].style.display = 'block';
                    
                    // Animación al mostrar
                    anime({
                        targets: tarjetas[i],
                        opacity: [0, 1],
                        scale: [0.95, 1],
                        duration: 500,
                        delay: (i - inicio) * 50,
                        easing: 'easeOutQuad'
                    });
                }
                
                // Actualizar la página actual
                paginaActual = pagina;
                crearPaginacion(paginaActual);
            }

            // Genera paginación dinámica
            function crearPaginacion(paginaActual) {
                paginacion.innerHTML = '';

                // Botón anterior
                const prev = document.createElement('li');
                prev.className = 'page-item' + (paginaActual === 1 ? ' disabled' : '');
                prev.innerHTML = `<a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>`;
                prev.onclick = () => { if (paginaActual > 1) mostrarPagina(paginaActual - 1); };
                paginacion.appendChild(prev);

                // Números de página
                const paginasAMostrar = 3;
                let inicioPaginas = Math.max(1, paginaActual - Math.floor(paginasAMostrar / 2));
                let finPaginas = Math.min(totalPaginas, inicioPaginas + paginasAMostrar - 1);

                if (finPaginas - inicioPaginas < paginasAMostrar - 1) {
                    inicioPaginas = Math.max(1, finPaginas - paginasAMostrar + 1);
                }

                for (let i = inicioPaginas; i <= finPaginas; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (i === paginaActual ? ' active' : '');
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.onclick = () => mostrarPagina(i);
                    paginacion.appendChild(li);
                }

                // Botón siguiente
                const next = document.createElement('li');
                next.className = 'page-item' + (paginaActual === totalPaginas ? ' disabled' : '');
                next.innerHTML = `<a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>`;
                next.onclick = () => { if (paginaActual < totalPaginas) mostrarPagina(paginaActual + 1); };
                paginacion.appendChild(next);
            }

            // Mostrar la primera página al cargar
            mostrarPagina(1);
        <?php endif; ?>
    </script>
</body>
</html>