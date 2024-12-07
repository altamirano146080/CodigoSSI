#!/bin/bash

# Colores para salida legible
GREEN='\033[0;32m'
NC='\033[0m'

# Variables de configuración
PHPMYADMIN_PASSWORD="sql"
ROOT_MYSQL_PASSWORD="sql"
DB_NAME="ssi"
WEB_DIR="/var/www/html/web"
ZIP_FILE="/home/final.zip"
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
sudo DEBIAN_FRONTEND=noninteractive dpkg-reconfigure mysql-apt-config <<< "1" &>/dev/null
sudo dpkg -i mysql-apt-config_0.8.30-1_all.deb
sudo apt update
sudo DEBIAN_FRONTEND=noninteractive apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql
echo -e "${GREEN}MySQL Server instalado y funcionando.${NC}"

# Configuración básica de MySQL
echo -e "${GREEN}Configurando MySQL...${NC}"
sudo mysql --user=root <<_EOF_
ALTER USER 'root'@'localhost' IDENTIFIED WITH 'mysql_native_password' BY '${ROOT_MYSQL_PASSWORD}';
FLUSH PRIVILEGES;
CREATE DATABASE IF NOT EXISTS ${DB_NAME};
_EOF_

# Instalamos PHP 8.2 y extensiones necesarias
echo -e "${GREEN}Instalando PHP 8.2 y extensiones...${NC}"
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-mysqli libapache2-mod-php8.2 gcc gcc-multilib
echo -e "${GREEN}PHP 8.2 y herramientas de compilación instaladas correctamente.${NC}"

# Instalamos phpMyAdmin
echo -e "${GREEN}Instalando phpMyAdmin...${NC}"
sudo apt install -y phpmyadmin
echo -e "${GREEN}phpMyAdmin instalado correctamente.${NC}"

# Configuramos Apache para phpMyAdmin
echo -e "${GREEN}Configurando Apache para phpMyAdmin...${NC}"
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin
echo -e "${GREEN}phpMyAdmin configurado y habilitado en Apache.${NC}"

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
SCRIPT_PATH="$WEB_DIR/ARCHIVOS/$SCRIPT_FILE"
SERVICE_PATH="$WEB_DIR/ARCHIVOS/$SERVICE_NAME"
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
