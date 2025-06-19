<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SMAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #007bff 0%, #6c63ff 100%);
            min-height: 100vh;
        }
        .dashboard-card {
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
            transition: transform 0.2s;
            padding: 2rem 1.5rem;
        }
        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        .dashboard-icon {
            font-size: 3rem;
            color: #fff;
            background: linear-gradient(135deg, #6c63ff 0%, #007bff 100%);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
        }
        .btn-dashboard {
            border-radius: 30px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .btn-naranja-pastel {
            background-color: #FFA726 !important;
            color: #fff !important;
            border: none;
        }
        .btn-naranja-pastel:hover, .btn-naranja-pastel:focus {
            background-color: #fb8c00 !important;
            color: #fff !important;
        }
        @media (max-width: 767.98px) {
            .dashboard-card {
                padding: 2rem 1rem;
            }
            .dashboard-icon {
                font-size: 2.5rem;
                width: 60px;
                height: 60px;
            }
            .dashboard-card h3 {
                font-size: 1.3rem;
            }
            .dashboard-card p {
                font-size: 1rem;
            }
        }
    </style>
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="text-white fw-bold mb-3">Bienvenido a SMAQ</h1>
            <p class="text-white-50 fs-5">Seleccione un módulo para comenzar</p>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- Módulo Equipos -->
            <div class="col-12 col-md-5">
                <div class="card dashboard-card text-center p-4">
                    <div class="dashboard-icon mb-3 bg-primary">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Equipos</h3>
                    <p class="text-secondary mb-4">Gestione los equipos y cree la hoja de vida de las máquinas.</p>
                    <div class="d-grid gap-2">
                        <a href="equipos/listado_equipos.php" class="btn btn-primary btn-dashboard">
                            <i class="bi bi-list-ul me-2"></i> Listado de Equipos
                        </a>
                        <a href="equipos/registrar.php" class="btn btn-outline-primary btn-dashboard">
                            <i class="bi bi-file-earmark-text me-2"></i> Hoja de Vida de Máquina
                        </a>
                    </div>
                </div>
            </div>
            <!-- Módulo Mantenimiento -->
            <div class="col-12 col-md-5">
                <div class="card dashboard-card text-center p-4">
                    <div class="dashboard-icon mb-3 bg-success">
                        <i class="bi bi-tools"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Mantenimiento</h3>
                    <p class="text-secondary mb-4">Gestione solicitudes, órdenes y el historial de mantenimientos.</p>
                    <div class="d-grid gap-2">
                        <a href="mantenimientos/solicitud_mantenimiento.php" class="btn btn-success btn-dashboard">
                            <i class="bi bi-plus-circle me-2"></i> Nueva Solicitud
                        </a>
                        <a href="mantenimientos/listado_mantenimiento.php" class="btn btn-outline-success btn-dashboard">
                            <i class="bi bi-list-check me-2"></i> Listado de Mantenimientos
                        </a>
                    </div>
                </div>
            </div>
            <!-- Módulo Cronograma -->
            <div class="col-12 col-md-5">
                <div class="card dashboard-card text-center p-4">
                    <div class="dashboard-icon mb-3" style="background-color: #FFA726;">
                        <i class="bi bi-calendar3 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Cronograma</h3>
                    <p class="text-secondary mb-4">Visualice y gestione el cronograma maestro de mantenimientos.</p>
                    <div class="d-grid gap-2">
                        <a href="cronogramas/visualizar_cronograma.php" class="btn btn-naranja-pastel btn-dashboard">
                            <i class="bi bi-calendar-range me-2"></i> Ver cronograma maestro
                        </a>
                        <a href="cronogramas/listar_cronograma.php" class="btn btn-warning btn-dashboard text-white">
                            <i class="bi bi-calendar-range me-2"></i> Ver Cronograma
                        </a>
                        <a href="cronogramas/crear_cronograma.php" class="btn btn-warning btn-dashboard text-white">
                            <i class="bi bi-calendar-plus me-2"></i> Crear Cronograma
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>