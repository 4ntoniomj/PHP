#!/bin/bash
# Fecha: lun 20 oct 2025 10:28:14 CEST
# Descripción: Iniciar docker
# Versión: 1.0

# Color palette
greenColor="\e[0;32m[1m"
endColor="\033[0m[0m"
redColor="\e[0;31m[1m"
blueColor="\e[0;34m[1m"
yellowColor="\e[0;33m[1m"
purpleColor="\e[0;35m[1m"
turquoiseColor="\e[0;36m[1m"
grayColor="\e[0;37m[1m"

docker compose up -d

echo -e "Para entrar a la base de datos ejecute ${yellowColor}docker exec -it bsd mysql -u root -p${endColor}\nLa contraseña por defecto es ${redColor}123456789${endColor}"

sudo chown $USER:$USER -R data
