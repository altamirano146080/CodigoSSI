<?php
// Establecer los detalles de conexión a la base de datos
include("basededatos.php");
// Crear conexión
$conn = conexion();

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// SQL para obtener el número de eventos por categoría
$sql = "SELECT ca.title, COUNT(e.id_evento) AS EventCount 
FROM final_events e 
JOIN final_categories ca ON e.categories = ca.id_categories
GROUP BY ca.title";
$result = $conn->query($sql);

// Preparar los datos para la gráfica
$labels = [];
$values = [];

if ($result->num_rows > 0) {
    // Obtener datos de cada fila
    while($row = $result->fetch_assoc()) {
        $labels[] = $row["title"]; // Usando 'title' para mostrar el nombre de la categoría
        $values[] = $row["EventCount"];
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
