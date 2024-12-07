<?php
$servidor = "dbserver";
$bd = "db_grupo04";
$user = "grupo04";
$password = "IePhi3ooho";

$conn = mysqli_connect($servidor, $user, $password, $bd);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$fecha = "date";
// SQL para obtener la foto más reciente
$sql = "SELECT hd_url, explanation FROM final_apod_images ORDER BY $fecha DESC LIMIT 1";
$result = $conn->query($sql);

$imageData = array();

if ($result->num_rows > 0) {
    // Obtener los datos de la fila
    echo "sale";
    $row = $result->fetch_assoc();
    $imageData = $row;
} else {
    $imageData = array("url" => "ruta_a_una_imagen_predeterminada.jpg", "descripcion" => "No hay descripción disponible");
}

$conn->close();

echo json_encode($imageData);
?>
