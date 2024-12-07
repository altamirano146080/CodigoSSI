<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "sql";
$dbname = "ssi";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para crear tablas
function crearTabla($conn, $sql, $nombreTabla) {
    if ($conn->query($sql) === TRUE) {
        echo "Tabla '$nombreTabla' creada exitosamente.<br>";
    } else {
        echo "Error creando la tabla '$nombreTabla': " . $conn->error . "<br>";
    }
}

// Creación de las tablas
$tablas = [
    "final_Foto" => "
        CREATE TABLE final_Foto (
            photo_id INT NOT NULL PRIMARY KEY,
            rover_id VARCHAR(255) NULL,
            photo_date DATE NULL,
            photo_url VARCHAR(255) NULL,
            sol INT NULL
        )",
    "final_Rover" => "
        CREATE TABLE final_Rover (
            name VARCHAR(255) NOT NULL PRIMARY KEY,
            status VARCHAR(50) NULL,
            launch_date DATE NULL,
            landing_date DATE NULL,
            max_sol INT NULL,
            max_date DATE NULL,
            total_photos INT NULL
        )",
    "final_administradores" => "
        CREATE TABLE final_administradores (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL
        )",
    "final_apod_images" => "
        CREATE TABLE final_apod_images (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            date DATE NULL,
            title VARCHAR(255) NULL,
            explanation TEXT NULL,
            url VARCHAR(255) NULL,
            hd_url VARCHAR(255) NULL,
            media_type VARCHAR(50) NULL
        )",
    "final_categories" => "
        CREATE TABLE final_categories (
            id_categories INT NOT NULL PRIMARY KEY,
            title TEXT NOT NULL,
            link TEXT NOT NULL,
            description TEXT NOT NULL,
            layers TEXT NOT NULL
        )",
    "final_events" => "
        CREATE TABLE final_events (
            id_evento VARCHAR(20) NOT NULL PRIMARY KEY,
            title TEXT NOT NULL,
            description TEXT NULL,
            link TEXT NOT NULL,
            categories INT NOT NULL,
            closed DATE NULL
        )",
    "final_fecha_actualizacion" => "
        CREATE TABLE final_fecha_actualizacion (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
    "final_geometries" => "
        CREATE TABLE final_geometries (
            event_id TEXT NOT NULL,
            geometry JSON NOT NULL,
            id_geometry INT NOT NULL AUTO_INCREMENT PRIMARY KEY
        )",
    "final_source" => "
        CREATE TABLE final_source (
            id_source VARCHAR(20) NOT NULL PRIMARY KEY,
            title TEXT NOT NULL,
            source TEXT NOT NULL,
            link TEXT NOT NULL
        )",
    "final_source_links" => "
        CREATE TABLE final_source_links (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            link TEXT NOT NULL,
            id_source VARCHAR(20) NOT NULL,
            id_event VARCHAR(20) NOT NULL
        )",
    "final_usuarios" => "
        CREATE TABLE final_usuarios (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(50) NOT NULL,
            gmail VARCHAR(100) NOT NULL UNIQUE,
            contrasena VARCHAR(255) NOT NULL,
	    foto VARCHAR(255) NOT NULL DEFAULT 'img/logo.png'
        )",
    "Cuy" => "
	Create table cuy (
		id INT PRIMARY KEY
	)"
];

// Ejecutar las consultas de creación de tablas
foreach ($tablas as $nombreTabla => $sql) {
    crearTabla($conn, $sql, $nombreTabla);
}

// Cerrar conexión
$conn->close();
?>
