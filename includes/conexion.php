<?php
// Verifica que NO haya espacios/lineas vacías antes de esta línea
$host = 'dpg-d2ah3gjuibrs73ael9vg-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'smaq';
$user = 'smaq_user';
$password = 'FBARdr3pMYwg04QVZkJyJ1ZJxLY8aDeh';

try {
    $conexion = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conexion; // Retorna la conexión
} catch (PDOException $e) {
    // Log del error sin generar salida
    error_log("Error de conexión: " . $e->getMessage());
    throw new PDOException("Error de base de datos"); // Lanza excepción en lugar de redirigir
}
// Asegúrate de NO tener espacios/líneas después de este cierre ?>
