<?php
// Verificar si se ha subido un archivo
if (isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    // Definir el directorio donde se almacenará la imagen
    $upload_dir = 'uploads/';
    
    // Verificar si el directorio existe, si no, crearlo
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Obtener la extensión del archivo
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    // Definir un nombre único para el archivo
    $unique_name = uniqid('profile_', true) . '.' . $file_extension;

    // Definir la ruta de destino
    $upload_path = $upload_dir . $unique_name;

    // Mover el archivo al directorio de destino
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        echo "Archivo subido con éxito.";
        // Puedes hacer algo más, como guardar la ruta en la base de datos
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "No se seleccionó ningún archivo.";
}
?>
