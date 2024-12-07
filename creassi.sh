#!/bin/bash

# Colores para salida legible
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

# Variables de configuración
PHPMYADMIN_PASSWORD="sql"
ROOT_MYSQL_PASSWORD="sql"
DB_NAME="ssi"
WEB_DIR="/var/www/html/web"
ZIP_FILE="/home/user1/final.zip"
SERVICE_NAME="script.service"
SCRIPT_FILE="script.sh"

# Actualizamos el sistema
echo -e "${GREEN}Actualizando paquetes del sistema...${NC}"
sudo apt update && sudo apt upgrade -y

# Instalamos Apache2
echo -e "${GREEN}Instalando Apache2...${NC}"
sudo apt install -y apache2
sudo systemctl enable apache2
sudo systemctl start apache2
echo -e "${GREEN}Apache2 instalado y funcionando.${NC}"

# Instalamos MySQL Server (no interactivo)
echo -e "${GREEN}Instalando MySQL Server...${NC}"
sudo apt install -y gnupg wget
wget https://dev.mysql.com/get/mysql-apt-config_0.8.30-1_all.deb

# Configuramos mysql-apt-config automáticamente
echo -e "${GREEN}Configurando mysql-apt-config automáticamente...${NC}"
sudo DEBIAN_FRONTEND=noninteractive dpkg -i mysql-apt-config_0.8.30-1_all.deb <<< "mysql-8.0"

sudo apt update
sudo DEBIAN_FRONTEND=noninteractive apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql
echo -e "${GREEN}MySQL Server instalado y funcionando.${NC}"

# Configuración básica de MySQL
echo -e "${GREEN}Configurando MySQL...${NC}"

# Cambiar la contraseña de root y usar el plugin por defecto
sudo mysql --user=root <<_EOF_
-- Cambiar la contraseña y usar el plugin adecuado
ALTER USER 'root'@'localhost' IDENTIFIED WITH caching_sha2_password BY '${ROOT_MYSQL_PASSWORD}';
FLUSH PRIVILEGES;

-- Crear la base de datos 'ssi'
CREATE DATABASE IF NOT EXISTS ${DB_NAME};
FLUSH PRIVILEGES;
_EOF_

# Instalamos PHP y extensiones requeridas
echo -e "${GREEN}Instalando PHP y extensiones necesarias...${NC}"
sudo apt install -y php libapache2-mod-php php-mysql php-cli php-mbstring php-xml php-zip
echo -e "${GREEN}PHP instalado.${NC}"

# Preconfiguramos phpMyAdmin para instalación automatizada
echo -e "${GREEN}Preconfigurando phpMyAdmin...${NC}"
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password ${PHPMYADMIN_PASSWORD}" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password ${ROOT_MYSQL_PASSWORD}" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password ${PHPMYADMIN_PASSWORD}" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | sudo debconf-set-selections

# Instalamos phpMyAdmin (sin interacción)
echo -e "${GREEN}Instalando phpMyAdmin...${NC}"
sudo DEBIAN_FRONTEND=noninteractive apt install -y phpmyadmin
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin
sudo systemctl restart apache2
echo -e "${GREEN}phpMyAdmin instalado y disponible en /phpmyadmin.${NC}"

# Instalamos gcc para compilar código C
echo -e "${GREEN}Instalando gcc...${NC}"
sudo apt install -y gcc
echo -e "${GREEN}gcc instalado correctamente.${NC}"

# Limpieza de paquetes
echo -e "${GREEN}Limpiando paquetes no necesarios...${NC}"
sudo apt autoremove -y
sudo apt autoclean

# Despliegue web desde ZIP
echo -e "${GREEN}Preparando despliegue web...${NC}"
if [ -f "$ZIP_FILE" ]; then
    sudo rm -rf "$WEB_DIR"  # Eliminar cualquier contenido previo
    sudo mkdir -p "$WEB_DIR"
    sudo unzip "$ZIP_FILE" -d "$WEB_DIR"
    sudo chown -R www-data:www-data "$WEB_DIR"
    sudo chmod -R 755 "$WEB_DIR"
    echo -e "${GREEN}Página desplegada en ${WEB_DIR}.${NC}"
else
    echo -e "${RED}El archivo ${ZIP_FILE} no existe. Abortando.${NC}"
    exit 1
fi

# Configuración de script.service
echo -e "${GREEN}Configurando script.service...${NC}"
SCRIPT_PATH="/home/user1/$SCRIPT_FILE"
SERVICE_PATH="/home/user1/$SERVICE_NAME"
TARGET_SERVICE_PATH="/etc/systemd/system/$SERVICE_NAME"

if [ -f "$SCRIPT_PATH" ]; then
    sudo chmod +x "$SCRIPT_PATH"
else
    echo -e "${RED}Error: $SCRIPT_PATH no encontrado. Abortando.${NC}"
    exit 1
fi

if [ -f "$SERVICE_PATH" ]; then
    sudo cp "$SERVICE_PATH" "$TARGET_SERVICE_PATH"
else
    echo -e "${RED}Error: $SERVICE_PATH no encontrado. Abortando.${NC}"
    exit 1
fi

sudo systemctl daemon-reload
sudo systemctl enable "$SERVICE_NAME"
sudo systemctl start "$SERVICE_NAME"
echo -e "${GREEN}Servicio $SERVICE_NAME configurado y habilitado.${NC}"

# Configuración de permisos sudoers para gcc
echo "www-data ALL=(ALL) NOPASSWD: /usr/bin/gcc" | sudo tee -a /etc/sudoers > /dev/null
echo -e "${GREEN}Permiso especial para gcc configurado.${NC}"

# Reiniciamos Apache para aplicar cambios
echo -e "${GREEN}Reiniciando Apache...${NC}"
sudo systemctl restart apache2
echo -e "${GREEN}¡Despliegue completado! La página web está funcionando correctamente.${NC}"
