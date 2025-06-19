<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SMAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <style>
    .navbar-carnicos {
        background-color: #3E2C23;
    }
    .navbar-carnicos .navbar-brand {
        color: #F5F3E7 !important;
        font-weight: bold;
        letter-spacing: 2px;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: smaq-entrada 0.8s cubic-bezier(.68,-0.55,.27,1.55);
        transition: color 0.3s, text-shadow 0.3s, transform 0.3s;
    }
    .navbar-carnicos .nav-link {
        color: #F5F3E7 !important;
        font-weight: 500;
        border-radius: 20px;
        padding: 6px 18px;
        margin: 0 2px;
        position: relative;
        overflow: hidden;
        transition:
            color 0.2s,
            background 0.3s,
            box-shadow 0.3s,
            border-bottom 0.2s;
    }
    .navbar-carnicos .nav-link::after {
        content: "";
        position: absolute;
        left: 18px;
        right: 18px;
        bottom: 4px;
        height: 2px;
        background: #BF6C2C;
        transform: scaleX(0);
        transition: transform 0.3s;
    }
    .navbar-carnicos .nav-link:hover::after,
    .navbar-carnicos .nav-link.active::after {
        transform: scaleX(1);
    }
    .navbar-carnicos .nav-link.active,
    .navbar-carnicos .nav-link:hover {
        color: #BF6C2C !important;
        background: #f5f3e7;
        box-shadow: 0 2px 12px 0 rgba(191,108,44,0.10);
        border-bottom: 2px solid #C97A44;
        text-shadow: 0 1px 6px #fff2e0;
    }
    .navbar-carnicos .nav-link i {
        transition: transform 0.3s;
        display: inline-block;
    }
    .navbar-carnicos .nav-link:hover i,
    .navbar-carnicos .nav-link.active i {
        transform: rotate(-15deg) scale(1.15);
    }
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

    /* Animación al cargar */
    @keyframes smaq-entrada {
        0% { opacity: 0; transform: translateY(-20px) scale(0.9);}
        100% { opacity: 1; transform: translateY(0) scale(1);}
    }
    .navbar-carnicos .navbar-brand {
        animation: smaq-entrada 0.8s cubic-bezier(.68,-0.55,.27,1.55);
        transition: color 0.3s, text-shadow 0.3s, transform 0.3s;
    }

    /* Animación al pasar el mouse */
    .navbar-carnicos .navbar-brand:hover {
        color: #FFA726 !important;
        text-shadow: 0 2px 12px #FFA72644;
        transform: scale(1.07) rotate(-2deg);
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
                        <a class="nav-link" href="../cronogramas/listar_cronograma.php"><i class="bi bi-calendar3"></i> Cronograma</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php"><i class="bi bi-house"></i> Inicio</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    // Resalta el enlace activo según la URL
    const links = document.querySelectorAll('.navbar-carnicos .nav-link');
    const path = window.location.pathname.split('/').pop();

    links.forEach(link => {
        if (link.getAttribute('href').includes(path)) {
            link.classList.add('active');
        }
        // Cierra el menú en móvil al hacer clic
        link.addEventListener('click', function() {
            const navbar = document.querySelector('.navbar-collapse');
            if (navbar.classList.contains('show')) {
                new bootstrap.Collapse(navbar).hide();
            }
        });
    });
});
    </script>
</body>
</html>
