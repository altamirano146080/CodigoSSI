<?php

	function conexion () {
		$servidor = "localhost";
		$user = "root"; 
		$password = "sql"; 
		$bd = "ssi"; 
		$con = mysqli_connect($servidor, $user, $password, $bd);

		if (!$con) {
			echo "Error de conexión de base de datos <br>";
			echo "Error número: " . mysqli_connect_errno();
			echo "Texto error: " . mysqli_connect_error();
			exit;
		}

		return $con;
	}

?>
