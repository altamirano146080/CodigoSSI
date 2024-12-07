<?php
    // Inicia la sesión si no está iniciada
    session_start();

    // Elimina todas las variables de sesión
    session_unset();

    // Destruye la sesión
    session_destroy();

    // Redirige al usuario a la página de inicio de sesión u otra página
    header("Location: ../ulio-html/index.html");
    exit();
?>
