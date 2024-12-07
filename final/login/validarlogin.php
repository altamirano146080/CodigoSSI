<?php

	session_start();

	include "basededatos.php";

	$user = $_POST['usuario'];
	$passwd = md5($_POST['password']);

	$_SESSION['user'] = $user;
	$_SESSION['passwd'] = $passwd;
	$_SESSION['tiempo'] = time();

	$con = conexion();

	//$consulta = "select * from usuarios where Gmail = '$user'";
	//$resultado = $con->query($consulta);
	//$datos = $resultado->fetch_assoc();

	$consulta = "SELECT * FROM usuarios WHERE Gmail = ?";
	$statement = $con->prepare($consulta);

	// Enlazar parámetros
	$statement->bind_param("s", $user);

	// Ejecutar la consulta preparada
	$statement->execute();

	// Obtener el resultado
	$resultado = $statement->get_result();

	// Obtener los datos
	$datos = $resultado->fetch_assoc();

	if ($datos['Pasword'] == $passwd) {
		$aux = file_get_contents("entrada.html");
		$aux = str_replace("##titulo##", "Login", $aux);
		$aux = str_replace("##mensaje##", "Sesion iniciada correctamente", $aux);
		echo $aux;
	} else {
		$aux = file_get_contents("mensaje.html");
		$aux = str_replace("##titulo##", "Login", $aux);
		$aux = str_replace("##mensaje##", "Error de login", $aux);
		echo $aux;
	}
?>



