<?php
$host = 'dpg-d2ah3gjuibrs73ael9vg-a.oregon-postgres.render.com';
$user = 'smaq_user';
$password = 'FBARdr3pMYwg04QVZkJyJ1ZJxLY8aDeh';
$dbname = 'smaq';

try {
    $conexion = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    echo "Conexión exitosa (sin SSL)";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
