#! /bin/bash
DIR_PRUEBA="/home/pi/pruebas"
DIR_DRIVER="/home/pi/Documents/Arduino/instalacion/"
cp "$DIR_PRUEBA/driver.ino" "$DIR_DRIVER"
if [ $? -eq 0 ]
then
	echo  "0"
else
        echo  "1"
fi
