<?php
include '../includes/proteccion.php';
?>

<?php
// registro_usuario.php
include '../includes/conexion.php'; // Asegúrate de tener tu conexión configurada

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
    <title>Registro de Usuario - SMAQ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Registro de Usuario</h5>
        </div>
        <div class="card-body">
            <?php if ($mensaje): ?>
                <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" required minlength="6">
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select class="form-select" name="rol" id="rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="ingeniero">Ingeniero de Mantenimiento</option>
                        <option value="encargado">Encargado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Registrar Usuario</button>
                <a href="../dashboard.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
