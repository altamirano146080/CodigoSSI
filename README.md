Este proyecto ha acabado con mi salud mental
descargar zip y 
poner los archivos en home de user1
abrir una terminal
HACER:
chmod +x setup_OVA.sh
./setup_OVA.sh

chmod +x automatizado.sh
./automatizado.sh


### WriteUp para la solución del sistema vulnerable y explotación de RCE

#### Descripción general del sistema

Este trabajo consiste en la creación de un sistema web vulnerable basado en PHP, con un fallo de seguridad que permite ejecutar código remoto (RCE) en el servidor web, en este caso bajo el usuario `www-data`, que es el mismo usuario que ejecuta el servicio Apache. Este sistema tiene una serie de fallos concatenados que permiten la explotación del sistema para obtener una shell con privilegios elevados. Además, el sistema está configurado para almacenar dos flags: una en `/home/www-data/user.txt` (flag de usuario) y otra en `/root/root.txt` (flag de root).

#### Objetivos del trabajo

1. **Vulnerabilidad Web (RCE)**:
   Se crea un sistema web vulnerable con PHP, donde se permite la ejecución de código malicioso a través de la concatenación de varios fallos de seguridad, como un fallo en el manejo de entradas no sanitizadas o inyecciones de comandos. 
   
2. **Elevación de Privilegios**:
   Se ha utilizado para la elevación de privilegios:
   - **gtfobins**: Se proporciona una configuración que explota una vulnerabilidad de una herramienta estándar (por ejemplo, un binario como `python`, `gcc`, etc.) para elevar los privilegios de `www-data` a root.

3. **Generación y almacenamiento de flags**:
   Las flags se generan aleatoriamente y se almacenan en los archivos `/home/www-data/user.txt` y `/root/root.txt`. Estos archivos contienen las flags que se deben capturar una vez que se exploten las vulnerabilidades.

#### Paso 1: Instalación del sistema vulnerable

El sistema está configurado para que se ejecute automáticamente en el arranque. Se utilizan los siguientes componentes:

1. **Apache2 y PHP**: El servidor Apache se instala y configura para que sirva el sistema web. El script `putFlags.sh` configura Apache para que se ejecute en el usuario `www-data` y genera las flags de usuario y root.

2. **Vulnerabilidades en PHP**:
   El sistema web se encuentra vulnerable a inyecciones SQL y ejecución de comandos. El primer paso consiste en acceder a la URL que contiene un fallo de inyección SQL para extraer las credenciales de los usuarios del sistema.

#### Paso 2: Explotación de la vulnerabilidad (RCE)

1. **Explotación de Inyección SQL**:
2. 
   En el script `automatizado.sh`, se realiza una solicitud HTTP con una inyección SQL en el parámetro `rover_id`:
   ```bash
   fetch_url="http://localhost/web/final/ulio-html/listadoFotosRovers.php?rover_id=%27e%27%20UNION%20SELECT%20fa.contrasena,%20fa.gmail,%201,%202,%203%20FROM%20final_usuarios%20fa,%20final_administradores%20f%20WHERE%20f.usuario_id%20=%20fa.id"
   ```
   La inyección SQL utiliza `UNION SELECT` para combinar las tablas `final_usuarios` y `final_administradores` y extraer las contraseñas (`fa.contrasena`) y los correos electrónicos (`fa.gmail`) de los usuarios. Esta información se extrae utilizando `jq` para manejar la respuesta JSON y extraer los valores de las credenciales.

   En este escenario, la necesidad de obtener las credenciales de administrador se debe a que, aunque el sistema permite que los usuarios normales se registren y accedan a la página, solo los administradores tienen privilegios especiales, como la capacidad de subir archivos.

4. **Login de administrador**:
   Los datos obtenidos de la inyección se usan para realizar un login automático con `curl`, enviando las credenciales extraídas del ataque:
   ```bash
   curl -X POST \
        -d "email=$rover_id" \
        -d "contrasena=$photo_id" \
        -d "es_administrador=on" \
        -c cookies.txt \
        "$login_url"
   ```
   Esto permite al atacante obtener acceso como administrador en la web.

5. **Subida de archivo malicioso**:
   Una vez autenticado, el atacante sube un archivo malicioso (`gdb.sh`) al servidor mediante una solicitud POST:
   ```bash
   curl -X POST -F "profile_picture=@gdb.sh" http://localhost/web/final/dashmin/upload.php
   ```
   Este archivo contiene un script que intentará ejecutar comandos en el servidor, probablemente con la intención de obtener una shell.

6. **Ejecución de comando**:
   El script luego realiza una llamada a la URL que ejecuta comandos en el servidor (`files_url_root`) para ejecutar el archivo malicioso subido. El comando intenta cambiar permisos del archivo `gdb.sh` para hacerlo ejecutable y luego lo ejecuta:
   ```bash
   files_url_root="http://localhost/web/final/dashmin/list_uploads.php?comando=ls%20uploads%3B%20cd%20uploads;chmod%20744%20gdb.sh;./gdb.sh"
   ```

#### Paso 3: Elevación de privilegios

1. **Elevación usando gtfobins**:
   Alternativamente, se puede usar una herramienta como `gtfobins` para explotar una vulnerabilidad en un binario del sistema que permita a `www-data` ejecutar comandos como root. Esto depende de la configuración del sistema y de los permisos del binario.

#### Paso 4: Captura de las flags

Una vez que se obtiene acceso con privilegios elevados (por una vulnerabilidad gtfobins), el atacante puede leer las flags desde los archivos `/home/www-data/user.txt` y `/root/root.txt`. Esto se realiza mediante la ejecución de comandos como `cat` o cualquier otro método para acceder a esos archivos.

- **Flag de usuario**: Se encuentra en `/home/www-data/user.txt`.
- **Flag de root**: Se encuentra en `/root/root.txt`.

#### Paso 5: Solución a la vulnerabilidad

Para solucionar las vulnerabilidades, se deben tomar varias medidas, como:

1. **Sanitización de entradas**: El sistema debe validar y sanitizar todas las entradas de los usuarios para prevenir inyecciones SQL y de comandos.
2. **Configuración de permisos**: Se deben revisar y ajustar los permisos de los binarios críticos y de los archivos, para que no sea posible la ejecución de comandos arbitrarios por parte del usuario `www-data`.
3. **Desactivar funcionalidades inseguras**: Desactivar funciones de PHP o cualquier otra funcionalidad que permita la ejecución de código arbitrario.

#### Conclusión

Este trabajo muestra cómo un sistema web vulnerable puede ser explotado utilizando inyecciones de SQL, subida de archivos maliciosos y técnicas de escalada de privilegios, como el uso de vulnerabilidades de buffer overflow o configuraciones inseguras de binarios. La explotación de estas vulnerabilidades permite obtener acceso completo al sistema, incluyendo la obtención de las flags de usuario y root.

Este tipo de ejercicios es clave para aprender sobre la seguridad informática, ya que permite identificar y remediar vulnerabilidades comunes en aplicaciones web y sistemas.

