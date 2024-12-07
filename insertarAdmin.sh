ADMIN_NAME="admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="admin_password"

# Insertar el usuario administrador
echo -e "${GREEN}Insertando usuario administrador...${NC}"

# Insertamos el usuario administrador con el hash de la contraseña
USER_ID=$(sudo mysql --user=root --password=${ROOT_MYSQL_PASSWORD} ${DB_NAME} -se "INSERT INTO final_usuarios (nombre, gmail, contrasena) VALUES ('${ADMIN_NAME}', '${ADMIN_EMAIL}', '${ADMIN_PASSWORD_HASH}'); SELECT LAST_INSERT_ID();")

# Verificamos si la inserción fue exitosa
if [ -z "$USER_ID" ]; then
    echo -e "${RED}Error al insertar el usuario administrador.${NC}"
    exit 1
fi

# Insertamos en la tabla final_administradores usando el id obtenido
sudo mysql --user=root --password=${ROOT_MYSQL_PASSWORD} ${DB_NAME} <<_EOF_
INSERT INTO final_administradores (usuario_id)
VALUES ('${USER_ID}');
_EOF_

echo -e "${GREEN}Usuario administrador insertado correctamente en final_usuarios y final_administradores.${NC}"
