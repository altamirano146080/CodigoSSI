<?php
// Datos de conexión a la base de datos
include("basededatos.php");
// Crear conexión
$conn = conexion();

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// URL de la API
$url = "https://eonet.gsfc.nasa.gov/api/v2.1/sources";

// Obtener datos JSON de la API
$json_data = file_get_contents($url);

// Decodificar el JSON
$data = json_decode($json_data, true);

// Verificar si se obtuvieron los datos correctamente
if ($data && isset($data['sources'])) {
    // Insertar los datos en la base de datos
    foreach ($data['sources'] as $source) {
        $id = $source['id'];
        $title = $conn->real_escape_string($source['title']);
        $source_url = $conn->real_escape_string($source['souce']);
        $link = $conn->real_escape_string($source['link']);
        
        // Construir la consulta SQL de inserción
        $sql = "INSERT INTO final_source (id_source, title, source,link) 
                VALUES ('$id', '$title', '$source_url', '$link')";
        
        // Ejecutar la consulta SQL
        if ($conn->query($sql) === TRUE) {
            echo "Registro insertado correctamente: $title <br>";
        } else {
            echo "Error al insertar registro: " . $conn->error . "<br>";
        }
    }
} else {
    echo "No se pudieron obtener los datos de la API.";
}

$sqlupdate = "INSERT INTO final_fecha_actualizacion(id, update_time) VALUES (1, NOW())
    ON DUPLICATE KEY UPDATE update_time = NOW()";

// Ejecutar la consulta SQL
if (mysqli_query($conn, $sqlupdate)) {
    echo "La fecha de actualización se ha registrado correctamente.";
} else {
    echo "Error al registrar la fecha de actualización: " . mysqli_error($conn);
}

// Cerrar la conexión
$conn->close();
?>
