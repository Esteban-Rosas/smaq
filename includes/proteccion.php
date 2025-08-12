<?php
session_start();

// Verifica si la sesión existe
if (!isset($_SESSION['usuario_id'])) {
    // Redirige con mensaje de error
    header('Location: /smaq/sesion/login.php?error=login_required');
    exit;
}

// Verifica el tiempo de inactividad (30 min)
$tiempo_inactivo = 1800;
if (isset($_SESSION['ultimo_acceso']) && (time() - $_SESSION['ultimo_acceso']) > $tiempo_inactivo) {
    session_unset();
    session_destroy();
    header('Location: ../sesion/login.php?error=session_expired');
    exit;
}

// Renueva el tiempo de actividad
$_SESSION['ultimo_acceso'] = time();
