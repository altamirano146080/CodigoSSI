<?php
include("../loginConMod/basededatos.php");
// Crear conexión
$conn = conexion();

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// URL de la API
$url = "https://eonet.gsfc.nasa.gov/api/v2.1/categories";

// Obtener datos JSON de la API
$json_data = file_get_contents($url);

// Decodificar el JSON
$data = json_decode($json_data, true);

// Verificar si se obtuvieron los datos correctamente
if ($data && isset($data['categories'])) {
    // Insertar los datos en la base de datos
    foreach ($data['categories'] as $category) {
        $id = $category['id'];
        $title = $conn->real_escape_string($category['title']);
        $link = $conn->real_escape_string($category['link']);
        $description = $conn->real_escape_string($category['description']);
        $layers = $conn->real_escape_string($category['layers']);
        
        // Construir la consulta SQL de inserción
        $sql = "INSERT INTO final_categories (id_categories, title, link, description, layers) 
                VALUES ('$id', '$title', '$link', '$description', '$layers')";
        
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

// Cerrar la conexión
$conn->close();
?>
