<?php

include "basededatos.php";
$con = conexion();

// Comprobar si el correo electrónico ya existe en la base de datos
$stmt_check_email = $con->prepare("SELECT COUNT(*) AS count FROM final_usuarios WHERE Gmail = ?");
if (!$stmt_check_email) {
    die("Error en la preparación de la consulta: " . $con->error);
}

$stmt_check_email->bind_param("s", $gmail);

$gmail = $_POST['gmail'];

$stmt_check_email->execute();
$stmt_check_email->bind_result($count);
$stmt_check_email->fetch();
$stmt_check_email->close();

if ($count > 0) {
    session_start();
    $_SESSION['error_message'] = "El correo electrónico ya está registrado en la base de datos. Por favor, intenta con otro correo electrónico.";
    header("Location: ../ulio-html/registrar.html"); // Redireccionar al formulario de registro
    exit; // Detener la ejecución del script
}

// Insertar usuario si el correo electrónico no existe
$stmt_insert = $con->prepare("INSERT INTO final_usuarios (nombre, gmail, contrasena) VALUES (?, ?, ?)");
if (!$stmt_insert) {
    die("Error en la preparación de la consulta: " . $con->error);
}

$stmt_insert->bind_param("sss", $nombre, $gmail, $contraseina);

$nombre = $_POST['nombre'];
$gmail = $_POST['gmail'];
$contraseina = md5($_POST['contraseina']);

$stmt_insert->execute();

if ($stmt_insert->affected_rows) {
    echo "Se ha insertado correctamente";
} else {
    echo "No se ha insertado la persona";
}


// Comprobar si el correo electrónico ya existe en la base de datos
$stmt_check_email = $con->prepare("SELECT id FROM final_usuarios WHERE Gmail = ?");
if (!$stmt_check_email) {
    die("Error en la preparación de la consulta: " . $con->error);
}

$stmt_check_email->bind_param("s", $gmail);

$stmt_check_email->execute();
$stmt_check_email->bind_result($id);
$stmt_check_email->fetch();
$stmt_check_email->close();



session_start();
// Iniciar sesión
$_SESSION['user'] = $gmail;
$_SESSION['passwd'] = $contraseina;
$_SESSION['tiempo'] = time();
$_SESSION['id'] = $id;



$stmt_insert->close();
$con->close();
header("Location: ../ulio-html/index.html");
exit;

?>
