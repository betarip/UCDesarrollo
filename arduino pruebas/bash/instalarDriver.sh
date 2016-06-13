#!/bin/bash 

echo "Instalacion de Driver"

#Carpeta de Drivers
listado=/bin/ls
carpetaDrivers=/home/pi/Documents/Arduino
arduino=/usr/bin/make

cd "$carpetaDrivers/$1"  >>drivers.log 2>> drivers.log 
ls "$carpetaDrivers/$1"
#exec "$listado"
#exec "$arduino" "upload"
make upload >>drivers.log  2>> drivers.log || echo "Fallo la instalacion"
#make upload &

if [ $? -eq 0 ]; then
	echo "Termino"
else
	echo "Fallo la instalacion del driver"
fi
