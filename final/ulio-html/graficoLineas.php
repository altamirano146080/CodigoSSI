<?php
// Establecer los detalles de conexión a la base de datos
include("basededatos.php");
// Crear conexión
$conn = conexion();

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// SQL para obtener el número de fotos por año y mes
$sql = "SELECT DATE_FORMAT(photo_date, '%Y-%m') AS YearMonth, COUNT(*) AS PhotoCount FROM final_Foto GROUP BY YearMonth ORDER BY YearMonth";
$result = $conn->query($sql);

// Preparar los datos para la gráfica
$labels = [];
$values = [];

if ($result->num_rows > 0) {
    // Obtener datos de cada fila
    while($row = $result->fetch_assoc()) {
        $labels[] = $row["YearMonth"];
        $values[] = $row["PhotoCount"];
    }
} else {
    echo "0 resultados";
}

// Cerrar conexión
$conn->close();

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode(array("labels" => $labels, "values" => $values));
?>
