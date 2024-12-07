<?php
include("basededatos.php");
$conn = conexion();

$roverId = $_GET['rover_id'];

// SQL para obtener listado de fotos del día sin paginación
$sql = "SELECT photo_id, rover_id, photo_date, photo_url, sol FROM final_Foto WHERE rover_id = $roverId";

$resultado = mysqli_query($conn, $sql);

// Verificar si la consulta devuelve resultados
if ($resultado) {
    $imageData = array();  // Asegurarse de que $imageData esté inicializado

    // Verificar si hay filas
    if (mysqli_num_rows($resultado) > 0) {
        // Si hay resultados, agregar las filas al array
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $imageData[] = $fila;
        }
    } else {
        // Si no hay resultados, puedes manejarlo aquí
        echo "No se encontraron fotos para el rover_id: $roverId";
        $imageData = array();  // Devolver un array vacío
    }

    mysqli_free_result($resultado);
} else {
    echo "Error ejecutando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Devolver el array de resultados en formato JSON
echo json_encode(array("data" => $imageData));
?>