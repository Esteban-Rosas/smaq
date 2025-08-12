<?php

$host = 'localhost';     // o IP de tu servidor
$port = '5432';          // Puerto por defecto de PostgreSQL
$dbname = 'smaq';        // Nombre de tu base de datos
$user = 'postgres';    // Reemplaza con tu usuario PostgreSQL
$password = '3013162608Zel@'; // Reemplaza con tu contraseña

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
