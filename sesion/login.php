<?php
$mensaje = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'login_required':
            $mensaje = 'Debes iniciar sesión para acceder al sistema.';
            break;
        case 'session_expired':
            $mensaje = 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.';
            break;
        case 'login':
            $mensaje = 'Usuario o contraseña incorrectos.';
            break;
        case 'empty':
            $mensaje = 'Por favor, ingresa usuario y contraseña.';
            break;
    }
}
?>

<?php if ($mensaje): ?>
    <div id="mensajeError" class="toast-error"><?= htmlspecialchars($mensaje) ?></div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const errorBox = document.getElementById('mensajeError');
        if (errorBox) {
            setTimeout(() => errorBox.remove(), 3000);
        }
    });
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - SMAQ</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="background-overlay" id="backgroundOverlay"></div>
    <div class="login-container">
        <form class="login-form" action="validar.php" method="POST">
            <img src="smaq.png" alt="Logo SMAQ" class="logo-login">
            <h2>Bienvenido a SMAQ</h2>
            <div class="input-group">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="input-group">
                <label for="clave">Contraseña</label>
                <input type="password" id="clave" name="clave" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>
    </div>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'login'): ?>
    <div id="mensajeError" class="toast-error">
        Usuario o contraseña incorrectos
    </div>
<?php endif; ?>
<?php if (isset($_GET['validando'])): ?>
<div id="validandoOverlay" style="
    position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.6); z-index:99999; display:flex; align-items:center; justify-content:center;">
    <div style="background:white; color:#333; padding:40px 30px; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.2); text-align:center;">
        <div style="font-size:2.5em; margin-bottom:10px;">
            <i class="bi bi-person-check"></i>
        </div>
        <div style="font-size:1.2em; font-weight:bold;">Validando usuario...</div>
    </div>
</div>
<script>
setTimeout(function() {
    window.location.href = "dashboard.php";
}, 1000);
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const errorBox = document.getElementById('mensajeError');
    if (errorBox) {
        setTimeout(() => {
            errorBox.remove();
        }, 2000); // 2 segundos (1000 = 1 seg)
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('backgroundOverlay');
    const inputs = document.querySelectorAll('.login-form input');

    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            overlay.classList.add('active');
        });

        input.addEventListener('blur', () => {
            // Solo quitar si ningún input tiene el foco
            setTimeout(() => {
                if (![...inputs].some(i => i === document.activeElement)) {
                    overlay.classList.remove('active');
                }
            }, 100);
        });
    });
});
</script>

</body>
</html>
