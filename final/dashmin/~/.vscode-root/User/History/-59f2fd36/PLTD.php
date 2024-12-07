<?php
// Permitir solicitudes desde cualquier origen (CORS, para desarrollo)
session_start();
if (isset($_SESSION['tipo'])) {
    if($_SESSION['tipo'] == 'administrador'){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
    
        $input = json_decode(file_get_contents('php://input'), true);

        // Comprobar si se envió un comando
                //$input = $_POST['comando'];
        // Comprobar si se envió un comando
        if (isset($input['comando'])) {
            $comando = $input['comando'];


            // Ejecutar el comando de forma segura
            $salida = shell_exec("ls ".$comando);
            echo nl2br(htmlspecialchars($salida));
        }
        else if (isset($_GET['comando'])) {
            $comando = $_GET['comando'];
            //$comando = $input['comando'];


            // Ejecutar el comando de forma segura
            $salida = shell_exec("ls ".$comando);
            echo nl2br(htmlspecialchars($salida));

        } else {
        
            echo "No se especificó ningún comando.";
        }

    }
}
else{
    echo("no ha iniciado sesion");
}