<?php
include("basededatos.php");
$conn = conexion();

$roverId = $_GET['rover_id'];

// SQL para obtener listado de fotos del día sin paginación
$sql = "SELECT photo_id, rover_id, photo_date, photo_url, sol FROM final_Foto WHERE rover_id = $roverId";

$resultado = mysqli_query($conn, $sql);
  
if ($resultado) {
    echo("entra");
    print_r($resultado);
    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Añadir la fila directamente al array de imagenes
        $imageData[] = $fila;
    }
    mysqli_free_result($resultado);
} else {
    echo "Error ejecutando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

echo json_encode(array("data" => $imageData));
?>
