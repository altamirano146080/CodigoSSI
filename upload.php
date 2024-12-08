// Este es el archivo upload.php

<?php
include("basededatos.php");
session_start();
$con = conexion();
// Verificar si el archivo ha sido subido correctamente
if (isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    // Definir el directorio donde se almacenará la imagen
    $upload_dir = 'uploads/';

    // Verificar si el directorio existe, si no, crearlo
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Obtener la extensión del archivo
    //$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    // Definir un nombre único para el archivo
    //$unique_name = uniqid($file['name'], true) . '.' . $file_extension;

    // Definir la ruta de destino
    //$upload_path = $upload_dir . $unique_name;
    $upload_path =$upload_dir . $file['name'];
    // Mover el archivo al directorio de destino
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Obtener el ID del usuario
        $user_id = $_SESSION['id']; 

        // Preparar la consulta SQL para actualizar la foto del usuario
        $sql = "UPDATE final_usuarios SET foto = ? WHERE id = ?";
        
        // Preparar la declaración
        $stmt = $con->prepare($sql);
        
        // Vincular parámetros
        $stmt->bind_param('si', $upload_path, $user_id);
        
        // Ejecutar la declaración
        if ($stmt->execute()) {
            echo "Foto de perfil actualizada con éxito.";  // Esto será mostrado en la consola del navegador
        } else {
            echo "Error al actualizar la foto de perfil: " . $stmt->error;
        }
        
        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "No se seleccionó ningún archivo.";
}

// Cerrar la conexión
$con->close();
?>
