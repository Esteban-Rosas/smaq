<?php
// Configuración de la base de datos
$host = "localhost";
$port = "5432";
$dbname = "smaq"; // Cambia por el nombre real de tu base de datos
$user = "postgres"; // Usuario de la base de datos
$password = "toor"; // Contraseña del usuario

try {
    // Crear conexión PDO
    $conexion = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    
    // Configuración de PDO
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>
