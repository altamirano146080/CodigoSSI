<?php
include("basededatos.php");
$conn = conexion();


// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Comprobar conexión
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$roverId = $_GET['rover_id'];
$numerolineas = $_GET['numerolineas'];

// SQL para obtener listado de fotos del día sin paginación
$sql = "SELECT photo_id, rover_id, photo_date, photo_url, sol FROM final_Foto WHERE rover_id = ? LIMIT ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'si', $roverId, $numerolineas);

// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $imageData = array(); // Inicializar un array para almacenar los datos de las imágenes

    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Agregar los datos de la fila actual al array
        $imageData[] = array(
            "fotoId"  => $fila["photo_id"],
            "roverId"  => $fila["rover_id"],
            "title"  => $fila["photo_date"],
            "fotoURL"  => $fila["photo_url"],
            "sol"  => $fila["sol"]
        );
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

echo json_encode(array("data" => $imageData));
?>
