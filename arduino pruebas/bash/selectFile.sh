#!/bin/bash
path=/home/pi/Documents/Arduino
cd $path
array=($(ls -d */))
if [ $# -eq 0 ]
then
	carE=0
else
	carpeta=${array[$1]}
	carE=1
fi

if [ $carE -eq 0 ]
then
	echo "error"
else
	if [ -z "$carpeta" ]
	then
		echo "0"
	else
		cd "$carpeta"  >>drivers.log 2>> drivers.log 
		make upload >>drivers.log  2>> drivers.log
		#make upload
		if [ $? -eq 0 ]; then
		        echo -ne "0"
		else
		        echo -ne "1"
		fi
	fi
fi
