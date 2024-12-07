<?php
// Permitir solicitudes desde cualquier origen (CORS, para desarrollo)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Verificar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    // Comprobar si se envió un comando
    if (isset($input['comando'])) {
        $comando = $input['comando'];

        // Lista de comandos permitidos
        $comandos_permitidos = [
            'ls uploads',
            'ls defaults'
        ];

        // Validar que el comando esté permitido
        if (in_array($comando, $comandos_permitidos)) {
            // Ejecutar el comando de forma segura
            $salida = shell_exec(escapeshellcmd($comando));
            echo nl2br(htmlspecialchars($salida));
        } else {
            echo "Comando no permitido.";
        }
    } else {
        echo "No se especificó ningún comando.";
    }
} else {
    echo "Método no permitido.";
}
