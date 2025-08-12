<?php

include '../includes/conexion.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$campo = $data['campo'] ?? '';
$valor = $data['valor'] ?? '';

$permitidos = ['fecha_inicio', 'fecha_fin'];

if ($id && in_array($campo, $permitidos)) {
    try {
        // Depuración
        // file_put_contents('debug_cronograma.txt', print_r($data, true), FILE_APPEND);

        $sql = "UPDATE cronogramas SET $campo = :valor WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':valor' => $valor, ':id' => $id]);

        // Verifica si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se actualizó ningún registro.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
}

