<?php
include("basededatos.php");

// Crear conexión
$conn = conexion();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = $_POST['table'];

// Obtener columnas
$sqlColumns = "SHOW COLUMNS FROM $table";
$resultColumns = $conn->query($sqlColumns);

$columns = array();

if ($resultColumns->num_rows > 0) {
    while ($row = $resultColumns->fetch_assoc()) {
        $columns[] = $row;
    }
}

// Contar filas
$sqlRowCount = "SELECT COUNT(*) as count FROM $table";
$resultRowCount = $conn->query($sqlRowCount);

$rowCount = 0;
if ($resultRowCount->num_rows > 0) {
    $row = $resultRowCount->fetch_assoc();
    $rowCount = $row['count'];
}

$conn->close();

$response = array(
    'columns' => $columns,
    'rowCount' => $rowCount
);

echo json_encode($response);
?>
