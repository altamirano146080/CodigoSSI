<?php
// Verificar si el archivo se ha subido correctamente
if (isset($_FILES['profile_picture'])) {
    echo "<script>console.log('Archivo recibido por PHP');</script>";  // Mensaje de consola en el navegador

    $file = $_FILES['profile_picture'];

    // Imprimir detalles del archivo en la consola del navegador
    echo "<script>console.log('Detalles del archivo subido:', ".json_encode($_FILES['profile_picture']).");</script>";

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
        // Imprimir el resultado de la carga del archivo en la consola
        echo "<script>console.log('Archivo subido con éxito a:', '". $upload_path ."');</script>";
        
        // Obtener el ID del usuario (supongamos que se pasa como parámetro)
        $user_id = $_SESSION['id']; 

        // Preparar la consulta SQL para actualizar la foto del usuario
        $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
        
        // Preparar la declaración
        $stmt = $conn->prepare($sql);
        
        // Vincular parámetros
        $stmt->bind_param('si', $upload_path, $user_id);
        
        // Ejecutar la declaración
        if ($stmt->execute()) {
            echo "<script>console.log('Foto de perfil actualizada con éxito');</script>";  // Mensaje en consola
            echo "Foto de perfil actualizada con éxito.";
        } else {
            echo "<script>console.log('Error al actualizar la foto de perfil:', '".$stmt->error."');</script>";  // Mensaje en consola
            echo "Error al actualizar la foto de perfil: " . $stmt->error;
        }
        
        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "<script>console.log('Error al subir el archivo');</script>";  // Mensaje en consola
        echo "Error al subir el archivo.";
    }
} else {
    echo "<script>console.log('No se seleccionó ningún archivo');</script>";  // Mensaje en consola
    echo "No se seleccionó ningún archivo.";
}

// Cerrar la conexión
$conn->close();
?>
