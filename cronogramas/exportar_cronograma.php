<?php
// filepath: c:\xampp\htdocs\smaq\cronogramas\exportar_cronograma.php
require '../vendor/autoload.php';
include '../includes/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Obtener equipos
$equipos = $conexion->query("SELECT id, nombre FROM equipos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Obtener cronogramas
$sql = "SELECT c.equipo_id, c.tipo_mantenimiento, c.fecha_inicio, c.fecha_fin, e.nombre AS equipo_nombre
        FROM cronogramas c
        JOIN equipos e ON c.equipo_id = e.id";
$cronogramas = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Organizar datos como en la vista
function getWeekOfMonth($date) {
    $firstDay = date('Y-m-01', strtotime($date));
    return ceil((date('d', strtotime($date)) + date('N', strtotime($firstDay)) - 1) / 7);
}

$data = [];
foreach ($cronogramas as $c) {
    $id = $c['equipo_id'];
    $tipo = ucfirst(strtolower(trim($c['tipo_mantenimiento'])));
    $start = strtotime($c['fecha_inicio']);
    $end = strtotime($c['fecha_fin']);
    while ($start <= $end) {
        $month = (int)date('n', $start);
        $week = getWeekOfMonth(date('Y-m-d', $start));
        $data[$id][$month][$week][] = $tipo;
        $start = strtotime('+1 day', $start);
    }
}

// Crear archivo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Cronograma');

// Encabezados
$sheet->setCellValue('A1', 'Equipo');
$col = 2;
$meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
foreach ($meses as $m) {
    for ($s = 1; $s <= 4; $s++) {
        $colString = Coordinate::stringFromColumnIndex($col);
        $sheet->setCellValue($colString . '1', "$m-S$s");
        $col++;
    }
}

// Estilo de encabezados
$sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

// Llenar datos
$fila = 2;
foreach ($equipos as $equipo) {
    $sheet->setCellValue('A' . $fila, $equipo['nombre']);
    $col = 2;
    for ($m = 1; $m <= 12; $m++) {
        for ($s = 1; $s <= 4; $s++) {
            // Mostrar cada tipo de mantenimiento en una línea
            $celda = isset($data[$equipo['id']][$m][$s]) ? implode("\n", array_unique($data[$equipo['id']][$m][$s])) : '';
            $colString = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colString . $fila, $celda);
            // Ajuste de texto para la celda
            $sheet->getStyle($colString . $fila)->getAlignment()->setWrapText(true);
            $col++;
        }
    }
    // Opcional: ajustar el alto de la fila
    $sheet->getRowDimension($fila)->setRowHeight(30);
    $fila++;
}

// Opcional: ajustar ancho de columnas
$lastColNum = Coordinate::columnIndexFromString($sheet->getHighestColumn());
for ($i = 1; $i <= $lastColNum; $i++) {
    $colString = Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($colString)->setAutoSize(true);
}

// Agregar bordes a toda la tabla
$lastCol = $sheet->getHighestColumn();
$lastRow = $sheet->getHighestRow();
$sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => 'FF888888'],
        ],
    ],
]);

// Propiedades del archivo
$spreadsheet->getProperties()
    ->setCreator("SMAQ")
    ->setTitle("Cronograma de Mantenimientos")
    ->setDescription("Exportación automática desde SMAQ");

// Descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="cronograma_mantenimientos.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
