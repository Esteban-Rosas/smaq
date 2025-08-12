<?php
try {
    session_start();
    require __DIR__ . '/../includes/conexion.php'; // Usa require para error fatal si falla

    $email = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if (empty($email) || empty($clave)) {
        header("Location: login.php?error=empty");
        exit;
    }

    $sql = "SELECT id, nombre, email, contraseña, rol FROM usuarios WHERE email = :email";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($clave, $usuario['contraseña'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['ultimo_acceso'] = time();

        header('Location: ../dashboard.php');
        exit;
    } else {
        header("Location: login.php?error=login");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error en validar.php: " . $e->getMessage());
    header("Location: login.php?error=db");
    exit;
}
