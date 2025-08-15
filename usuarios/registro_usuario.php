<?php
include '../includes/proteccion.php';
?>

<?php
// registro_usuario.php
include '../includes/conexion.php'; // Asegúrate de tener tu conexión configurada

// Inicializar $mensaje para evitar el warning
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // Validaciones mínimas
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo inválido.";
    } elseif (!in_array($rol, ['ingeniero', 'encargado'])) {
        $mensaje = "Rol no permitido.";
    } elseif (strlen($password) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Verificar si el correo ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            $mensaje = "El correo ya está registrado.";
        } else {
            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar nuevo usuario
            $sql = "INSERT INTO usuarios (nombre, email, contraseña, rol) 
                    VALUES (:nombre, :email, :password, :rol)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':email' => $email,
                ':password' => $password_hash,
                ':rol' => $rol
            ]);

            $mensaje = "Usuario registrado exitosamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - SMAQ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4ade80;
            --danger: #f87171;
            --warning: #fbbf24;
            --border-radius: 16px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f7ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .register-container {
            max-width: 900px;
            width: 100%;
            perspective: 1000px;
        }
        
        .register-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transform-style: preserve-3d;
            transition: var(--transition);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        .card-header h3 {
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 2;
            font-size: 1.8rem;
        }
        
        .card-header i {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .card-body {
            padding: 40px;
        }
        
        .form-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
            transition: var(--transition);
        }
        
        .form-control {
            padding-left: 60px;
            border-radius: 12px;
            height: 55px;
            border: 2px solid #e1e5eb;
            font-size: 1.05rem;
            transition: var(--transition);
            background: rgba(245, 247, 250, 0.6);
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.2);
            background: white;
            transform: translateY(-2px);
        }
        
        .form-control:focus + .form-icon {
            color: var(--accent);
            transform: translateY(-50%) scale(1.1);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 5;
            font-size: 1.1rem;
            transition: var(--transition);
        }
        
        .password-toggle:hover {
            color: var(--primary);
            transform: translateY(-50%) scale(1.1);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border: none;
            padding: 15px 30px;
            font-weight: 600;
            border-radius: 12px;
            transition: var(--transition);
            height: 55px;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(to right, var(--secondary), var(--primary));
            transition: var(--transition);
            z-index: -1;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }
        
        .btn-primary:hover::before {
            width: 100%;
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 15px 30px;
            font-weight: 600;
            border-radius: 12px;
            transition: var(--transition);
            height: 55px;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(108, 117, 125, 0.3);
        }
        
        .alert {
            border-radius: 12px;
            padding: 18px 25px;
            margin-bottom: 30px;
            font-size: 1.05rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .alert::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }
        
        .alert-success {
            background: rgba(76, 222, 128, 0.15);
            border-left: 5px solid var(--success);
            color: #0f5132;
        }
        
        .alert-danger {
            background: rgba(248, 113, 113, 0.15);
            border-left: 5px solid var(--danger);
            color: #842029;
        }
        
        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .brand-logo h2 {
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .brand-logo p {
            color: #4a5568;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .illustration {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            position: relative;
        }
        
        .illustration-svg {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(67, 97, 238, 0.2));
        }
        
        .password-strength {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin-top: 10px;
            overflow: hidden;
            position: relative;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            border-radius: 4px;
            transition: width 0.5s ease, background 0.5s ease;
        }
        
        .password-rules {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 8px;
        }
        
        .form-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #718096;
            font-weight: 500;
        }
        
        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            border-bottom: 2px dashed #e2e8f0;
        }
        
        .form-divider::before {
            margin-right: 15px;
        }
        
        .form-divider::after {
            margin-left: 15px;
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
            opacity: 0.1;
        }
        
        .shape-1 {
            width: 120px;
            height: 120px;
            background: var(--primary);
            top: 10%;
            left: 5%;
        }
        
        .shape-2 {
            width: 80px;
            height: 80px;
            background: var(--accent);
            bottom: 15%;
            right: 7%;
        }
        
        .shape-3 {
            width: 60px;
            height: 60px;
            background: var(--secondary);
            top: 40%;
            right: 20%;
        }
        
        @media (max-width: 992px) {
            .register-container {
                max-width: 600px;
            }
            
            .illustration {
                display: none;
            }
            
            .card-body {
                padding: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .card-header {
                padding: 20px;
            }
            
            .card-header h3 {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 25px 20px;
            }
            
            .form-control {
                padding-left: 50px;
                height: 50px;
            }
            
            .btn-primary, .btn-secondary {
                padding: 12px 20px;
                height: 50px;
                font-size: 1rem;
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
    
    <div class="register-container">
        <div class="brand-logo">
            <h2><i class="fas fa-cogs"></i> SMAQ</h2>
            <p>Sistema de Mantenimiento y Administración de Equipos</p>
        </div>
        
        <div class="register-card" id="registerCard">
            <div class="card-header">
                <h3><i class="fas fa-user-plus"></i> Registro de Nuevo Usuario</h3>
            </div>
            
            <div class="row g-0">
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="illustration">
                        <svg class="illustration-svg" width="300" height="300" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="250" cy="200" r="80" fill="#4361ee" opacity="0.1"/>
                            <circle cx="250" cy="200" r="60" fill="#4361ee" opacity="0.2"/>
                            <circle cx="250" cy="200" r="40" fill="#4361ee" opacity="0.3"/>
                            <circle cx="250" cy="200" r="20" fill="#4361ee"/>
                            <path d="M150 350 L350 350 L320 450 L180 450 Z" fill="#3a0ca3" opacity="0.7"/>
                            <path d="M200 300 L300 300 L280 350 L220 350 Z" fill="#4361ee" opacity="0.8"/>
                            <circle cx="200" cy="130" r="20" fill="#4cc9f0"/>
                            <circle cx="300" cy="130" r="20" fill="#4cc9f0"/>
                            <path d="M230 250 Q250 280 270 250" stroke="#3f37c9" stroke-width="10" fill="none"/>
                            <circle cx="250" cy="180" r="10" fill="white"/>
                        </svg>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert <?= strpos($mensaje, 'exitosamente') !== false ? 'alert-success' : 'alert-danger' ?>">
                                <i class="fas <?= strpos($mensaje, 'exitosamente') !== false ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i>
                                <?= htmlspecialchars($mensaje) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="registerForm">
                            <div class="input-group">
                                <span class="form-icon"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre completo" required>
                            </div>
                            
                            <div class="input-group">
                                <span class="form-icon"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Correo electrónico" required>
                            </div>
                            
                            <div class="input-group">
                                <span class="form-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" required minlength="6">
                                <button type="button" class="password-toggle" id="passwordToggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            
                            <div class="password-strength">
                                <div class="strength-meter" id="strengthMeter"></div>
                            </div>
                            <div class="password-rules">
                                <small>La contraseña debe tener al menos 6 caracteres</small>
                            </div>
                            
                            <div class="form-divider">Seleccione el rol</div>
                            
                            <div class="input-group">
                                <span class="form-icon"><i class="fas fa-user-tag"></i></span>
                                <select class="form-select" name="rol" id="rol" required>
                                    <option value="">Seleccione un rol</option>
                                    <option value="ingeniero">Ingeniero de Mantenimiento</option>
                                    <option value="encargado">Encargado</option>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-3 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i> Registrar Usuario
                                </button>
                                <a href="../dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted">
            <small>&copy; <?= date('Y') ?> SMAQ - Sistema de Mantenimiento y Administración de Equipos</small>
        </div>
    </div>

    <script>
        // Animaciones iniciales con Anime.js
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada de la tarjeta
            anime({
                targets: '.register-card',
                translateY: [30, 0],
                opacity: [0, 1],
                scale: [0.95, 1],
                duration: 1200,
                easing: 'easeOutElastic(1, .8)',
                delay: 300
            });
            
            // Animación de los elementos del formulario
            anime({
                targets: '.input-group',
                translateY: [20, 0],
                opacity: [0, 1],
                duration: 800,
                delay: anime.stagger(100),
                easing: 'easeOutQuad'
            });
            
            // Animación de los botones
            anime({
                targets: '.btn',
                translateY: [15, 0],
                opacity: [0, 1],
                duration: 800,
                delay: 1000,
                easing: 'easeOutBack'
            });
            
            // Animación de formas flotantes
            anime({
                targets: '.shape-1',
                translateY: [0, -30],
                translateX: [0, 20],
                duration: 4000,
                direction: 'alternate',
                loop: true,
                easing: 'easeInOutSine'
            });
            
            anime({
                targets: '.shape-2',
                translateY: [0, 40],
                translateX: [0, -30],
                duration: 5000,
                direction: 'alternate',
                loop: true,
                easing: 'easeInOutSine',
                delay: 500
            });
            
            anime({
                targets: '.shape-3',
                translateY: [0, -50],
                translateX: [0, 40],
                duration: 4500,
                direction: 'alternate',
                loop: true,
                easing: 'easeInOutSine',
                delay: 1000
            });
        });

        // Toggle para mostrar/ocultar contraseña
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordField = document.getElementById('password');
        const strengthMeter = document.getElementById('strengthMeter');
        
        passwordToggle.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            
            // Animación del ícono
            anime({
                targets: this.querySelector('i'),
                scale: [1, 1.3, 1],
                duration: 300,
                easing: 'easeInOutQuad'
            });
        });
        
        // Indicador de fortaleza de contraseña
        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength += 25;
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            
            // Animación de la barra de fortaleza
            anime({
                targets: strengthMeter,
                width: strength + '%',
                duration: 500,
                easing: 'easeOutQuad',
                backgroundColor: function() {
                    if (strength < 50) return '#ef4444';
                    if (strength < 75) return '#f59e0b';
                    return '#10b981';
                }
            });
        });
        
        // Animación al enfocar campos
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                anime({
                    targets: this,
                    scale: 1.02,
                    duration: 300,
                    easing: 'easeOutQuad'
                });
            });
            
            input.addEventListener('blur', function() {
                anime({
                    targets: this,
                    scale: 1,
                    duration: 300,
                    easing: 'easeOutQuad'
                });
            });
        });
        
        // Animación al enviar el formulario
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            anime({
                targets: this,
                translateY: [0, -10],
                duration: 200,
                direction: 'alternate',
                easing: 'easeInOutSine',
                loop: 2
            });
        });
    </script>
</body>
</html>