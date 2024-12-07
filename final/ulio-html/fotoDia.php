<?php

header('Content-Type: application/json');

include("basededatos.php");

$conn = conexion();

// SQL para obtener la foto más reciente, incluyendo media_type
$sql = "SELECT hd_url, url, explanation, date, title, media_type FROM final_apod_images ORDER BY date DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Crear el arreglo con los datos necesarios
        $imageData = array(
            "url" => $fila["media_type"] == "video" ? $fila["url"] : $fila["hd_url"],
            "descripcion" => $fila["explanation"],
            "date" => $fila["date"],
            "title" => $fila["title"],
            "media" => $fila["media_type"]
        );
    } else {
        // Caso en el que no se encuentre ningún resultado
        $imageData = array(
            "url" => "ruta_a_una_imagen_predeterminada.jpg",
            "descripcion" => "No hay descripción disponible",
            "date" => "sin FECHA",
            "title" => "sin TITULO",
            "media" => "sin MEDIA"
        );
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(array("error" => "Error preparando la consulta: " . mysqli_error($conn)));
    exit;
}

mysqli_close($conn);

echo json_encode($imageData);
?>
