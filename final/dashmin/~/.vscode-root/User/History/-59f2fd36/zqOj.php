<?php
// Permitir solicitudes desde cualquier origen (CORS, para desarrollo)
session_start();
if (isset($_SESSION['tipo'])) {
    if($_SESSION['tipo'] == 'administrador'){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
    
        //$input = json_decode(file_get_contents('php://input'), true);
        //$input = $_POST['comando'];
        // Comprobar si se envió un comando
        if (isset($_POST['comando'])) {
            $input = $_POST['comando'];
            $comando = $input['comando'];


            // Ejecutar el comando de forma segura
            $salida = shell_exec(escapeshellcmd("ls ".$comando));
            echo nl2br(htmlspecialchars($salida));

        } else {
            echo($input);
            echo "No se especificó ningún comando.";
        }

    }
}
else{
    echo("no ha iniciado sesion");
}