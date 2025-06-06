<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SMAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Importante para responsividad -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <style>
    .navbar-carnicos {
        background-color: #3E2C23; /* Marrón oscuro tipo madera */
    }
    .navbar-carnicos .navbar-brand {
        color: #F5F3E7 !important; /* Crema claro para contraste */
        font-weight: bold;
        letter-spacing: 2px;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .navbar-carnicos .nav-link {
        color: #F5F3E7 !important; /* Texto claro */
        font-weight: 500;
        border-radius: 20px;
        padding: 6px 18px;
        margin: 0 2px;
        transition: 
            color 0.2s,
            background 0.3s,
            box-shadow 0.3s,
            border-bottom 0.2s;
    }
    .navbar-carnicos .nav-link.active,
    .navbar-carnicos .nav-link:hover {
        color: #BF6C2C !important; /* Bronce ahumado */
        background: #f5f3e7;
        box-shadow: 0 2px 12px 0 rgba(191,108,44,0.10);
        border-bottom: 2px solid #C97A44;
        text-shadow: 0 1px 6px #fff2e0;
    }
    /* Mejoras para móviles */
    @media (max-width: 991.98px) {
        .navbar-carnicos .navbar-nav .nav-link {
            margin: 6px 0;
            padding: 10px 18px;
            border-radius: 12px;
            text-align: left;
        }
        .navbar-carnicos .navbar-collapse {
            background: #3E2C23;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
    }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-carnicos shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="bi bi-gear-fill"></i> SMAQ
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../equipos/listado_equipos.php"><i class="bi bi-hdd-stack"></i> Equipos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../mantenimientos/listado_mantenimiento.php"><i class="bi bi-tools"></i> Mantenimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../mantenimientos/solicitud_mantenimiento.php"><i class="bi bi-plus-circle"></i> Nueva Solicitud</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
    </div> <!-- container mt-4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
