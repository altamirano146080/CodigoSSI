<?php
include("basededatos.php");
$conn = conexion();

$categorieId = $_GET['categorieId'];

// SQL para obtener listado de fotos del día
$sql = "SELECT id_evento, title, description, link, categories FROM final_events  WHERE categories = ? LIMIT 20" ;

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $categorieId);

// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $imageData = array(); // Inicializar un array para almacenar los datos de las imágenes

    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Agregar los datos de la fila actual al array
        $imageData[] = array(
            "eventoId"  => $fila["id_evento"],
            "title"  => $fila["title"],
            "descripcion"  => $fila["description"],
            "link"  => $fila["link"],
            "categories"  => $fila["categories"]
        );
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Convertir el array de datos de las imágenes a JSON y enviarlo como respuesta
echo json_encode($imageData);
?>