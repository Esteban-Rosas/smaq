<?php
include '../includes/proteccion.php';
include '../includes/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Eliminar la solicitud
        $sql = "DELETE FROM solicitudes_mantenimiento WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id]);
        
        // Redirigir de vuelta al listado con mensaje de éxito
        header('Location: listado_mantenimiento.php');
        exit();
    } catch (PDOException $e) {
        // Manejar errores
        header('Location: listado_mantenimiento.php' . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no se proporciona ID, redirigir
    header('Location: listado_mantenimiento.php');
    exit();
}
?>