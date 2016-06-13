#!/bin/bash 


#Carpeta de Drivers
carpetaDrivers=/home/pi/Documents/Arduino
cd "$carpetaDrivers/$1"  >>drivers.log 2>> drivers.log 
make upload >>drivers.log  2>> drivers.log
if [ $? -eq 0 ]; then
	echo -ne "0"
else
	echo -ne "1"
fi
