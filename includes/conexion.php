<?php
$host = 'dpg-d2ah3gjuibrs73ael9vg-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'smaq';
$user = 'smaq_user';
$password = 'FBARdr3pMYwg04QVZkJyJ1ZJxLY8aDeh';

try {
    $conexion = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require", // Fuerza SSL
        $user,
        $password
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Error de conexión: " . $e->getMessage()); // Mejor para producción
    header("Location: error.php"); // Redirige a una página de error
    exit;
}
?>
