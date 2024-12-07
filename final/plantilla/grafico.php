<?php
// Establecer los detalles de conexión a la base de datos
$servidor = "dbserver";
$bd = "db_grupo04";
$user = "grupo04";
$password = "IePhi3ooho";

// Crear conexión
$conn = mysqli_connect($servidor, $user, $password, $bd);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// SQL para obtener el número de fotos por rover_id
$sql = "SELECT rover_id, COUNT(*) as count FROM final_Foto GROUP BY rover_id";
$result = $conn->query($sql);

// Preparar los datos para la gráfica
$labels = [];
$values = [];

if ($result->num_rows > 0) {
    // Obtener datos de cada fila
    while($row = $result->fetch_assoc()) {
        $labels[] = $row["rover_id"];
        $values[] = $row["count"];
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
