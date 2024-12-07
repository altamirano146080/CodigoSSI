<?php
	//Conexion hecha
	function conexion () {
		$server = "localhost";
		$username = "root"; 
		$password = "sql"; 
		$database = "ssi"; 
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
