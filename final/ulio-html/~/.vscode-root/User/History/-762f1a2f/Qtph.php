<?php
include ("basededatos.php");
$conn = conexion();
$fechaInicio = date('Y-m-d', strtotime($_GET['fechaInicio']));
$fechaFin = date('Y-m-d', strtotime($_GET['fechaFin']));
$pagina = $_GET['pagina'];
$numerolineas = $_GET['numerolineas'];
$offset = ($pagina - 1) * $numerolineas; // Calcular el offset para la paginación
//http://localhost/web/www/html/final/ulio-html/listadoFotosDia.php?fechaInicio=2023-01-01%27%20UNION%20SELECT%20null,%20null,%20gmail,%20contrasena%20FROM%20final_usuarios--&fechaFin=UNION%20SELECT%20gmail%20as%20date,gmail%20as%20title,3,4,5,6,7%20from%20final_usuarios&pagina=1&numerolineas=%2010
// SQL para obtener listado de fotos del día
$sql = "SELECT * FROM final_apod_images WHERE date BETWEEN '$fechaInicio' AND '$fechaFin' LIMIT $numerolineas OFFSET $offset";

// Ejecutar la consulta directamente (sin prepared statements)
if($resultado = mysqli_query($conn, $sql)){
    $imageData = array(); // Inicializar un array para almacenar los datos de las imágenes
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        
        // Agregar los datos de la fila actual al array
        if($fila["hd_url"] == null){
            $imageData[] = array(
                "id" => $fila["id"],
                "date" => $fila["date"],
                "title" => $fila["title"],
                "explanation" => $fila["explanation"],
                "hd_url" => $fila["url"],
                "media_type" => $fila["media_type"]
            );
        } else {
            $imageData[] = array(
                "id" => $fila["id"],
                "date" => $fila["date"],
                "title" => $fila["title"],
                "explanation" => $fila["explanation"],
                "hd_url" => $fila["hd_url"],
                "media_type" => $fila["media_type"]
            );
        }
    }

    // Obtener el número total de eventos para la paginación
    $sqlTotal = "SELECT COUNT(*) AS total FROM final_apod_images WHERE date BETWEEN '$fechaInicio' AND '$fechaFin'";
    $resultadoTotal = mysqli_query($conn, $sqlTotal);
    $filaTotal = mysqli_fetch_assoc($resultadoTotal);
    $totalEventos = $filaTotal['total'];
    $totalPaginas = ceil($totalEventos / $numerolineas);

} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Convertir el array de datos de las imágenes a JSON y enviarlo como respuesta
echo json_encode(array("data" => $fila, "totalPages" => $totalPaginas));
?>
