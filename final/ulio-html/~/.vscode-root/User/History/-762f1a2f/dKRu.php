<?php
include ("basededatos.php");
$conn = conexion();
$fechaInicio = date('Y-m-d', strtotime($_GET['fechaInicio']));
$fechaFin = date('Y-m-d', strtotime($_GET['fechaFin']));
$pagina = $_GET['pagina'];
$numerolineas = $_GET['numerolineas'];
$offset = ($pagina - 1) * $numerolineas; // Calcular el offset para la paginación

// SQL para obtener listado de fotos del día
//$sql = "SELECT * FROM final_apod_images WHERE date BETWEEN ? AND ? LIMIT ? OFFSET ?";
//$stmt = mysqli_prepare($conn, $sql);
//mysqli_stmt_bind_param($stmt, 'ssii', $fechaInicio, $fechaFin, $numerolineas, $offset);
$sql = "SELECT * FROM final_apod_images WHERE date BETWEEN '$fechaInicio' AND '$fechaFin' LIMIT $numerolineas OFFSET $offset";

// Ejecutar la consulta directamente (sin prepared statements)
if($resultado = mysqli_query($conn, $sql)){
    //mysqli_stmt_execute($stmt);
    //$resultado = mysqli_stmt_get_result($stmt);

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
        }else{
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
    
    // Obtener el numero total de eventos para la paginacion
    $sqlTotal = "SELECT COUNT(*) AS total FROM final_apod_images WHERE date BETWEEN ? AND ?";
    $stmtTotal = mysqli_prepare($conn, $sqlTotal);
    mysqli_stmt_bind_param($stmtTotal, 'ss', $fechaInicio, $fechaFin);
    mysqli_stmt_execute($stmtTotal);
    $resultadoTotal = mysqli_stmt_get_result($stmtTotal);
    $filaTotal = mysqli_fetch_assoc($resultadoTotal);
    $totalEventos = $filaTotal['total'];
    $totalPaginas = ceil($totalEventos / $numerolineas);

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmtTotal);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Convertir el array de datos de las imágenes a JSON y enviarlo como respuesta
echo json_encode(array("data" => $imageData, "totalPages" => $totalPaginas));
?>
