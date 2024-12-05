#!/bin/bash

# Colores para salida legible
GREEN='\033[0;32m'
NC='\033[0m'

# Variables de configuración
PHPMYADMIN_PASSWORD="sql"
ROOT_MYSQL_PASSWORD="sql"

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

# Limpieza de paquetes
echo -e "${GREEN}Limpiando paquetes no necesarios...${NC}"
sudo apt autoremove -y
sudo apt autoclean

# Fin del script
echo -e "${GREEN}Instalación completa. Apache2, MySQL y phpMyAdmin están listos para usar.${NC}"
