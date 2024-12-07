<?php

	session_start();

	require_once("basededatos.php");

	function comprobarusuario() {
		$logueado = false;
		$con = conexion();

		if (isset($_SESSION['user'])) {
			$user = $_SESSION['user'];	
		} else {
			return false;
		}

		if (isset($_SESSION['passwd'])) {
			$passwd = $_SESSION['passwd'];	
		} else {
			return false;
		}

		if (isset($_SESSION['tiempo'])) {
			$tiempo1 = $_SESSION['tiempo'];	
		} else {
			return false;
		}

		$tiempo2 = time();
		$diferencia = $tiempo2 - $tiempo1;

		if ($diferencia > 300) {
			//Se ha acabado el tiempo
			return $logueado;
		} else {
			$consulta = "select * from usuarios where Gmail = '$user'";
			$resultado = $con->query($consulta);
			$datos = $resultado->fetch_assoc();
			if ($datos['Pasword'] == $passwd) {
				return true;
			}
		}

		return $logueado;
	}


?>


