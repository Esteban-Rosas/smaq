<?php
// includes/conexion.php

$host = 'dpg-d2ah3gjuibrs73ael9vg-a.oregon-postgres.render.com';     // o IP de tu servidor
$port = '5432';          // Puerto por defecto de PostgreSQL
$dbname = 'smaq';        // Nombre de tu base de datos
$user = 'smaq_user';    // Reemplaza con tu usuario PostgreSQL
$password = 'FBARdr3pMYwg04QVZkJyJ1ZJxLY8aDeh'; // Reemplaza con tu contraseña

try {
    $conexion = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa"; // Puedes descomentar para probar
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    exit;
}
?>
