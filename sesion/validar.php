<?php
session_start();
include '../includes/conexion.php';

$email = trim($_POST['usuario'] ?? '');
$clave = trim($_POST['clave'] ?? '');

if ($email === '' || $clave === '') {
    header("Location: login.php?error=empty");
    exit;
}

// Buscar al usuario por correo
$sql = "SELECT id, nombre, email, contraseña, rol FROM usuarios WHERE email = :email";
$stmt = $conexion->prepare($sql);
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si existe y si la contraseña coincide
if ($usuario && password_verify($clave, $usuario['contraseña'])) {
    session_regenerate_id(true); // Protección contra session fixation

    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['ultimo_acceso'] = time();

    header('Location: ../dashboard.php');
    exit;
}
 else {
    header("Location: login.php?error=login");
    exit;
}
